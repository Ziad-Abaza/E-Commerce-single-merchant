<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $oldStatus;
    public $notes;

    public function __construct(Order $order, $oldStatus, $notes = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->notes = $notes;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('orders.' . $this->order->user_id);
    }

    public function broadcastWith()
    {
        return [
            'title' => 'Order Status Updated',
            'body' => "Your order status has changed from '{$this->oldStatus}' to '{$this->order->status}'" . ($this->notes ? " - Note: {$this->notes}" : ""),
            'url' => route('orders.show', $this->order->id),
        ];
    }
}
