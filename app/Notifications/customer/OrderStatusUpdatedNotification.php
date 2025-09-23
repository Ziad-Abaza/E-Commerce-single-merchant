<?php

namespace App\Notifications\customer;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Models\Order;
use Illuminate\Notifications\Channels\DatabaseChannel;

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
        return [DatabaseChannel::class, WebPushChannel::class];
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

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Order Status Updated',
            'body' => "Your order status has changed from '{$this->oldStatus}' to '{$this->order->status}'" . ($this->notes ? " - Note: {$this->notes}" : ""),
            'url' => route('orders.show', $this->order->id),
            'order_id' => $this->order->id,
            'type' => 'order_status_update',
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
        ];
    }
}
