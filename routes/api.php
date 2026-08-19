<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\LuckyTimeController;
use App\Http\Controllers\Api\V1\AdminProductController;
use App\Http\Middleware\EnsureAdmin;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
    Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

    Route::get('/lucky-time/status', [LuckyTimeController::class, 'status']);

    Route::middleware('auth:sanctum')->group(function () {
        
        // User & Auth Management
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user/profile', [UserController::class, 'show']);
        Route::put('/user/profile', [UserController::class, 'update']);

        // Shopping Cart
        Route::apiResource('cart', CartController::class);

        // Checkout Process
        Route::post('/checkout', CheckoutController::class);

        // Order History (Users can only view their own orders, not delete/update them)
        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

        // Reviews (Users can post, update, or delete their own reviews)
        Route::apiResource('reviews', ReviewController::class)->only(['store', 'update', 'destroy']);

        // Lucky Time Flash Sale (Secured endpoints for concurrency and queueing)
        Route::post('/lucky-time/participate', [LuckyTimeController::class, 'participate']);
    });

    Route::prefix('admin')->middleware(['auth:sanctum', EnsureAdmin::class])->group(function () {
    
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::put('/products/{product}', [AdminProductController::class, 'update']);
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy']);
    
    Route::patch('/products/{product}/stock', [AdminProductController::class, 'updateStock']);

    });

   });