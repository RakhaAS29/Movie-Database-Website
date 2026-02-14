<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Check if lang parameter exists in URL
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            
            // Validate locale (only allow 'en' or 'id')
            if (in_array($locale, ['en', 'id'])) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        } 
        // Check if locale is stored in session
        elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        // Default to English
        else {
            App::setLocale('en');
        }

        return $next($request);
    }
}