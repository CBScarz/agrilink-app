<?php

namespace App\Http\Controllers\Api\Farmer;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController
{
    public function index(Request $request)
    {
        $orders = Order::whereHas('items', function ($q) {
            $q->where('farmer_id', auth()->id());
        })
            ->with(['buyer', 'items'])
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function show(Order $order)
    {
        $hasAccess = $order->items()->where('farmer_id', auth()->id())->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['buyer', 'items']));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $hasAccess = $order->items()->where('farmer_id', auth()->id())->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order,
        ]);
    }

    public function getStats()
    {
        $orders = Order::whereHas('items', function ($q) {
            $q->where('farmer_id', auth()->id());
        });

        return response()->json([
            'total_orders' => $orders->count(),
            'pending' => (clone $orders)->where('status', 'pending')->count(),
            'processing' => (clone $orders)->where('status', 'processing')->count(),
            'delivered' => (clone $orders)->where('status', 'delivered')->count(),
        ]);
    }
}
