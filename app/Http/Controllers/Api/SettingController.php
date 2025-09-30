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
            return Setting::orderBy('group')
                ->orderBy('sort_order')
                ->get();
        });

        $formattedSettings = [];

        foreach ($settings as $setting) {
            $formattedSettings[$setting->key] = $setting->typed_value;

            // add key visibility
            $formattedSettings[$setting->key . '_visible'] = (bool) $setting->is_public;
        }

        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
        ]);
    }
}
