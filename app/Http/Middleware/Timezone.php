<?php

namespace App\Http\Middleware;

use App\Models\System;
use Closure;
use Illuminate\Support\Facades\Auth;

class Timezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $timezone = config('app.timezone');

        $setting_timezone = System::getProperty('timezone');
        if (!empty($setting_timezone)) {
            $timezone = $setting_timezone;
        } else {
            $timezone = 'Asia/Qatar';
        }

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $next($request);
    }
}
