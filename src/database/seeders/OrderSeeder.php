<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create addresses for clients first
        $addresses = [
            ['user_id' => 7, 'street' => '15 Rue de la Paix', 'city' => 'Paris', 'postal_code' => '75002', 'country' => 'France'],
            ['user_id' => 8, 'street' => '28 Avenue des Champs', 'city' => 'Lyon', 'postal_code' => '69001', 'country' => 'France'],
            ['user_id' => 9, 'street' => '42 Boulevard Victor Hugo', 'city' => 'Marseille', 'postal_code' => '13001', 'country' => 'France'],
            ['user_id' => 10, 'street' => '7 Rue du Commerce', 'city' => 'Nice', 'postal_code' => '06000', 'country' => 'France'],
            ['user_id' => 11, 'street' => '33 Avenue de la République', 'city' => 'Toulouse', 'postal_code' => '31000', 'country' => 'France'],
        ];

        foreach ($addresses as $address) {
            DB::table('addresses')->insert([
                'user_id' => $address['user_id'],
                'street' => $address['street'],
                'city' => $address['city'],
                'postal_code' => $address['postal_code'],
                'country' => $address['country'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get site_id for France
        $siteId = DB::table('sites')->where('country_code', 'FR')->value('id');

        // Create orders
        $orders = [
            [
                'user_id' => 7,
                'address_id' => 1,
                'status' => 'completed',
                'payment_status' => 'paid',
                'total' => 89.97,
                'tracking_number' => 'TRK' . rand(100000, 999999),
                'items' => [
                    ['product_id' => 1, 'quantity' => 2, 'price' => 29.99],
                    ['product_id' => 2, 'quantity' => 1, 'price' => 19.99],
                    ['product_id' => 4, 'quantity' => 1, 'price' => 10.00],
                ]
            ],
            [
                'user_id' => 8,
                'address_id' => 2,
                'status' => 'processing',
                'payment_status' => 'paid',
                'total' => 124.95,
                'tracking_number' => 'TRK' . rand(100000, 999999),
                'items' => [
                    ['product_id' => 5, 'quantity' => 3, 'price' => 24.99],
                    ['product_id' => 6, 'quantity' => 2, 'price' => 24.99],
                ]
            ],
            [
                'user_id' => 9,
                'address_id' => 3,
                'status' => 'pending',
                'payment_status' => 'pending',
                'total' => 159.96,
                'tracking_number' => null,
                'items' => [
                    ['product_id' => 9, 'quantity' => 2, 'price' => 49.99],
                    ['product_id' => 3, 'quantity' => 2, 'price' => 29.99],
                ]
            ],
            [
                'user_id' => 10,
                'address_id' => 4,
                'status' => 'completed',
                'payment_status' => 'paid',
                'total' => 69.98,
                'tracking_number' => 'TRK' . rand(100000, 999999),
                'items' => [
                    ['product_id' => 7, 'quantity' => 1, 'price' => 34.99],
                    ['product_id' => 8, 'quantity' => 1, 'price' => 34.99],
                ]
            ],
            [
                'user_id' => 11,
                'address_id' => 5,
                'status' => 'cancelled',
                'payment_status' => 'failed',
                'total' => 44.99,
                'tracking_number' => null,
                'items' => [
                    ['product_id' => 10, 'quantity' => 1, 'price' => 44.99],
                ]
            ],
        ];

        foreach ($orders as $orderData) {
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $orderData['user_id'],
                'site_id' => $siteId,
                'address_id' => $orderData['address_id'],
                'status' => $orderData['status'],
                'payment_method' => 'bank_transfer',
                'payment_status' => $orderData['payment_status'],
                'total' => $orderData['total'],
                'remaining_amount' => $orderData['payment_status'] === 'paid' ? 0 : $orderData['total'],
                'tracking_number' => $orderData['tracking_number'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            foreach ($orderData['items'] as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
