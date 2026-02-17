<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendeurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendeurRoleId = DB::table('roles')->where('name', 'vendeur')->value('id');

        $vendeurs = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@nutrisport.com',
                'phone' => '+33123456701'
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@nutrisport.com',
                'phone' => '+33123456702'
            ],
            [
                'name' => 'Pierre Bernard',
                'email' => 'pierre.bernard@nutrisport.com',
                'phone' => '+33123456703'
            ],
            [
                'name' => 'Sophie Dubois',
                'email' => 'sophie.dubois@nutrisport.com',
                'phone' => '+33123456704'
            ],
            [
                'name' => 'Luc Moreau',
                'email' => 'luc.moreau@nutrisport.com',
                'phone' => '+33123456705'
            ],
        ];

        foreach ($vendeurs as $vendeur) {
            DB::table('users')->insert([
                'name' => $vendeur['name'],
                'email' => $vendeur['email'],
                'phone' => $vendeur['phone'],
                'password' => Hash::make('password'),
                'role_id' => $vendeurRoleId,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
