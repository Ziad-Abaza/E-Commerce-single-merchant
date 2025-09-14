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
            'site_name' => 'E-Commerce',
            'site_description' => 'Your one-stop shop for quality products',
            'contact_email' => 'support@store.com',
            'theme_color' => 'blue',
            'logo_url' => '/images/logo.png',
            'products_per_page' => 15,
            'currency' => 'EGP',
            'free_shipping_threshold' => 0,
        ];

        $finalSettings = array_merge($defaults, $formattedSettings->toArray());

        return response()->json([
            'success' => true,
            'data' => $finalSettings,
        ]);
    }
}
