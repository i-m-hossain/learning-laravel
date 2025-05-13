<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderForShipping extends Notification
{
    use Queueable;
    protected $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }


     public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New Order Ready for Shipping: #{$this->order->id}")
            ->line("A new order requires shipping preparation.")
            ->line("Order ID: {$this->order->id}")
            ->line("Customer: {$this->order->user->name}")
            ->line("Items: {$this->order->items->count()}")
            ->action('View Order Details', url("/admin/orders/{$this->order->id}"));
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'customer' => $this->order->user->name,
            'items_count' => $this->order->items->count(),
        ];
    }
}
