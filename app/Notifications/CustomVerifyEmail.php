<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        $frontendVerificationUrl = config('app.frontend_url') . '/verify-email?url=' . urlencode($verificationUrl);

        $siteName = \App\Models\Setting::get('site_name', 'E-Commerce Store');
        $logoUrl  = \App\Models\Setting::get('logo_url', asset('assets/image/brand/logo.png'));
        $supportEmail = \App\Models\Setting::get('contact_email', 'support@example.com');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Verify Your Email - Welcome to $siteName")
            ->markdown('emails.verify-email', [
                'verificationUrl' => $frontendVerificationUrl,
                'user' => $notifiable,
                'siteName' => $siteName,
                'logoUrl' => $logoUrl,
                'supportEmail' => $supportEmail,
            ]);
    }
}
