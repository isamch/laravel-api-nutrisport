<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Requests\Order\CheckoutRequest;
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
        try {
            $order = $this->orderService->getById($id, auth('api')->id());
            return $this->success($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    public function store(CheckoutRequest $request)
    {
        try {
            $order = $this->orderService->createFromCart(
                auth('api')->id(),
                $request->site,
                $request->address_id,
                $request->payment_method
            );

            return $this->success($order, 'Order created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
