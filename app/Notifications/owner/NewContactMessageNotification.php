<?php

namespace App\Notifications\owner;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Channels\DatabaseChannel;
use App\Models\ContactMessage;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    protected $contactMessage;

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    public function via($notifiable)
    {
        return [DatabaseChannel::class, WebPushChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Contact Message',
            'body' => "New message from: {$this->contactMessage->name} - Subject: {$this->contactMessage->subject}",
            'url' => route('dashboard.contact-messages.show', $this->contactMessage->id),
            'contact_message_id' => $this->contactMessage->id,
            'type' => 'new_contact_message',
            'sender_name' => $this->contactMessage->name,
            'sender_email' => $this->contactMessage->email,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'New Contact Message',
            'body' => "New message from: {$this->contactMessage->name} - Subject: {$this->contactMessage->subject}",
            'url' => route('dashboard.contact-messages.show', $this->contactMessage->id),
        ]);
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('New Contact Message')
            ->body("New message from: {$this->contactMessage->name} - Subject: {$this->contactMessage->subject}")
            ->action('View Message', route('dashboard.contact-messages.show', $this->contactMessage->id));
    }
}
