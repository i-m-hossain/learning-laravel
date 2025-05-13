<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\NewOrderForShipping;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class NotifyShippingDepartment implements ShouldQueue
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
    public function handle(OrderStatusChanged $event)
    {
        // Only notify when status changes to "paid"
        if ($event->order->status === 'paid' && $event->oldStatus !== 'paid') {
            // Get shipping department staff
            $shippingStaff = User::where('department', 'shipping')->get();
            
            // Send notification
            Notification::send($shippingStaff, new NewOrderForShipping($event->order));
        }
    }
}
