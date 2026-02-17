<?php

namespace App\Http\Controllers\Api\Vendeur;

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
        $days = $request->input('days', 5);
        $filters = $request->only(['site_id', 'status']);
        
        $orders = $this->orderService->getRecentOrders($days, $filters);
        
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
        $order = $this->orderService->getById($id);
        
        return $this->success($order);
    }
}
