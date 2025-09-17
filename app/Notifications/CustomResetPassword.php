<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomResetPassword extends ResetPassword implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Get settings
        $siteName = \App\Models\Setting::get('site_name', 'Our Store');
        $logoUrl = \App\Models\Setting::get('logo_url', asset('assets/image/brand/logo.png'));
        $supportEmail = \App\Models\Setting::get('contact_email', 'support@example.com');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Reset Your Password - {$siteName}")
            ->markdown('emails.reset-password', [
                'url' => $url,
                'siteName' => $siteName,
                'logoUrl' => $logoUrl,
                'supportEmail' => $supportEmail,
            ]);
    }
}
