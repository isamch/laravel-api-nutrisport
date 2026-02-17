<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(private ProductService $productService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['site_id', 'category_id', 'in_stock']);
        $products = $this->productService->getAll($filters);
        
        return $this->success([
            'products' => ProductResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $siteId = $request->query('site_id');
        $product = $this->productService->getById($id, $siteId);
        
        return $this->success(new ProductResource($product));
    }
}
