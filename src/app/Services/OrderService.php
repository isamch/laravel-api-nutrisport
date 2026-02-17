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
        $siteId = \App\Models\Site::where('code', $siteCode)->value('id');

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
            }

            $this->cartService->clear($userId);

            return $order->load(['items.product', 'address']);
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

        return $query->findOrFail($id);
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
