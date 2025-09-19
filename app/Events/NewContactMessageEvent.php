<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;

class NewContactMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contactMessage;

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    public function broadcastOn()
    {
        // Broadcast to a private channel for owners
        return new PrivateChannel('contact-messages.owner');
    }

    public function broadcastWith()
    {
        return [
            'title' => 'New Contact Message',
            'body' => "New message from: {$this->contactMessage->name} - Subject: {$this->contactMessage->subject}",
            'url' => route('dashboard.contact-messages.show', $this->contactMessage->id),
        ];
    }
}
