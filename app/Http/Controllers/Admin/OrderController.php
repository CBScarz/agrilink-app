<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Get all orders for admin management
     */
    public function index(Request $request)
    {
        $query = Order::with('buyer', 'farmer', 'items');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by buyer
        if ($request->has('buyer_id') && $request->buyer_id) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by farmer
        if ($request->has('farmer_id') && $request->farmer_id) {
            $query->where('farmer_id', $request->farmer_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order ID
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('id', 'like', "%{$search}%");
        }

        $orders = $query->latest()->paginate(20);
        $buyers = \App\Models\User::where('role', 'buyer')->get();
        $farmers = \App\Models\User::where('role', 'farmer')->where('status', 'active')->get();

        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
            'buyers' => $buyers,
            'farmers' => $farmers,
            'filters' => $request->only(['search', 'status', 'buyer_id', 'farmer_id', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Get order details
     */
    public function show(Order $order)
    {
        $order->load('buyer', 'farmer', 'items');

        return Inertia::render('Admin/OrderDetails', [
            'order' => $order,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('status', 'Order status updated successfully.');
    }

    /**
     * Delete an order
     */
    public function delete(Order $order): RedirectResponse
    {
        $order->delete();

        return back()->with('status', 'Order deleted successfully.');
    }
}
