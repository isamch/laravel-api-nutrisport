<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [];
        
        for ($productId = 1; $productId <= 10; $productId++) {
            $images[] = [
                'product_id' => $productId,
                'url' => "products/{$productId}/product.avif",
                'alt_text' => null,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('product_images')->insert($images);
    }
}
