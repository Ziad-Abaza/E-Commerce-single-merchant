<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {}

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/auth/reset-password?token={$token}&email=" . urlencode($notifiable->getEmailForPasswordReset());
        });

        Schema::defaultStringLength(191);
    }
}
