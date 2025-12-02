<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Models\FarmerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController
{
    public function index()
    {
        $farmer = auth()->user();
        $profile = $farmer->farmerProfile;

        $totalProducts = $farmer->products()->count();
        $totalOrders = $farmer->orders()->count();
        $totalEarnings = 0; // Calculate from completed orders

        $recentOrders = $farmer->orders()
            ->with(['buyer', 'items'])
            ->latest()
            ->take(5)
            ->get();

        $topProducts = $farmer->products()
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        return response()->json([
            'status' => $farmer->status,
            'profile' => $profile,
            'stats' => [
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'total_earnings' => $totalEarnings,
                'average_rating' => 4.5, // TODO: Calculate from reviews
            ],
            'recent_orders' => $recentOrders,
            'top_products' => $topProducts,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'description' => 'sometimes|string',
        ]);

        $profile = auth()->user()->farmerProfile ?? new FarmerProfile();
        $profile->farmer_id = auth()->id();
        $profile->fill($validated);
        $profile->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }
}
