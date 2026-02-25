<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Site;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $site = Site::create(['name' => 'nutri-sport.fr', 'domain' => 'nutri-sport.fr', 'country' => 'FR']);
        
        $this->product = Product::create([
            'name' => 'Whey Protein',
            'description' => 'Test product',
            'stock' => 100,
            'is_available' => true
        ]);
        
        $this->product->prices()->create(['site_id' => $site->id, 'price' => 29.99]);
    }

    public function test_guest_can_add_product_to_cart()
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('data.cart_id'));
    }

    public function test_cart_is_stored_in_cache()
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        $cartId = $response->json('data.cart_id');
        $this->assertTrue(Cache::has("cart:{$cartId}"));
    }

    public function test_cart_persists_for_3_days()
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        $cartId = $response->json('data.cart_id');
        $ttl = Cache::getStore()->getRedis()->ttl("laravel_database_cart:{$cartId}");
        
        $this->assertGreaterThan(259000, $ttl); // ~3 days in seconds
    }

    public function test_can_view_cart()
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $cartId = $response->json('data.cart_id');

        $response = $this->getJson('/api/cart', ['HTTP_X_CART_ID' => $cartId]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['items', 'total']]);
    }

    public function test_can_update_cart_quantity()
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        $cartId = $response->json('data.cart_id');

        $response = $this->putJson("/api/cart/{$this->product->id}", [
            'quantity' => 5
        ], ['HTTP_X_CART_ID' => $cartId]);

        $response->assertStatus(200);
    }

    public function test_can_remove_product_from_cart()
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        $cartId = $response->json('data.cart_id');

        $response = $this->deleteJson("/api/cart/{$this->product->id}", [], ['HTTP_X_CART_ID' => $cartId]);

        $response->assertStatus(200);
    }

    public function test_can_clear_cart()
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        $cartId = $response->json('data.cart_id');

        $response = $this->deleteJson('/api/cart', [], ['HTTP_X_CART_ID' => $cartId]);

        $response->assertStatus(200);
        $this->assertFalse(Cache::has("cart:{$cartId}"));
    }
}
