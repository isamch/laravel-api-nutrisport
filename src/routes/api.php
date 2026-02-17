<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\ProductController as PublicProductController;
use App\Http\Controllers\Api\Public\CategoryController as PublicCategoryController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public routes
Route::prefix('public')->group(function () {
    Route::get('products', [PublicProductController::class, 'index']);
    Route::get('products/{id}', [PublicProductController::class, 'show']);
    Route::get('categories', [PublicCategoryController::class, 'index']);
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
        // Cart, Orders will be added here
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:administrateur')->group(function () {
        Route::apiResource('products', AdminProductController::class);
    });

    // Vendeur routes
    Route::prefix('vendeur')->middleware('role:vendeur')->group(function () {
        Route::apiResource('products', AdminProductController::class);
    });
});
