<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'administrateur',
                'description' => 'Administrateur avec accès complet',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'vendeur',
                'description' => 'Vendeur de produits',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'client',
                'description' => 'Client régulier',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
