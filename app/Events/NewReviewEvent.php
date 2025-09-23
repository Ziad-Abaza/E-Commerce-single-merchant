<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Review;

class NewReviewEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function broadcastOn()
    {
        // Broadcast to a private channel for owners
        return new PrivateChannel('reviews.owner');
    }

    public function broadcastWith()
    {
        return [
            'title' => 'New Product Review',
            'body' => "A new review has been posted for product: {$this->review->product->name} (Rating: {$this->review->rating} stars)",
            'url' => route('products.show', $this->review->product->id),
        ];
    }
}
