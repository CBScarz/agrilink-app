<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display all farmer's products
     */
    public function farmerProducts(Request $request)
    {
        $farmer = auth()->user();

        if ($farmer->role !== 'farmer') {
            abort(403, 'Only farmers can access this page');
        }

        $query = Product::where('farmer_id', $farmer->id);

        // Search by product name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        $categories = Product::distinct('category')->pluck('category');

        // Get summary stats
        $totalProducts = Product::where('farmer_id', $farmer->id)->count();
        $inStockCount = Product::where('farmer_id', $farmer->id)->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('farmer_id', $farmer->id)->where('stock', '<=', 0)->count();

        return Inertia::render('Farmer/Products', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'stock_status', 'sort']),
            'stats' => [
                'totalProducts' => $totalProducts,
                'inStockCount' => $inStockCount,
                'outOfStockCount' => $outOfStockCount,
            ],
        ]);
    }
}
