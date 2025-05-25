<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateInventory implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     */
     public function handle(OrderCreated $event)
    {
        foreach ($event->order->items as $item) {
            $product = $item->product;
            
            // Add validation to prevent negative stock
            if ($product->stock >= $item->quantity) {
                $product->stock -= $item->quantity;
                $product->save();
                
                Log::info('Product stock updated', [
                    'product_id' => $product->id,
                    'old_stock' => $product->stock + $item->quantity,
                    'new_stock' => $product->stock
                ]);
            } else {
                Log::warning('Insufficient stock for product', [
                    'product_id' => $product->id,
                    'requested' => $item->quantity,
                    'available' => $product->stock
                ]);
            }
        }
    }
}
