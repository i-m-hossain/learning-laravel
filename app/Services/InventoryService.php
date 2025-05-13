<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService{
    /**
     * Reserve inventory items for an order
     */
    public function reserveItems(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                // Update inventory with locking to prevent race conditions
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->lockForUpdate()
                    ->update([
                        'reserved_quantity' => DB::raw("reserved_quantity + {$item->quantity}")
                    ]);
                
                Log::info("Reserved {$item->quantity} units of product #{$item->product_id} for order #{$order->id}");
            }
        });
    }

    /**
     * Confirm inventory reduction after successful payment
     */
    public function confirmInventoryReduction(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->lockForUpdate()
                    ->update([
                        'reserved_quantity' => DB::raw("reserved_quantity - {$item->quantity}"),
                        'quantity' => DB::raw("quantity - {$item->quantity}")
                    ]);
                
                Log::info("Confirmed reduction of {$item->quantity} units of product #{$item->product_id} for order #{$order->id}");
            }
        });
    }
    

    /**
     * Release reserved inventory if order is cancelled
     */
    public function releaseReservedItems(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->lockForUpdate()
                    ->update([
                        'reserved_quantity' => DB::raw("reserved_quantity - {$item->quantity}")
                    ]);
                
                Log::info("Released reservation of {$item->quantity} units of product #{$item->product_id} from order #{$order->id}");
            }
        });
    }
}