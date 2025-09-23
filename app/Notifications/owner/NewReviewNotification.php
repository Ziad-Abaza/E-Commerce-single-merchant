<?php

namespace App\Notifications\owner;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Channels\DatabaseChannel;
use App\Models\Review;
use App\Models\Product;

class NewReviewNotification extends Notification
{
    use Queueable;

    protected $review;
    protected $product;

    public function __construct(Review $review)
    {
        $this->review = $review;
        $this->product = $review->product;
    }

    public function via($notifiable)
    {
        return [DatabaseChannel::class, WebPushChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Product Review',
            'body' => "A new review has been posted for product: {$this->product->name} (Rating: {$this->review->rating} stars)",
            'url' => route('products.show', $this->product->id),
            'product_id' => $this->product->id,
            'review_id' => $this->review->id,
            'type' => 'new_review',
            'rating' => $this->review->rating,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'New Product Review',
            'body' => "A new review has been posted for product: {$this->product->name} (Rating: {$this->review->rating} stars)",
            'url' => route('products.show', $this->product->id),
        ]);
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('New Product Review')
            ->body("A new review has been posted for product: {$this->product->name} (Rating: {$this->review->rating} stars)")
            ->action('View Product', route('products.show', $this->product->id));
    }
}
