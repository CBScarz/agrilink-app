<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Product::where('farmer_id', auth()->id());

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

        if ($request->sort === 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort === 'price_high') {
            $query->orderBy('price', 'desc');
        } elseif ($request->sort === 'name') {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }

        $products = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'data' => $products->items(),
            'stats' => [
                'total' => Product::where('farmer_id', auth()->id())->count(),
                'in_stock' => Product::where('farmer_id', auth()->id())->where('stock', '>', 0)->count(),
                'out_of_stock' => Product::where('farmer_id', auth()->id())->where('stock', '<=', 0)->count(),
            ],
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        // Check if farmer is active
        if (auth()->user()->status !== 'active') {
            return response()->json([
                'message' => 'Your account is pending approval. Please wait for admin verification before creating products.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string',
            'availability' => 'nullable|string',
            'unit' => 'nullable|string',
            'origin' => 'nullable|string',
            'harvestDate' => 'nullable|date',
            'expirationDate' => 'nullable|date',
            'certification' => 'nullable|string',
            'features' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['farmer_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_url'] = $imagePath;
        }

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->farmer_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'category' => 'sometimes|string',
            'availability' => 'nullable|string',
            'unit' => 'nullable|string',
            'origin' => 'nullable|string',
            'harvestDate' => 'nullable|date',
            'expirationDate' => 'nullable|date',
            'certification' => 'nullable|string',
            'features' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_url'] = $imagePath;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->farmer_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function getStats()
    {
        return response()->json([
            'total' => Product::where('farmer_id', auth()->id())->count(),
            'in_stock' => Product::where('farmer_id', auth()->id())->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('farmer_id', auth()->id())->where('stock', '<=', 0)->count(),
            'total_earnings' => 0, // Calculate from orders
        ]);
    }
}
