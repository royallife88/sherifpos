<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\StorePos;
use App\Models\System;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (session('language') == 'undefined') {
            $request->session()->put('language', null);
        }
        if (!$request->session()->has('user') || empty(session('user.store_id')) || empty(session('user.job_title')) || empty(session('user.pos_id')) || empty(session('language'))) {

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
                    'id' => $currency->id,
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

            if (empty(session('language'))) {
                $language = System::getProperty('language');

                if (empty($language)) {
                    $language = 'en';
                }
                $request->session()->put('language', $language);
            }

            $user = User::find(Auth::user()->id);
            $user_pos = StorePos::where('user_id', Auth::user()->id)->first();
            if (!empty($user_pos)) {
                $user->pos_id = $user_pos->id;
                $user->store_id = $user_pos->store_id;
            }

            $employee = $user->employee;
            if ($employee) {
                if (!empty($employee->job_type)) {
                    $user->job_title = $employee->job_type->job_title;
                } else {
                    $user->job_title = 'N/A';
                }
            }

            $request->session()->put('user', $user);
        }

        $system_mode = env('SYSTEM_MODE', 'pos');
        if (empty($system_mode)) {
            $system_mode = 'pos';
        }
        $request->session()->put('system_mode', $system_mode);


        $logo = System::getProperty('logo');
        if (empty($logo)) {
            $logo = 'sharifshalaby.png';
        }
        $request->session()->put('logo', $logo);


        return $next($request);
    }
}
