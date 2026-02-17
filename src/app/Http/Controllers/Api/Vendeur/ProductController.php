<?php

namespace App\Http\Controllers\Api\Vendeur;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Traits\ApiResponse;
use App\Models\Product;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(private ProductService $productService) {}

    public function index()
    {
        $filters = request()->all();
        $filters['created_by'] = auth('api')->id(); // Only vendeur's products
        
        $products = $this->productService->getAll($filters);
        
        return $this->success([
            'products' => ProductResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());
        
        return $this->success(new ProductResource($product), 'Product created successfully', 201);
    }

    public function show(Product $product)
    {
        return $this->success(new ProductResource($product->load(['prices', 'stock', 'categories'])));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->productService->update($product, $request->validated());
        
        return $this->success(new ProductResource($product), 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);
        
        return $this->success(null, 'Product deleted successfully');
    }
}
