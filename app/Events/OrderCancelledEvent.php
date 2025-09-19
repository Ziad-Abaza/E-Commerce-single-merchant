<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderCancelledEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $cancelledBy;
    public $reason;

    public function __construct(Order $order, $cancelledBy = 'customer', $reason = null)
    {
        $this->order = $order;
        $this->cancelledBy = $cancelledBy;
        $this->reason = $reason;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('orders.' . $this->order->user_id);
    }

    public function broadcastWith()
    {
        $message = match ($this->cancelledBy) {
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
        ];
    }
}
