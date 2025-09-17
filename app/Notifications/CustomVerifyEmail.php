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
        // $verificationUrl = $this->verificationUrl($notifiable); // هذا الرابط الأصلي

        // ننشئ رابطًا إلى واجهة Vue
        $frontendUrl = config('app.frontend_url', 'https://online-shop.cbatu.com'); // مثلاً
        $verifyRoute = "/email/verify/{$notifiable->getKey()}/{$this->verificationHash($notifiable)}";
        $verificationUrl = $frontendUrl . $verifyRoute;

        $siteName = \App\Models\Setting::get('site_name', 'E-Commerce Store');
        $logoUrl  = \App\Models\Setting::get('logo_url', asset('assets/image/brand/logo.png'));
        $supportEmail = \App\Models\Setting::get('contact_email', 'support@example.com');

        return (new MailMessage)
            ->subject("Verify Your Email - Welcome to $siteName")
            ->markdown('emails.verify-email', [
                'verificationUrl' => $verificationUrl,
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
