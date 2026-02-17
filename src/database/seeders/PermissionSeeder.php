<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Products
            [
                'name' => 'create_product',
                'description' => 'Créer un produit'
            ],
            [
                'name' => 'view_product',
                'description' => 'Voir les produits'
            ],
            [
                'name' => 'update_product',
                'description' => 'Modifier un produit'
            ],
            [
                'name' => 'delete_product',
                'description' => 'Supprimer un produit'
            ],

            // Orders
            [
                'name' => 'view_all_orders',
                'description' => 'Voir toutes les commandes'
            ],
            [
                'name' => 'view_own_orders',
                'description' => 'Voir ses propres commandes'
            ],
            [
                'name' => 'update_order_status',
                'description' => 'Modifier le statut de commande'
            ],

            // Users
            [
                'name' => 'create_vendeur',
                'description' => 'Créer un vendeur'
            ],
            [
                'name' => 'view_users',
                'description' => 'Voir les utilisateurs'
            ],
            [
                'name' => 'manage_users',
                'description' => 'Gérer les utilisateurs'
            ],

            // Reports
            [
                'name' => 'view_reports',
                'description' => 'Voir les rapports'
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign all permissions to administrateur role
        $adminRoleId = DB::table('roles')->where('name', 'administrateur')->value('id');
        $permissionIds = DB::table('permissions')->pluck('id');

        foreach ($permissionIds as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign limited permissions to vendeur role
        $vendeurRoleId = DB::table('roles')->where('name', 'vendeur')->value('id');
        $vendeurPermissions = DB::table('permissions')
            ->whereIn('name', ['view_product', 'view_own_orders'])
            ->pluck('id');

        foreach ($vendeurPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => $vendeurRoleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
