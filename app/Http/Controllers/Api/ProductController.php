<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    
    use AuthorizesRequests;
    /**
     * Get all products with optional filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('farmer');

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by farmer
        if ($request->has('farmer_id')) {
            $query->where('farmer_id', $request->input('farmer_id'));
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter in-stock items only
        if ($request->has('in_stock') && $request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $products = $query->latest()->paginate(15);

        return response()->json($products);
    }

    /**
     * Create a new product (Farmer only).
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        // Check if farmer account is approved
        if (auth()->user()->role === 'farmer' && auth()->user()->status !== 'active') {
            return response()->json([
                'message' => 'Your account is pending approval. Please wait for admin verification before creating products.',
                'status' => auth()->user()->status,
            ], 403);
        }

        $validated = $request->validated();
        $validated['farmer_id'] = auth()->id();

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product->load('farmer'),
        ], 201);
    }

    /**
     * Get a specific product.
     */
    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return response()->json($product->load('farmer', 'orderItems'));
    }

    /**
     * Update a product (Farmer can only update their own).
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validated();
        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product->load('farmer'),
        ]);
    }

    /**
     * Delete a product (Farmer can only delete their own).
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }

    /**
     * Get products by a specific farmer.
     */
    public function getFarmerProducts(int $farmerId): JsonResponse
    {
        $products = Product::where('farmer_id', $farmerId)
            ->with('farmer')
            ->latest()
            ->paginate(15);

        return response()->json($products);
    }

    /**
     * Get products by category.
     */
    public function getByCategory(string $category): JsonResponse
    {
        $products = Product::where('category', $category)
            ->with('farmer')
            ->latest()
            ->paginate(15);

        return response()->json($products);
    }
}
