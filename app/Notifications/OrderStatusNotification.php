<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;
    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'processing' => 'Your order is now being processed.',
            'shipped' => 'Great news! Your order has been shipped.',
            'delivered' => 'Your order has been delivered.',
            'cancelled' => 'Your order has been cancelled.',
        ];

        $message = $statusMessages[$this->order->status] ?? "Your order status is now {$this->order->status}.";
        return (new MailMessage)
            ->subject("Order #{$this->order->id} Status Update")
            ->greeting("Hello {$notifiable->name}!")
            ->line($message)
            ->line("Order ID: #{$this->order->id}")
            ->action('View Order Details', url("/orders/{$this->order->id}"))
            ->line('Thank you for shopping with us!');
    }
    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => "Your order #{$this->order->id} is now {$this->order->status}",
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => "Your order #{$this->order->id} status is now {$this->order->status}."
        ];
        
    }
}
