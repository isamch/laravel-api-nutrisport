<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientRoleId = DB::table('roles')->where('name', 'client')->value('id');

        $clients = [
            [
                'name' => 'Ahmed Benali',
                'email' => 'ahmed.benali@example.com',
                'phone' => '+33612345601'
            ],
            [
                'name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@example.com',
                'phone' => '+33612345602'
            ],
            [
                'name' => 'Karim Alami',
                'email' => 'karim.alami@example.com',
                'phone' => '+33612345603'
            ],
            [
                'name' => 'Nadia Idrissi',
                'email' => 'nadia.idrissi@example.com',
                'phone' => '+33612345604'
            ],
            [
                'name' => 'Youssef Tazi',
                'email' => 'youssef.tazi@example.com',
                'phone' => '+33612345605'
            ],
        ];

        foreach ($clients as $client) {
            DB::table('users')->insert([
                'name' => $client['name'],
                'email' => $client['email'],
                'phone' => $client['phone'],
                'password' => Hash::make('password'),
                'role_id' => $clientRoleId,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
