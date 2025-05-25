<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
     use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->view('emails.orders.confirmation')
            ->subject("Order Confirmation #{$this->order->id}")
            ->with([
                'orderItems' => $this->order->items,
                'shippingAddress' => $this->order->shipping_address,
                'totalAmount' => $this->order->total_amount,
            ]);
    }
}
