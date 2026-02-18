<?php

namespace App\Http\Controllers\Api\Admin;

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

    public function show(Product $product)
    {
        return $this->success(new ProductResource($product->load(['prices', 'categories', 'images'])));
    }

    public function getVendeurProducts($vendeurId)
    {
        $filters = request()->all();
        $filters['created_by'] = $vendeurId;
        
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

    public function uploadImages(Product $product)
    {
        request()->validate([
            'images' => 'required|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $uploadedImages = $this->productService->uploadImages($product, request()->file('images'));
        
        return $this->success($uploadedImages, 'Images uploaded successfully');
    }

    public function deleteImage($imageId)
    {
        $this->productService->deleteImage($imageId);
        
        return $this->success(null, 'Image deleted successfully');
    }
}
