<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushMessage;
use App\Models\Order;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $notes;

    public function __construct(Order $order, $oldStatus, $notes = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['broadcast', 'webpush'];
    }


    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Order Status Updated',
            'body' => "Your order status has changed from '{$this->oldStatus}' to '{$this->order->status}'" . ($this->notes ? " - Note: {$this->notes}" : ""),
            'url' => route('orders.show', $this->order->id),
        ]);
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Order Status Updated')
            ->body("Your order status has changed from '{$this->oldStatus}' to '{$this->order->status}'" . ($this->notes ? " - Note: {$this->notes}" : ""))
            ->action('View Order', route('orders.show', $this->order->id));
    }
}
