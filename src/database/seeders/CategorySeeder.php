<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main categories
        $supplementsId = DB::table('categories')->insertGetId([
            'name' => 'Supplements',
            'slug' => 'supplements',
            'description' => 'Sports supplements',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $equipmentId = DB::table('categories')->insertGetId([
            'name' => 'Equipment',
            'slug' => 'equipment',
            'description' => 'Sports equipment',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub-categories
        DB::table('categories')->insert([
            ['name' => 'Proteins', 'slug' => 'proteins', 'description' => 'Protein supplements', 'parent_id' => $supplementsId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vitamins', 'slug' => 'vitamins', 'description' => 'Vitamin supplements', 'parent_id' => $supplementsId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pre-Workout', 'slug' => 'pre-workout', 'description' => 'Pre-workout supplements', 'parent_id' => $supplementsId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shakers', 'slug' => 'shakers', 'description' => 'Protein shakers', 'parent_id' => $equipmentId, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
