<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'stock' => $data['stock'] ?? 0,
                'created_by' => auth('api')->id(),
            ]);

            // Add prices for each site
            foreach ($data['prices'] as $siteId => $price) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'site_id' => $siteId,
                    'price' => $price,
                ]);
            }

            // Attach categories
            if (!empty($data['categories'])) {
                $product->categories()->attach($data['categories']);
            }

            return $product->load(['prices', 'categories']);
        });
    }

    public function update(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            $updateData = [
                'name' => $data['name'],
                'description' => $data['description'],
            ];

            if (isset($data['stock'])) {
                $updateData['stock'] = $data['stock'];
            }

            $product->update($updateData);

            // Update prices
            if (isset($data['prices'])) {
                foreach ($data['prices'] as $siteId => $price) {
                    ProductPrice::updateOrCreate(
                        ['product_id' => $product->id, 'site_id' => $siteId],
                        ['price' => $price]
                    );
                }
            }

            // Update categories
            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            return $product->load(['prices', 'categories']);
        });
    }

    public function getAll(array $filters = [])
    {
        $query = Product::with(['prices', 'categories']);

        // Filter by creator (vendeur)
        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        // Filter by site (country code: FR, IT, BE)
        if (!empty($filters['site'])) {
            $siteId = \Cache::remember("site_{$filters['site']}", 86400, function () use ($filters) {
                return \DB::table('sites')->where('country_code', strtoupper($filters['site']))->value('id');
            });

            if ($siteId) {
                $query->whereHas('prices', fn($q) => $q->where('site_id', $siteId));
                $filters['site_id'] = $siteId;
            }
        }

        // Filter by category (slug or id)
        if (!empty($filters['category'])) {
            $query->whereHas('categories', function($q) use ($filters) {
                if (is_numeric($filters['category'])) {
                    $q->where('categories.id', $filters['category']);
                } else {
                    $q->where('categories.slug', $filters['category']);
                }
            });
        }

        // Filter by stock availability
        if (isset($filters['in_stock'])) {
            $inStock = filter_var($filters['in_stock'], FILTER_VALIDATE_BOOLEAN);

            if ($inStock) {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '=', 0);
            }
        }

        $perPage = min((int)($filters['per_page'] ?? 15), 100);
        return $query->paginate($perPage);
    }

    public function getById($id, $site = null)
    {
        $query = Product::with(['prices', 'categories', 'images']);

        if ($site) {
            $siteId = \Cache::remember("site_{$site}", 86400, function () use ($site) {
                return \DB::table('sites')->where('country_code', strtoupper($site))->value('id');
            });

            if ($siteId) {
                $query->with(['prices' => fn($q) => $q->where('site_id', $siteId)]);
            }
        }

        return $query->findOrFail($id);
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}
