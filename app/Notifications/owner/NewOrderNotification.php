<?php

namespace App\Notifications\owner;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Support\Facades\Log;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return [DatabaseChannel::class, WebPushChannel::class];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'New Order Received',
            'body' => 'Order #' . $this->order->id . ' has been placed.',
            'url' => route('orders.show', $this->order->id),
        ]);
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        Log::info('[NewOrderNotification@toWebPush] Sending to user', [
            'user_id' => $notifiable->id,
            'order_id' => $this->order->id
        ]);

        return (new WebPushMessage)
            ->title('New Order Received')
            ->body('Order #' . $this->order->id . ' has been placed.')
            ->action('View Order', route('orders.show', $this->order->id));
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Order Received',
            'body' => 'Order #' . $this->order->id . ' has been placed.',
            'url' => route('orders.show', $this->order->id),
            'order_id' => $this->order->id,
            'type' => 'new_order',
        ];
    }
}
