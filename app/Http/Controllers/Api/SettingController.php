<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Get all public settings for frontend
     */
    public function index()
    {
        $settings = Cache::remember('public_settings', 300, function () {
            return Setting::public()->orderBy('group')->orderBy('sort_order')->get();
        });

        $formattedSettings = $settings->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->typed_value];
        });

        $defaults = [
            // General Settings
            'site_name' => 'E-Commerce Store',
            'site_description' => 'Your one-stop shop for quality products',
            'maintenance_mode' => false,
            'address' => 'EGYPT, Alexandria, al-Ajami',
            'business_hours' => 'Mon-Fri: 9am-5pm',

            // Contact Settings
            'contact_email' => 'contact@example.com',
            'contact_phone' => '+1 (234) 567-8900',
            'whatsapp_number' => '+1 (234) 567-8900',
            'facebook_url' => 'https://www.facebook.com/example',
            'twitter_url' => 'https://twitter.com/example',
            'instagram_url' => 'https://www.instagram.com/example',
            'youtube_url' => 'https://www.youtube.com/channel/example',
            'tiktok_url' => 'https://www.tiktok.com/@example',

            // Appearance Settings
            'theme_color' => 'blue',
            'logo_url' => '/images/default-logo.webp',
            'products_per_page' => 12,

            // Email Settings
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'email_notifications' => true,

            // Payment Settings
            'currency' => 'USD',
            'tax_rate' => 0.10,
            'free_shipping_threshold' => 100,

            // Security Settings
            'max_login_attempts' => 5,
            'session_timeout' => 120,
            'require_email_verification' => true,
        ];


        $finalSettings = array_merge($defaults, $formattedSettings->toArray());

        return response()->json([
            'success' => true,
            'data' => $finalSettings,
        ]);
    }
}
