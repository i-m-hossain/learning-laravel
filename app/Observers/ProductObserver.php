<?php

namespace App\Observers;

use App\Events\LowStockDetected;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    public function updated(Product $product)
    {
        // Check if stock was updated and if it's now below threshold
        if ($product->wasChanged('stock') && $product->stock < 5) {
            // You could dispatch an event here
            event(new LowStockDetected($product));
            
            // Or send notification directly
            Log::warning("Low stock alert: Product {$product->id} ({$product->name}) has only {$product->stock} units left");
        }
    }

    public function creating(Product $product)
    {
        // Generate a SKU if not provided
        if (empty($product->sku)) {
            $product->sku = 'PRD-' . strtoupper(substr(md5(uniqid()), 0, 8));
        }
    }
    
    public function created(Product $product)
    {
        Log::info("New product created: {$product->name} with SKU {$product->sku}");
    }
}
