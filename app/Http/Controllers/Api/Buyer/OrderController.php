<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
        ]);

        $order = Order::create([
            'buyer_id' => auth()->id(),
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            
            if ($product->stock < $item['quantity']) {
                $order->delete();
                return response()->json([
                    'message' => 'Insufficient stock for ' . $product->name,
                ], Response::HTTP_BAD_REQUEST);
            }

            $itemTotal = $product->price * $item['quantity'];
            $totalAmount += $itemTotal;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'farmer_id' => $product->farmer_id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'total' => $itemTotal,
            ]);

            $product->decrement('stock', $item['quantity']);
        }

        $order->update(['total_amount' => $totalAmount]);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load(['items', 'buyer']),
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        $orders = Order::where('buyer_id', auth()->id())
            ->with(['items', 'items.product'])
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
        if ($order->buyer_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return response()->json($order->load(['items', 'items.product', 'items.farmer']));
    }

    public function cancel(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'message' => 'Cannot cancel order with status ' . $order->status,
            ], Response::HTTP_BAD_REQUEST);
        }

        // Restore stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => $order,
        ]);
    }
}
