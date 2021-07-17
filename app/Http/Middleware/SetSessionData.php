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

            $currency_id = System::getProperty('currency');
            if (empty($currency_id)) {
                $currency_data = [
                    'country' => 'Qatar',
                    'symbol' => 'QR',
                    'decimal_separator' => '.',
                    'thousand_separator' => ',',
                    'currency_precision' => 2,
                    'currency_symbol_placement' => 'before',
                ];
            } else {
                $currency = Currency::find($currency_id);
                $currency_data = [
                    'country' => $currency->country,
                    'code' => $currency->code,
                    'symbol' => $currency->symbol,
                    'decimal_separator' => '.',
                    'thousand_separator' => ',',
                    'currency_precision' => 2,
                    'currency_symbol_placement' => 'before',
                ];
            }


            $request->session()->put('currency', $currency_data);

            if(empty(session('language'))){
                $language = System::getProperty('language');

                if (empty($language)) {
                    $language = 'en';
                }
                $request->session()->put('language', $language);
            }
        }

        return $next($request);
    }
}
