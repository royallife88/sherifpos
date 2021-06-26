<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\System;
use Closure;
use Illuminate\Http\Request;

class SetSessionData
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
        if (!$request->session()->has('user')) {

            $currency_setting = System::getProperty('currency');
            $currency_data = [
                'name' => 'Qatari Riyals',
                'symbol' => 'QR'
            ];

            // if (!empty($currency_setting)) {
            //     $currency = Currency::find($currency_setting);
            //     $currency_data = [
            //         'id' => $currency->id,
            //         'name' => $currency->name,
            //         'symbol' => $currency->symbol
            //     ];
            // }
            $request->session()->put('currency', $currency_data);
        }

        return $next($request);
    }
}
