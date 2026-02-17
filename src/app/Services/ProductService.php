<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
            ]);

            // Add prices for each site
            foreach ($data['prices'] as $siteId => $price) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'site_id' => $siteId,
                    'price' => $price,
                ]);
            }

            // Add stock for each site
            foreach ($data['stock'] as $siteId => $quantity) {
                ProductStock::create([
                    'product_id' => $product->id,
                    'site_id' => $siteId,
                    'quantity' => $quantity,
                ]);
            }

            // Attach categories
            if (!empty($data['categories'])) {
                $product->categories()->attach($data['categories']);
            }

            return $product->load(['prices', 'stock', 'categories']);
        });
    }

    public function update(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'name' => $data['name'],
                'description' => $data['description'],
            ]);

            // Update prices
            if (isset($data['prices'])) {
                foreach ($data['prices'] as $siteId => $price) {
                    ProductPrice::updateOrCreate(
                        ['product_id' => $product->id, 'site_id' => $siteId],
                        ['price' => $price]
                    );
                }
            }

            // Update stock
            if (isset($data['stock'])) {
                foreach ($data['stock'] as $siteId => $quantity) {
                    ProductStock::updateOrCreate(
                        ['product_id' => $product->id, 'site_id' => $siteId],
                        ['quantity' => $quantity]
                    );
                }
            }

            // Update categories
            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            return $product->load(['prices', 'stock', 'categories']);
        });
    }

    public function getAll(array $filters = [])
    {
        $query = Product::with(['prices', 'stock', 'categories']);

        if (!empty($filters['site_id'])) {
            $query->whereHas('prices', fn($q) => $q->where('site_id', $filters['site_id']));
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $filters['category_id']));
        }

        if (!empty($filters['in_stock'])) {
            $query->whereHas('stock', fn($q) => $q->where('quantity', '>', 0));
        }

        return $query->paginate(15);
    }

    public function getById($id, $siteId = null)
    {
        $query = Product::with(['prices', 'stock', 'categories', 'images']);

        if ($siteId) {
            $query->with(['prices' => fn($q) => $q->where('site_id', $siteId)])
                  ->with(['stock' => fn($q) => $q->where('site_id', $siteId)]);
        }

        return $query->findOrFail($id);
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}
