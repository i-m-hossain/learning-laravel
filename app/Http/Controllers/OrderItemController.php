<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Events\OrderItemCreated;

class OrderItemController extends Controller
{
    /**
     * Display a listing of order items by order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $orderItems = OrderItem::with('product')
            ->where('order_id', $id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orderItems
        ]);
    }

    /**
     * Store a newly created order item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total price
        $validated['total_price'] = $validated['unit_price'] * $validated['quantity'];

        $orderItem = OrderItem::create($validated);

        // Trigger the order item created event (Observer pattern)
        // OrderItemCreated::dispatch($orderItem);

        return response()->json([
            'success' => true,
            'message' => 'Order item created successfully',
            'data' => $orderItem
        ], 201);
    }

    /**
     * Display the specified order item.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function show(OrderItem $orderItem)
    {
        return response()->json([
            'success' => true,
            'data' => $orderItem->load('product')
        ]);
    }

    /**
     * Update the specified order item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        // In a real application, updating order items directly 
        // might be restricted and handled through order updates instead
        return response()->json([
            'success' => false,
            'message' => 'Updating order items directly is not allowed. Please update the order instead.'
        ], 403);
    }

    /**
     * Remove the specified order item from storage.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderItem $orderItem)
    {
        // In a real application, deleting order items directly 
        // might be restricted and handled through order updates instead
        return response()->json([
            'success' => false,
            'message' => 'Deleting order items directly is not allowed. Please update the order instead.'
        ], 403);
    }
}