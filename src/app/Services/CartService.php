<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Product;

class CartService
{
    private const TTL = 259200; // 3 days

    private function getKey($userId = null, $sessionId = null)
    {
        return $userId ? "cart:user:{$userId}" : "cart:guest:{$sessionId}";
    }

    public function add($productId, $quantity, $siteCode, $userId = null, $sessionId = null)
    {
        $product = Product::with(['prices' => fn($q) => $q->whereHas('site', fn($q) => $q->where('code', $siteCode))])
            ->findOrFail($productId);

        $price = $product->prices->first()?->price;
        if (!$price) {
            throw new \Exception("Product not available for site {$siteCode}");
        }

        $key = $this->getKey($userId, $sessionId);
        $cart = $this->get($userId, $sessionId);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'site' => $siteCode
            ];
        }

        Redis::setex($key, self::TTL, json_encode($cart));
        return $cart;
    }

    public function get($userId = null, $sessionId = null)
    {
        $key = $this->getKey($userId, $sessionId);
        $data = Redis::get($key);
        return $data ? json_decode($data, true) : [];
    }

    public function update($productId, $quantity, $userId = null, $sessionId = null)
    {
        $key = $this->getKey($userId, $sessionId);
        $cart = $this->get($userId, $sessionId);

        if (!isset($cart[$productId])) {
            throw new \Exception("Product not found in cart");
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $quantity;
        }

        Redis::setex($key, self::TTL, json_encode($cart));
        return $cart;
    }

    public function remove($productId, $userId = null, $sessionId = null)
    {
        $key = $this->getKey($userId, $sessionId);
        $cart = $this->get($userId, $sessionId);

        unset($cart[$productId]);

        Redis::setex($key, self::TTL, json_encode($cart));
        return $cart;
    }

    public function clear($userId = null, $sessionId = null)
    {
        $key = $this->getKey($userId, $sessionId);
        Redis::del($key);
        return [];
    }

    public function getTotal($userId = null, $sessionId = null)
    {
        $cart = $this->get($userId, $sessionId);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }
}
