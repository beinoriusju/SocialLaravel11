<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        // Retrieve the language from the session or use the default locale
        $locale = Session::get('language', config('app.locale'));
        App::setLocale($locale); // Set the application's locale

        return $next($request);
    }
}
