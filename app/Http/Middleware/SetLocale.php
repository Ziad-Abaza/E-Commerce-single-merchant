<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['en', 'ar']);

        $locale = $this->determineLocale($request, $supportedLocales);

        app()->setLocale($locale);

        return $next($request);
    }

    private function determineLocale(Request $request, array $supportedLocales): string
    {
        if ($request->has('locale') && in_array($request->input('locale'), $supportedLocales)) {
            Session::put('locale', $request->input('locale'));
            return $request->input('locale');
        }

        if (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales)) {
            return Session::get('locale');
        }

        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE', ''), 0, 2);
        if ($browserLocale && in_array($browserLocale, $supportedLocales)) {
            return $browserLocale;
        }

        return in_array(config('app.locale'), $supportedLocales) ? config('app.locale') : $supportedLocales[0];
    }
}
