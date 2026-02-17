<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status']);
        $orders = $this->orderService->getUserOrders(auth('api')->id(), $filters);
        
        return $this->success([
            'orders' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ]);
    }

    public function show($id)
    {
        $order = $this->orderService->getById($id, auth('api')->id());
        
        return $this->success($order);
    }

    public function store(Request $request)
    {
        // Will be implemented with Cart
        return $this->error('Cart integration required', 501);
    }
}
