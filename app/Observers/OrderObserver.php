<?php

namespace App\Observers;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Notifications\OrderStatusNotification;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
     protected $inventoryService;
    
    /**
     * Constructor with dependency injection
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order)
    {
        // Log the new order
        Log::info('New order created', ['order_id' => $order->id, 'user_id' => $order->user_id]);
        
        // Reserve inventory
        $this->inventoryService->reserveItems($order);
        
        // Invalidate product cache
        $this->invalidateRelatedCache($order);
        
        // Notify admin about new order
        $this->notifyAdmin($order, 'New order received');
    }
    
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order)
    {
        // Check if status was changed
        if ($order->isDirty('status')) {
            // Dispatch event when status changes
            event(new OrderStatusChanged($order, $order->getOriginal('status')));
            
            // Notify customer about status change
            $order->user->notify(new OrderStatusNotification($order));
        }
        
        // Check if payment status was changed
        if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
            // Process the order after payment
            $this->inventoryService->confirmInventoryReduction($order);
        }
        
        // Invalidate cache
        $this->invalidateRelatedCache($order);
    }
    
    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order)
    {
        // Release any reserved inventory
        $this->inventoryService->releaseReservedItems($order);
        
        // Log the deletion
        Log::info('Order deleted', ['order_id' => $order->id, 'user_id' => $order->user_id]);
        
        // Invalidate cache
        $this->invalidateRelatedCache($order);
    }
    
    /**
     * Handle the Order "restored" event (for soft deletes).
     */
    public function restored(Order $order)
    {
        // Re-reserve inventory
        $this->inventoryService->reserveItems($order);
        
        // Invalidate cache
        $this->invalidateRelatedCache($order);
    }
    
    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order)
    {
        // Similar to deleted but may need additional cleanup
        Log::info('Order permanently deleted', ['order_id' => $order->id]);
    }
    
    /**
     * Helper method to invalidate related cache
     */
    private function invalidateRelatedCache(Order $order)
    {
        // Clear user's orders cache
        Cache::forget("user.{$order->user_id}.orders");
        
        // Clear any product inventory caches for products in this order
        foreach ($order->items as $item) {
            Cache::forget("product.{$item->product_id}.inventory");
        }
        
        // Clear dashboard stats cache
        Cache::forget('dashboard.order.stats');
    }
    
    /**
     * Helper method to notify administrators
     */
    private function notifyAdmin(Order $order, string $message)
    {
        // In a real app, you would fetch admin users and notify them
        // For simplicity, just logging here
        Log::channel('admin_notifications')->info($message, ['order_id' => $order->id]);
    }
}
