<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponse;

    public function __construct(private CartService $cartService) {}

    private function getIdentifiers(Request $request)
    {
        // Only authenticated users can use cart
        if (!auth('api')->check()) {
            throw new \Exception('Please login to use cart');
        }
        
        return [auth('api')->id(), null];
    }

    public function index(Request $request)
    {
        try {
            [$userId, $sessionId] = $this->getIdentifiers($request);
            $cart = $this->cartService->get($userId);
            $total = $this->cartService->getTotal($userId);

            return $this->success([
                'items' => array_values($cart),
                'total' => $total
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    public function add(AddToCartRequest $request)
    {
        try {
            [$userId, $sessionId] = $this->getIdentifiers($request);
            
            $cart = $this->cartService->add(
                $request->product_id,
                $request->quantity,
                $request->site,
                $userId
            );

            return $this->success(array_values($cart), 'Product added to cart');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function update(UpdateCartRequest $request, $productId)
    {
        try {
            [$userId, $sessionId] = $this->getIdentifiers($request);
            
            $cart = $this->cartService->update(
                $productId,
                $request->quantity,
                $userId
            );

            return $this->success(array_values($cart), 'Cart updated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function remove(Request $request, $productId)
    {
        try {
            [$userId, $sessionId] = $this->getIdentifiers($request);
            $cart = $this->cartService->remove($productId, $userId);

            return $this->success(array_values($cart), 'Product removed from cart');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function clear(Request $request)
    {
        try {
            [$userId, $sessionId] = $this->getIdentifiers($request);
            $this->cartService->clear($userId);

            return $this->success([], 'Cart cleared');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
