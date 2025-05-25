<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
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
        // Load related items and user for the email
        $order = $event->order->load('items.product', 'user');
        // Send email
        Mail::to($order->user->email)
            ->send(new OrderConfirmation($order));
    }
}
