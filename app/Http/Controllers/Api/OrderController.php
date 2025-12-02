<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class OrderController extends Controller
{   
    use AuthorizesRequests;
    /**
     * Get all orders (with role-based filtering).
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Order::with(['buyer', 'farmer', 'items.product']);

        // Filter based on user role
        if ($user->isBuyer()) {
            $query->where('buyer_id', $user->id);
        } elseif ($user->isFarmer()) {
            $query->where('farmer_id', $user->id);
        }
        // Admins see all orders

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(15);

        return response()->json($orders);
    }

    /**
     * Create a new order (Buyer checkout process).
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];

        return DB::transaction(function () use ($validated, $items) {
            $totalAmount = 0;

            // Validate all products exist and are in stock
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    return response()->json([
                        'message' => 'One or more products do not exist.',
                    ], 400);
                }

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'message' => "Insufficient stock for product: {$product->name}",
                    ], 400);
                }

                $totalAmount += $product->price * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'buyer_id' => auth()->id(),
                'farmer_id' => $validated['farmer_id'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
            ]);

            // Create order items and reduce product stock
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                $product->reduceStock($item['quantity']);
            }

            return response()->json([
                'message' => 'Order created successfully.',
                'order' => $order->load('buyer', 'farmer', 'items.product'),
            ], 201);
        });
    }

    /**
     * Get a specific order.
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json($order->load('buyer', 'farmer', 'items.product'));
    }

    /**
     * Update order status (Farmer/Admin).
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validated();
        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order->load('buyer', 'farmer', 'items.product'),
        ]);
    }

    /**
     * Cancel an order (Buyer can cancel pending orders).
     */
    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);

        // Restore product stock
        foreach ($order->items as $item) {
            $item->product->increaseStock($item->quantity);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'order' => $order->load('buyer', 'farmer', 'items.product'),
        ]);
    }

    /**
     * Get buyer's orders.
     */
    public function getBuyerOrders(): JsonResponse
    {
        $orders = Order::where('buyer_id', auth()->id())
            ->with('farmer', 'items.product')
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Get farmer's received orders.
     */
    public function getFarmerOrders(): JsonResponse
    {
        $orders = Order::where('farmer_id', auth()->id())
            ->with('buyer', 'items.product')
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Get order statistics (Admin/Farmer).
     */
    public function getOrderStats(): JsonResponse
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $stats = [
                'total_orders' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
                'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            ];
        } elseif ($user->isFarmer()) {
            $stats = [
                'total_orders' => Order::where('farmer_id', $user->id)->count(),
                'pending' => Order::where('farmer_id', $user->id)->where('status', 'pending')->count(),
                'processing' => Order::where('farmer_id', $user->id)->where('status', 'processing')->count(),
                'shipped' => Order::where('farmer_id', $user->id)->where('status', 'shipped')->count(),
                'delivered' => Order::where('farmer_id', $user->id)->where('status', 'delivered')->count(),
                'total_revenue' => Order::where('farmer_id', $user->id)->where('status', 'delivered')->sum('total_amount'),
            ];
        } else {
            return response()->json([
                'message' => 'Unauthorized to view order statistics.',
            ], 403);
        }

        return response()->json($stats);
    }
}
