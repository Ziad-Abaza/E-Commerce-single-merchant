<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class CustomResetPassword extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = call_user_func(
            [static::class, 'createUrlUsing'],
            $notifiable,
            $this->token
        );

        if (!$resetUrl) {
            Log::error("createUrlUsing returned null for user: {$notifiable->id}");
            throw new \Exception('Password reset URL could not be generated.');
        }

        $siteName = \App\Models\Setting::get('site_name', 'E-Commerce Store');
        $logoUrl = \App\Models\Setting::get('logo_url', asset('assets/image/brand/logo.png'));
        $supportEmail = \App\Models\Setting::get('contact_email', 'support@example.com');

        return (new MailMessage)
            ->subject("Reset Your Password - {$siteName}")
            ->markdown('emails.reset-password', [
                'resetUrl' => $resetUrl,
                'user' => $notifiable,
                'siteName' => $siteName,
                'logoUrl' => $logoUrl,
                'supportEmail' => $supportEmail,
            ]);
    }
}
