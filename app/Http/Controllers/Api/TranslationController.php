<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    public function getTranslations(Request $request, string $locale)
    {
        $supportedLocales = config('app.supported_locales', ['en', 'ar']);
        $fallback = config('app.fallback_locale', 'en');

        // Validate locale
        if (!in_array($locale, $supportedLocales)) {
            $locale = $fallback;
        }

        $langPath = lang_path($locale);
        $translations = [];

        if (File::exists($langPath)) {
            foreach (File::files($langPath) as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $fileTranslations = require $file->getPathname();

                $translations[$filename] = $fileTranslations;
            }
        }

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'fallback' => $fallback,
            'translations' => $translations,
        ]);
    }
}
