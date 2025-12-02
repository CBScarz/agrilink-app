<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Redirect admin to dashboard
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect('/admin/dashboard');
    }
    
    // Redirect farmer to their dashboard
    if (auth()->check() && auth()->user()->role === 'farmer') {
        return redirect('/farmer/dashboard');
    }
    
    $products = \App\Models\Product::with('farmer')->limit(4)->get();
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'products' => $products,
    ]);
})->name('home');

Route::redirect('/dashboard', '/')->name('dashboard');

// Farmer Dashboard
Route::get('/farmer/dashboard', function () {
    $farmer = auth()->user();
    
    if ($farmer->role !== 'farmer') {
        abort(403);
    }

    // Get farmer's products
    $totalProducts = \App\Models\Product::where('farmer_id', $farmer->id)->count();
    $totalOrders = \App\Models\Order::where('farmer_id', $farmer->id)->count();
    $totalEarnings = \App\Models\Order::where('farmer_id', $farmer->id)->sum('total_amount') ?? 0;
    
    // Calculate average rating (placeholder - can add ratings later)
    $averageRating = 4.5; // This will be updated when ratings are implemented
    
    // Get recent orders
    $recentOrders = \App\Models\Order::where('farmer_id', $farmer->id)
        ->with('buyer', 'items')
        ->latest()
        ->limit(10)
        ->get();
    
    // Get top products
    $topProducts = \App\Models\Product::where('farmer_id', $farmer->id)
        ->withCount('orderItems')
        ->orderBy('order_items_count', 'desc')
        ->limit(5)
        ->get();
    
    // Get low stock products
    $lowStockProducts = \App\Models\Product::where('farmer_id', $farmer->id)
        ->where('stock', '<=', 5)
        ->get();
    
    // Get farmer profile
    $farmerProfile = \App\Models\FarmerProfile::where('user_id', $farmer->id)->first();

    return Inertia::render('Farmer/Dashboard', [
        'farmer' => $farmer,
        'farmerProfile' => $farmerProfile,
        'totalProducts' => $totalProducts,
        'totalOrders' => $totalOrders,
        'totalEarnings' => $totalEarnings,
        'averageRating' => $averageRating,
        'recentOrders' => $recentOrders,
        'topProducts' => $topProducts,
        'lowStockProducts' => $lowStockProducts,
    ]);
})->middleware(['auth', 'verified'])->name('farmer.dashboard');

Route::get('/admin/dashboard', function () {
    $totalSales = \App\Models\Order::sum('total_amount') ?? 0;
    $totalFarmers = \App\Models\User::where('role', 'farmer')->count();
    $totalBuyers = \App\Models\User::where('role', 'buyer')->count();
    $totalProducts = \App\Models\Product::count();
    $totalOrders = \App\Models\Order::count();
    $pendingFarmers = \App\Models\User::where('role', 'farmer')->where('status', 'pending')->count();
    
    $recentOrders = \App\Models\Order::with('buyer', 'items')->latest()->limit(10)->get();
    $topProducts = \App\Models\Product::withCount('orderItems')
        ->orderBy('order_items_count', 'desc')
        ->limit(5)
        ->get();
    
    return Inertia::render('Admin/Dashboard', [
        'totalSales' => $totalSales,
        'totalFarmers' => $totalFarmers,
        'totalBuyers' => $totalBuyers,
        'totalProducts' => $totalProducts,
        'totalOrders' => $totalOrders,
        'pendingFarmers' => $pendingFarmers,
        'recentOrders' => $recentOrders,
        'topProducts' => $topProducts,
    ]);
})->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::get('/admin/farmers', function () {
    $farmers = \App\Models\User::where('role', 'farmer')
        ->with('farmerProfile')
        ->latest()
        ->get();
    
    return Inertia::render('Admin/Farmers', ['farmers' => $farmers]);
})->middleware(['auth', 'verified'])->name('admin.farmers');

Route::get('/admin/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.products');

Route::get('/admin/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.orders');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/admin/farmers/{farmer}/accept', [\App\Http\Controllers\Admin\FarmerController::class, 'accept'])->name('admin.farmers.accept');
    Route::post('/admin/farmers/{farmer}/reject', [\App\Http\Controllers\Admin\FarmerController::class, 'reject'])->name('admin.farmers.reject');
    Route::post('/admin/farmers/{farmer}/delete', [\App\Http\Controllers\Admin\FarmerController::class, 'delete'])->name('admin.farmers.delete');
});

// Admin-only routes
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/farmers/{farmer}/permit', [\App\Http\Controllers\Admin\FarmerController::class, 'downloadPermit'])->name('admin.farmers.permit');
    Route::post('/admin/products/{product}/delete', [\App\Http\Controllers\Admin\ProductController::class, 'delete'])->name('admin.products.delete');
    Route::patch('/admin/products/{product}/stock', [\App\Http\Controllers\Admin\ProductController::class, 'updateStock'])->name('admin.products.stock');
    Route::patch('/admin/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::post('/admin/orders/{order}/delete', [\App\Http\Controllers\Admin\OrderController::class, 'delete'])->name('admin.orders.delete');
});

Route::get('/products', function () {
    $products = \App\Models\Product::with('farmer')->get();
    return Inertia::render('Products', ['products' => $products]);
})->name('products.index');

Route::get('/farmer/products', [\App\Http\Controllers\ProductController::class, 'farmerProducts'])
    ->middleware(['auth', 'verified'])
    ->name('farmer.products');

Route::get('/farmer/products/create', function () {
    return Inertia::render('Farmer/AddProduct');
})->middleware(['auth', 'verified'])->name('farmer.products.create');

Route::post('/farmer/products', [\App\Http\Controllers\FarmerProductController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('farmer.products.store');

Route::get('/cart', function () {
    return Inertia::render('Cart', ['cartItems' => []]);
})->middleware('auth')->name('cart');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
