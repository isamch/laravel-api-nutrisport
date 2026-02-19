<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Product;

class CartService
{
    private const TTL = 259200; // 3 days

    private function getKey($userId)
    {
        return "cart:user:{$userId}";
    }

    public function add($productId, $quantity, $siteCode, $userId)
    {
        $product = Product::with(['prices' => fn($q) => $q->whereHas('site', fn($q) => $q->where('country_code', $siteCode)), 'images'])
            ->findOrFail($productId);

        $price = $product->prices->first()?->price;
        if (!$price) {
            throw new \Exception("Product not available for site {$siteCode}");
        }

        // Check stock availability
        if ($product->stock <= 0) {
            throw new \Exception("Product is out of stock");
        }

        $key = $this->getKey($userId);
        $cart = $this->get($userId);

        $currentQuantity = $cart[$productId]['quantity'] ?? 0;
        $newQuantity = $currentQuantity + $quantity;

        // Check if requested quantity exceeds stock
        if ($newQuantity > $product->stock) {
            throw new \Exception("Requested quantity exceeds available stock. Available: {$product->stock}");
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $price,
                'quantity' => $quantity,
                'stock' => $product->stock,
                'image' => $product->images->first() ? url(\Storage::url($product->images->first()->url)) : null,
                'site' => $siteCode
            ];
        }

        Redis::setex($key, self::TTL, json_encode($cart));
        return $cart;
    }

    public function get($userId)
    {
        $key = $this->getKey($userId);
        $data = Redis::get($key);
        return $data ? json_decode($data, true) : [];
    }

    public function update($productId, $quantity, $userId)
    {
        $key = $this->getKey($userId);
        $cart = $this->get($userId);

        if (!isset($cart[$productId])) {
            throw new \Exception("Product not found in cart");
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            // Check stock when updating
            $product = Product::findOrFail($productId);
            if ($quantity > $product->stock) {
                throw new \Exception("Requested quantity exceeds available stock. Available: {$product->stock}");
            }
            $cart[$productId]['quantity'] = $quantity;
        }

        Redis::setex($key, self::TTL, json_encode($cart));
        return $cart;
    }

    public function remove($productId, $userId)
    {
        $key = $this->getKey($userId);
        $cart = $this->get($userId);

        unset($cart[$productId]);

        Redis::setex($key, self::TTL, json_encode($cart));
        return $cart;
    }

    public function clear($userId)
    {
        $key = $this->getKey($userId);
        Redis::del($key);
        return [];
    }

    public function getTotal($userId)
    {
        $cart = $this->get($userId);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }
}
