<?php

namespace App\Notifications\owner;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Channels\DatabaseChannel;
use App\Models\Order;

class OrderCancelledNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $cancelledBy;
    protected $reason;

    public function __construct(Order $order, $cancelledBy = 'customer', $reason = null)
    {
        $this->order = $order;
        $this->cancelledBy = $cancelledBy;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return [DatabaseChannel::class, WebPushChannel::class];
    }

    public function toDatabase($notifiable)
    {
        $message = match($this->cancelledBy) {
            'customer' => 'Your order has been cancelled by you.',
            'admin' => 'Your order has been cancelled by administrator.',
            default => 'Your order has been cancelled.',
        };

        if ($this->reason) {
            $message .= " Reason: {$this->reason}";
        }

        return [
            'title' => 'Order Cancelled',
            'body' => $message,
            'url' => route('orders.show', $this->order->id),
            'order_id' => $this->order->id,
            'type' => 'order_cancelled',
            'cancelled_by' => $this->cancelledBy,
            'reason' => $this->reason,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $message = match($this->cancelledBy) {
            'customer' => 'Your order has been cancelled by you.',
            'admin' => 'Your order has been cancelled by administrator.',
            default => 'Your order has been cancelled.',
        };

        if ($this->reason) {
            $message .= " Reason: {$this->reason}";
        }

        return new BroadcastMessage([
            'title' => 'Order Cancelled',
            'body' => $message,
            'url' => route('orders.show', $this->order->id),
        ]);
    }

    public function toWebPush($notifiable, $notification)
    {
        $message = match($this->cancelledBy) {
            'customer' => 'Your order has been cancelled by you.',
            'admin' => 'Your order has been cancelled by administrator.',
            default => 'Your order has been cancelled.',
        };

        if ($this->reason) {
            $message .= " Reason: {$this->reason}";
        }

        return (new WebPushMessage)
            ->title('Order Cancelled')
            ->body($message)
            ->action('View Order', route('orders.show', $this->order->id));
    }
}
