<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sites')->insert([
            ['name' => 'NutriSport France', 'country_code' => 'FR', 'currency' => 'EUR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NutriSport Italia', 'country_code' => 'IT', 'currency' => 'EUR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NutriSport Belgique', 'country_code' => 'BE', 'currency' => 'EUR', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
