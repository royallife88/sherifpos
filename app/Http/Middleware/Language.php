<?php

namespace App\Http\Middleware;

use Closure;
use App;
use App\Models\System;
use Illuminate\Http\Request;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = config('app.locale');
        if (!empty(session('language'))) {
            $locale = session('language');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
