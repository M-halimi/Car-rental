<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session()->get('locale', $request->cookie('locale', 'en'));

        if (in_array($locale, ['en', 'fr', 'ar'])) {
            app()->setLocale($locale);
        } else {
            app()->setLocale('en');
        }

        return $next($request);
    }
}
