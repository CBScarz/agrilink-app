<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Product::with('farmer');

        if ($request->farmer_id) {
            $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->stock_status === 'in_stock') {
            $query->where('stock', '>', 0);
        } elseif ($request->stock_status === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $products = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function updateStock(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Stock updated successfully',
            'product' => $product,
        ]);
    }

    public function delete(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function getStats()
    {
        return response()->json([
            'total' => Product::count(),
            'in_stock' => Product::where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->count(),
        ]);
    }
}
