<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class NotifyInventoryManager implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LowStockDetected $event)
    {
        // Get inventory managers
        $inventoryManagers = User::where('department', 'inventory')->get();
        
        // Send notification
        FacadesNotification::send($inventoryManagers, new LowStockAlert($event->product));
    }
}
