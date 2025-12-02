<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get all products for admin management
     */
    public function index(Request $request)
    {
        $query = Product::with('farmer');

        // Filter by farmer
        if ($request->has('farmer_id') && $request->farmer_id) {
            $query->where('farmer_id', $request->farmer_id);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        $products = $query->latest()->paginate(20);
        $farmers = \App\Models\User::where('role', 'farmer')->where('status', 'active')->get();

        return inertia('Admin/Products', [
            'products' => $products,
            'farmers' => $farmers,
            'filters' => $request->only(['search', 'farmer_id', 'category', 'stock_status']),
        ]);
    }

    /**
     * Delete a product
     */
    public function delete(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('status', 'Product deleted successfully.');
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['stock' => $validated['stock']]);

        return back()->with('status', 'Product stock updated successfully.');
    }
}
