<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'site_id' => $data['site_id'],
                'address_id' => $data['address_id'],
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'total' => $data['total'],
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);
            }

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
