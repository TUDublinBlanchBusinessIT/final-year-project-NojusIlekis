<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'pt', 'pl', 'ro'];

        $locale = 'en';

        if (auth()->check()) {
            $userLocale = auth()->user()->preferred_language;

            if (in_array($userLocale, $supportedLocales, true)) {
                $locale = $userLocale;
            }
        } elseif (session()->has('locale')) {
            $sessionLocale = session('locale');

            if (in_array($sessionLocale, $supportedLocales, true)) {
                $locale = $sessionLocale;
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}