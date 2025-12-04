<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductRatingController extends Controller
{
    /**
     * Store a rating for a product.
     *
     * @param Product $product
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Product $product, Request $request): JsonResponse
    {
        // Validate that user is logged in and is a buyer
        if (!auth()->check() || !auth()->user()->isBuyer()) {
            return response()->json(['error' => 'Only buyers can rate products.'], 403);
        }

        // Validate rating data
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user has already rated this product
        $existingRating = Rating::where('product_id', $product->id)
            ->where('buyer_id', auth()->id())
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? $existingRating->comment,
            ]);
            $rating = $existingRating;
            $message = 'Rating updated successfully.';
        } else {
            // Create new rating
            $rating = Rating::create([
                'product_id' => $product->id,
                'buyer_id' => auth()->id(),
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);
            $message = 'Rating created successfully.';
        }

        return response()->json([
            'message' => $message,
            'rating' => $rating,
            'averageRating' => $product->ratings()->avg('rating') ?? 0,
        ], 201);
    }

    /**
     * Get ratings for a product.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function index(Product $product): JsonResponse
    {
        $ratings = $product->ratings()
            ->with(['buyer' => fn($query) => $query->select('id', 'name', 'email')])
            ->latest()
            ->get();

        $averageRating = $product->ratings()->avg('rating') ?? 0;
        $totalRatings = $product->ratings()->count();

        // Count ratings by star
        $ratingDistribution = collect(range(1, 5))->mapWithKeys(function ($star) use ($product) {
            return [
                $star => $product->ratings()->where('rating', $star)->count()
            ];
        });

        return response()->json([
            'averageRating' => round($averageRating, 2),
            'totalRatings' => $totalRatings,
            'ratingDistribution' => $ratingDistribution,
            'ratings' => $ratings,
        ]);
    }

    /**
     * Get top performing products (most ordered).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function topProducts(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);
        $farmerId = auth()->user()->isFarmer() ? auth()->id() : null;

        $query = Product::with(['ratings', 'orderItems'])
            ->withCount('orderItems')
            ->with([
                'orderItems' => fn($q) => $q->latest(),
                'ratings' => fn($q) => $q->select('id', 'product_id', 'rating'),
            ]);

        // If farmer, filter by their products
        if ($farmerId) {
            $query->where('farmer_id', $farmerId);
        }

        $topProducts = $query
            ->orderByDesc('order_items_count')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->image_url,
                    'price' => $product->price,
                    'order_count' => $product->order_items_count,
                    'average_rating' => $product->ratings()->avg('rating') ?? 0,
                    'rating_count' => $product->ratings()->count(),
                ];
            });

        return response()->json([
            'topProducts' => $topProducts,
        ]);
    }

    /**
     * Get farmer's average rating.
     *
     * @return JsonResponse
     */
    public function farmerAverageRating(): JsonResponse
    {
        if (!auth()->user()->isFarmer()) {
            return response()->json(['error' => 'Only farmers can access this.'], 403);
        }

        $farmer = auth()->user();
        $averageRating = $farmer->products()
            ->with('ratings')
            ->get()
            ->flatMap(fn($product) => $product->ratings)
            ->avg('rating') ?? 0;

        $totalRatings = $farmer->products()
            ->with('ratings')
            ->get()
            ->flatMap(fn($product) => $product->ratings)
            ->count();

        return response()->json([
            'averageRating' => round($averageRating, 2),
            'totalRatings' => $totalRatings,
        ]);
    }
}
