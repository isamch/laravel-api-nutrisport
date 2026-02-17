<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $sites = \DB::table('sites')->pluck('id')->toArray();
        $categories = \DB::table('categories')->pluck('id')->toArray();

        $products = [
            ['name' => 'Whey Protein Isolate', 'description' => 'High quality whey protein isolate for muscle building'],
            ['name' => 'Creatine Monohydrate', 'description' => 'Pure creatine monohydrate for strength and performance'],
            ['name' => 'BCAA Complex', 'description' => 'Branched-chain amino acids for recovery'],
            ['name' => 'Pre-Workout Energy', 'description' => 'Energy boost for intense workouts'],
            ['name' => 'Multivitamin Complex', 'description' => 'Complete daily vitamin and mineral supplement'],
            ['name' => 'Omega-3 Fish Oil', 'description' => 'Essential fatty acids for health'],
            ['name' => 'Casein Protein', 'description' => 'Slow-release protein for overnight recovery'],
            ['name' => 'Glutamine Powder', 'description' => 'Supports muscle recovery and immune system'],
            ['name' => 'Mass Gainer', 'description' => 'High-calorie formula for weight gain'],
            ['name' => 'Fat Burner', 'description' => 'Thermogenic formula for weight loss'],
        ];

        foreach ($products as $index => $productData) {
            $product = \App\Models\Product::create($productData);

            // Add prices for each site (varying prices)
            foreach ($sites as $siteIndex => $siteId) {
                \App\Models\ProductPrice::create([
                    'product_id' => $product->id,
                    'site_id' => $siteId,
                    'price' => rand(20, 80) + ($siteIndex * 5), // Different prices per site
                ]);
            }

            // Add stock for each site
            foreach ($sites as $siteId) {
                \App\Models\ProductStock::create([
                    'product_id' => $product->id,
                    'site_id' => $siteId,
                    'quantity' => rand(50, 200),
                ]);
            }

            // Attach random categories (ensure unique)
            $randomCategories = array_rand(array_flip($categories), min(2, count($categories)));
            $product->categories()->attach(is_array($randomCategories) ? $randomCategories : [$randomCategories]);
        }
    }
}
