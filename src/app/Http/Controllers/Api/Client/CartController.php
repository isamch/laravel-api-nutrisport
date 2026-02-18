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
        $userId = auth('api')->check() ? auth('api')->id() : null;
        
        // For guests, use a custom header or generate from IP + User Agent
        $sessionId = $userId ? null : (
            $request->header('X-Guest-ID') ?: 
            md5($request->ip() . $request->userAgent())
        );
        
        return [$userId, $sessionId];
    }

    public function index(Request $request)
    {
        [$userId, $sessionId] = $this->getIdentifiers($request);
        $cart = $this->cartService->get($userId, $sessionId);
        $total = $this->cartService->getTotal($userId, $sessionId);

        return $this->success([
            'items' => array_values($cart),
            'total' => $total
        ]);
    }

    public function add(AddToCartRequest $request)
    {
        [$userId, $sessionId] = $this->getIdentifiers($request);
        
        $cart = $this->cartService->add(
            $request->product_id,
            $request->quantity,
            $request->site,
            $userId,
            $sessionId
        );

        return $this->success(array_values($cart), 'Product added to cart');
    }

    public function update(UpdateCartRequest $request, $productId)
    {
        [$userId, $sessionId] = $this->getIdentifiers($request);
        
        $cart = $this->cartService->update(
            $productId,
            $request->quantity,
            $userId,
            $sessionId
        );

        return $this->success(array_values($cart), 'Cart updated');
    }

    public function remove(Request $request, $productId)
    {
        [$userId, $sessionId] = $this->getIdentifiers($request);
        $cart = $this->cartService->remove($productId, $userId, $sessionId);

        return $this->success(array_values($cart), 'Product removed from cart');
    }

    public function clear(Request $request)
    {
        [$userId, $sessionId] = $this->getIdentifiers($request);
        $this->cartService->clear($userId, $sessionId);

        return $this->success([], 'Cart cleared');
    }
}
