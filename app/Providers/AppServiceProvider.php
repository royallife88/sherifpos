<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\System;
use App\Models\Transaction;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Blade directive to format number into required format.
        Blade::directive('num_format', function ($expression) {
            $currency_precision =  2;
            return "number_format($expression,  $currency_precision, '.', ',')";
        });

        //Blade directive to convert.
        Blade::directive('format_date', function ($date = null) {
            if (!empty($date)) {
                return "Carbon\Carbon::createFromTimestamp(strtotime($date))->format('m/d/Y')";
            } else {
                return null;
            }
        });

        //Blade directive to convert.
        Blade::directive('format_time', function ($date) {
            if (!empty($date)) {
                $time_format = 'h:i A';
                if (System::getProperty('time_format') == 24) {
                    $time_format = 'H:i';
                }
                return "\Carbon\Carbon::createFromTimestamp(strtotime($date))->format('$time_format')";
            } else {
                return null;
            }
        });

        Blade::directive('format_datetime', function ($date) {
            if (!empty($date)) {
                $time_format = 'h:i A';
                if (System::getProperty('time_format') == 24) {
                    $time_format = 'H:i';
                }

                return "\Carbon\Carbon::createFromTimestamp(strtotime($date))->format('m/d/Y ' . '$time_format')";
            } else {
                return null;
            }
        });

        //Blade directive to format currency.
        Blade::directive('format_currency', function ($number) {
            return '<?php
            $formated_number = "";
            if (session("currency")["currency_symbol_placement"] == "before") {
                $formated_number .= session("currency")["symbol"] . " ";
            }
            $formated_number .= number_format((float) ' . $number . ', session("currency")["currency_precision"] , session("currency")["decimal_separator"], session("currency")["thousand_separator"]);

            if (session("currency")["currency_symbol_placement"] == "after") {
                $formated_number .= " " . session("currency")["symbol"];
            }
            echo $formated_number; ?>';
        });
        //Blade directive to return appropiate class according to attendance status
        Blade::directive('attendance_status', function ($status) {
            return "<?php if($status == 'late'){
                    echo 'badge-warning';
                }elseif($status == 'on_leave'){
                    echo 'badge-danger';
                }elseif ($status == 'present') {
                    echo 'badge-success';
                }?>";
        });

        //Blade directive to convert.
        Blade::directive('replace_space', function ($string) {
            return "str_replace(' ', '_', $string)";
        });
    }
}
