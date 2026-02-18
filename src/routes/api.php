<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\ProductController as PublicProductController;
use App\Http\Controllers\Api\Public\CategoryController as PublicCategoryController;
use App\Http\Controllers\Api\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Api\Client\CartController;
use App\Http\Controllers\Api\Client\ProfileController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Vendeur\ProductController as VendeurProductController;
use App\Http\Controllers\Api\Vendeur\OrderController as VendeurOrderController;
use App\Http\Controllers\Api\FeedController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Feeds (public - for third parties)
Route::get('feeds/products/{format}', [FeedController::class, 'show']);

// Public routes
Route::prefix('public')->group(function () {
    Route::get('products', [PublicProductController::class, 'index']);
    Route::get('products/{id}', [PublicProductController::class, 'show']);
    Route::get('categories', [PublicCategoryController::class, 'index']);
});

// Cart routes (available for both guest and authenticated users)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'add']);
    Route::put('/{productId}', [CartController::class, 'update']);
    Route::delete('/{productId}', [CartController::class, 'remove']);
    Route::delete('/', [CartController::class, 'clear']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth protected
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    // Client routes
    Route::prefix('client')->middleware('role:client')->group(function () {
        Route::put('profile', [ProfileController::class, 'update']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);

        Route::get('orders', [ClientOrderController::class, 'index']);
        Route::get('orders/{id}', [ClientOrderController::class, 'show']);
        Route::post('orders', [ClientOrderController::class, 'store']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:administrateur')->group(function () {
        Route::apiResource('products', AdminProductController::class);
        Route::get('vendeurs/{vendeurId}/products', [AdminProductController::class, 'getVendeurProducts']);
        Route::get('orders', [AdminOrderController::class, 'index']);
        Route::get('orders/{id}', [AdminOrderController::class, 'show']);
        Route::put('orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    });

    // Vendeur routes
    Route::prefix('vendeur')->middleware('role:vendeur')->group(function () {
        Route::apiResource('products', VendeurProductController::class);
        Route::get('orders', [VendeurOrderController::class, 'index']);
        Route::get('orders/{id}', [VendeurOrderController::class, 'show']);
    });
});
