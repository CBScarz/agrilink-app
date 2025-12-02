<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController
{   
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Order::with(['buyer', 'farmer', 'items']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->buyer_id) {
            $query->where('buyer_id', $request->buyer_id);
        }

        if ($request->farmer_id) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('farmer_id', $request->farmer_id);
            });
        }

        if ($request->search) {
            $query->where('id', 'like', "%{$request->search}%");
        }

        $orders = $query->paginate($request->per_page ?? 20);

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

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order->load(['buyer', 'items']),
        ]);
    }

    public function delete(Order $order)
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

    public function getStats()
    {
        return response()->json([
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ]);
    }
}
