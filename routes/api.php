<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (no auth required)
Route::post('/auth/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Get all public products
Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/products/category/{category}', [\App\Http\Controllers\Api\ProductController::class, 'getByCategory']);
Route::get('/products/farmer/{farmerId}', [\App\Http\Controllers\Api\ProductController::class, 'getFarmerProducts']);
Route::get('/products/{product}', [\App\Http\Controllers\Api\ProductController::class, 'show']);

// Product ratings (public read, authenticated write)
Route::get('/products/{product}/ratings', [\App\Http\Controllers\ProductRatingController::class, 'index']);
Route::get('/products/top-products', [\App\Http\Controllers\ProductRatingController::class, 'topProducts']);

// Protected routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/auth/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/auth/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::patch('/auth/profile', [\App\Http\Controllers\Api\AuthController::class, 'updateProfile']);

    // Admin Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        // Farmer Management
        Route::prefix('farmers')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Admin\FarmerController::class, 'index']);
            Route::get('/stats', [\App\Http\Controllers\Api\Admin\FarmerController::class, 'getStats']);
            Route::post('/{farmer}/approve', [\App\Http\Controllers\Api\Admin\FarmerController::class, 'approve']);
            Route::post('/{farmer}/reject', [\App\Http\Controllers\Api\Admin\FarmerController::class, 'reject']);
            Route::delete('/{farmer}', [\App\Http\Controllers\Api\Admin\FarmerController::class, 'delete']);
            Route::get('/{farmer}/permit', [\App\Http\Controllers\Api\Admin\FarmerController::class, 'downloadPermit']);
        });

        // Product Management
        Route::prefix('products')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Admin\ProductController::class, 'index']);
            Route::get('/stats', [\App\Http\Controllers\Api\Admin\ProductController::class, 'getStats']);
            Route::patch('/{product}/stock', [\App\Http\Controllers\Api\Admin\ProductController::class, 'updateStock']);
            Route::delete('/{product}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'delete']);
        });

        // Order Management
        Route::prefix('orders')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Admin\OrderController::class, 'index']);
            Route::get('/stats', [\App\Http\Controllers\Api\Admin\OrderController::class, 'getStats']);
            Route::patch('/{order}/status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus']);
            Route::delete('/{order}', [\App\Http\Controllers\Api\Admin\OrderController::class, 'delete']);
        });
    });

    // Farmer Routes
    Route::prefix('farmer')->middleware('farmer')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Api\Farmer\DashboardController::class, 'index']);
        Route::patch('/profile', [\App\Http\Controllers\Api\Farmer\DashboardController::class, 'updateProfile']);

        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Farmer\ProductController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\Farmer\ProductController::class, 'store']);
            Route::get('/stats', [\App\Http\Controllers\Api\Farmer\ProductController::class, 'getStats']);
            Route::patch('/{product}', [\App\Http\Controllers\Api\Farmer\ProductController::class, 'update']);
            Route::delete('/{product}', [\App\Http\Controllers\Api\Farmer\ProductController::class, 'destroy']);
        });

        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Farmer\OrderController::class, 'index']);
            Route::get('/stats', [\App\Http\Controllers\Api\Farmer\OrderController::class, 'getStats']);
            Route::get('/{order}', [\App\Http\Controllers\Api\Farmer\OrderController::class, 'show']);
            Route::patch('/{order}/status', [\App\Http\Controllers\Api\Farmer\OrderController::class, 'updateStatus']);
        });
    });

    // Buyer Routes
    Route::prefix('buyer')->middleware('buyer')->group(function () {
        // Orders
        Route::prefix('orders')->group(function () {
            Route::post('/checkout', [\App\Http\Controllers\Api\Buyer\OrderController::class, 'checkout']);
            Route::get('/', [\App\Http\Controllers\Api\Buyer\OrderController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\Buyer\OrderController::class, 'store']);
            Route::get('/{order}', [\App\Http\Controllers\Api\Buyer\OrderController::class, 'show']);
            Route::post('/{order}/cancel', [\App\Http\Controllers\Api\Buyer\OrderController::class, 'cancel']);
        });

        // Product Ratings
        Route::post('/products/{product}/ratings', [\App\Http\Controllers\ProductRatingController::class, 'store']);
    });

    // Common authenticated routes
    Route::get('/farmer/{farmer}/average-rating', [\App\Http\Controllers\ProductRatingController::class, 'farmerAverageRating']);

    // Farmer Application (Registration)
    Route::post('/farmer-applications', [\App\Http\Controllers\Api\FarmerApplicationController::class, 'store']);
});
