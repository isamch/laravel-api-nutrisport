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
            ['name' => 'admin', 'description' => 'Administrator with full access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'customer', 'description' => 'Regular customer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'agent', 'description' => 'Sales agent', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
