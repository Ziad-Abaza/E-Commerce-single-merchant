<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class CustomVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // original url
        $originalUrl = $this->verificationUrl($notifiable);

        // frontend url
        $frontendUrl = config('app.frontend_url', 'https://online-shop.cbatu.com');
        $parsed = parse_url($originalUrl);

        // get expires and signature
        parse_str($parsed['query'] ?? '', $query);
        $expires = $query['expires'] ?? null;
        $signature = $query['signature'] ?? null;

        if (!$expires || !$signature) {
            throw new \Exception('Missing expires or signature in verification URL');
        }

        // final url with expires and signature
        $vueUrl = "{$frontendUrl}/email/verify/{$notifiable->getKey()}/{$this->verificationHash($notifiable)}";
        $finalUrl = $vueUrl . "?expires={$expires}&signature={$signature}";

        // Get settings
        $siteName = \App\Models\Setting::get('site_name', 'E-Commerce Store');
        $logoUrl  = \App\Models\Setting::get('logo_url', asset('assets/image/brand/logo.png'));
        $supportEmail = \App\Models\Setting::get('contact_email', 'support@example.com');

        return (new MailMessage)
            ->subject("Verify Your Email - Welcome to $siteName")
            ->markdown('emails.verify-email', [
                'verificationUrl' => $finalUrl,
                'user' => $notifiable,
                'siteName' => $siteName,
                'logoUrl' => $logoUrl,
                'supportEmail' => $supportEmail,
            ]);
    }

    private function verificationHash($notifiable)
    {
        return sha1($notifiable->getEmailForVerification());
    }
}
