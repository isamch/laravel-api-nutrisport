<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private CartService $cartService) {}

    public function createFromCart($userId, $siteCode, $addressId, $paymentMethod)
    {
        $cart = $this->cartService->get($userId);

        if (empty($cart)) {
            throw new \Exception('Cart is empty');
        }

        $total = $this->cartService->getTotal($userId);
        $siteId = \App\Models\Site::where('country_code', $siteCode)->value('id');

        return DB::transaction(function () use ($userId, $siteId, $addressId, $paymentMethod, $total, $cart) {
            $order = Order::create([
                'user_id' => $userId,
                'site_id' => $siteId,
                'address_id' => $addressId,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'total' => $total,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);

                // Decrease product stock
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $this->cartService->clear($userId);

            $order->load(['items.product', 'address', 'user']);

            // Send emails
            \Mail::to($order->user->email)->send(new \App\Mail\OrderCreated($order));

            $adminEmail = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'administrateur'))
                ->first()?->email;
            if ($adminEmail) {
                \Mail::to($adminEmail)->send(new \App\Mail\OrderCreated($order));
            }

            return $order;
        });
    }

    public function getUserOrders($userId, $filters = [])
    {
        $query = Order::with(['items.product', 'address'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(15);
    }

    public function getById($id, $userId = null)
    {
        $query = Order::with(['items.product', 'address', 'user']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $order = $query->find($id);

        if (!$order) {
            throw new \Exception($userId ? 'Order not found or you do not have permission to view it' : 'Order not found');
        }

        return $order;
    }

    public function getRecentOrders($days = 5, $filters = [])
    {
        $query = Order::with(['items.product', 'user', 'address'])
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc');

        if (!empty($filters['site_id'])) {
            $query->where('site_id', $filters['site_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(20);
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);

        return $order->load(['items.product', 'user']);
    }
}
