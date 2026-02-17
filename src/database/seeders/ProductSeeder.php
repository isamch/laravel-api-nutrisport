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
            ['name' => 'Whey Protein Isolate', 'description' => 'High quality whey protein isolate for muscle building', 'stock' => 150],
            ['name' => 'Creatine Monohydrate', 'description' => 'Pure creatine monohydrate for strength and performance', 'stock' => 200],
            ['name' => 'BCAA Complex', 'description' => 'Branched-chain amino acids for recovery', 'stock' => 180],
            ['name' => 'Pre-Workout Energy', 'description' => 'Energy boost for intense workouts', 'stock' => 120],
            ['name' => 'Multivitamin Complex', 'description' => 'Complete daily vitamin and mineral supplement', 'stock' => 250],
            ['name' => 'Omega-3 Fish Oil', 'description' => 'Essential fatty acids for health', 'stock' => 300],
            ['name' => 'Casein Protein', 'description' => 'Slow-release protein for overnight recovery', 'stock' => 100],
            ['name' => 'Glutamine Powder', 'description' => 'Supports muscle recovery and immune system', 'stock' => 160],
            ['name' => 'Mass Gainer', 'description' => 'High-calorie formula for weight gain', 'stock' => 90],
            ['name' => 'Fat Burner', 'description' => 'Thermogenic formula for weight loss', 'stock' => 140],
        ];

        foreach ($products as $index => $productData) {
            $product = \App\Models\Product::create($productData);

            // Add prices for each site (varying prices)
            foreach ($sites as $siteIndex => $siteId) {
                \App\Models\ProductPrice::create([
                    'product_id' => $product->id,
                    'site_id' => $siteId,
                    'price' => rand(20, 80) + ($siteIndex * 5),
                ]);
            }

            // Attach random categories
            $randomCategories = array_rand(array_flip($categories), min(2, count($categories)));
            $product->categories()->attach(is_array($randomCategories) ? $randomCategories : [$randomCategories]);
        }
    }
}
