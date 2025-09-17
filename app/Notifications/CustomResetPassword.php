<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        if (!$this->token || !$notifiable->getEmailForPasswordReset()) {
            throw new \Exception('Missing token or email for password reset.');
        }

        $frontendUrl = rtrim(config('app.frontend_url', 'https://online-shop.cbatu.com'), '/');
        $resetRoute = "/auth/reset-password";

        $finalUrl = "{$frontendUrl}{$resetRoute}?token={$this->token}&email=" . urlencode($notifiable->getEmailForPasswordReset());

        $siteName = \App\Models\Setting::get('site_name', 'E-Commerce Store');
        $logoUrl  = \App\Models\Setting::get('logo_url', asset('assets/image/brand/logo.png'));
        $supportEmail = \App\Models\Setting::get('contact_email', 'support@example.com');

        return (new MailMessage)
            ->subject("Reset Your Password - {$siteName}")
            ->markdown('emails.reset-password', [
                'resetUrl' => $finalUrl,
                'user' => $notifiable,
                'siteName' => $siteName,
                'logoUrl' => $logoUrl,
                'supportEmail' => $supportEmail,
            ]);
    }
}
