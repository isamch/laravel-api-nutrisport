<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'administrateur')->value('id');

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@nutrisport.com',
            'phone' => '+33123456789',
            'password' => Hash::make('password'),
            'role_id' => $adminRoleId,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
