<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Order::with('orderItems.product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }


    /**
     * Display a listing of all orders (admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function allOrders(Request $request)
    {
        // Check if user is admin - this is a simplistic approach
        // In a real app, you would have proper role-based authorization
        if (!$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $orders = Order::with(['orderItems.product', 'user'])->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalAmount += $product->price * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'],
            ]);

            // Create order items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            DB::commit();

            // Note: Events/Observers automatically handle:
            // 1. Email confirmations
            // 2. Inventory updates 
            // 3. Other notifications
            // We don't need to call them explicitly

            return response()->json([
                'message' => 'Order created successfully',
                'order_id' => $order->id
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order: ' . $e->getMessage()], 500);
        }
    }
    public function show(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user or user is admin
        if ($order->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => $order->load('orderItems.product')
        ]);
    }
    /**
     * Update the order status.
     */
    public function updateStatus(Order $order, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);
        $oldStatus =$order->status;
        // Update the order status
        $order->status = $validated['status'];
        $order->save();

        OrderStatusChanged::dispatch($order, $oldStatus);

        // No need to manually trigger events or notifications
        // The Observer pattern handles all side effects

        return response()->json([
            'message' => 'Order status updated successfully',
            'status' => $order->status
        ]);
    }

    /**
     * Cancel an order (by the user who placed it).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Can only cancel if order is pending or processing
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order in ' . $order->status . ' status'
            ], 422);
        }
        
        $oldStatus = $order->status;
        $order->status = 'cancelled';
        $order->save();
        
        // Trigger order cancelled event (Observer pattern)
        // OrderCancelled::dispatch($order);
        
        // Return stock for cancelled orders
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            $product->stock += $item->quantity;
            $product->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order
        ]);
    }
}
