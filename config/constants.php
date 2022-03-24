<?php

return [

    /*
    |--------------------------------------------------------------------------
    | App Constants
    |--------------------------------------------------------------------------
    |List of all constants for the app
    */

    'langs' => [
        'en' => ['full_name' => 'English', 'short_name' => 'English'],
        'fr' => ['full_name' => 'French - Français', 'short_name' => 'French'],
        'ar' => ['full_name' => 'Arabic - العَرَبِيَّة', 'short_name' => 'Arabic'],
        'tr' => ['full_name' => 'Turkish - Türkçe', 'short_name' => 'Turkish'],
        'nl' => ['full_name' => 'Dutch - Dutch', 'short_name' => 'Dutch'],
        'ur' => ['full_name' => 'Urud - اردو', 'short_name' => 'Urud'],
        'hi' => ['full_name' => 'Hindi - हिंदी', 'short_name' => 'Hindi'],
        'fa' => ['full_name' => 'Persian - فارسی', 'short_name' => 'Persian'],
    ],
    'langs_rtl' => ['ar'],
    'non_utf8_languages' => ['ar', 'hi', 'ps'],

    'document_size_limit' => '1000000', //in Bytes,
    'image_size_limit' => '500000', //in Bytes

    'asset_version' => 52,

    'disable_expiry' => false,

    'disable_purchase_in_other_currency' => true,

    'iraqi_selling_price_adjustment' => false,

    'currency_precision' => 2, //Maximum 4
    'quantity_precision' => 2,  //Maximum 4

    'product_img_path' => 'img',

    'enable_sell_in_diff_currency' => false,
    'currency_exchange_rate' => 1,
    'orders_refresh_interval' => 600, //Auto refresh interval on Kitchen and Orders page in seconds,

    //Default date format to be used if session is not set. All valid formats can be found on https://www.php.net/manual/en/function.date.php
    'default_date_format' => 'm/d/Y',

    'administrator_emails' => env('ADMINISTRATOR_EMAIL'),
    'allow_registration' => env('ALLOW_REGISTRATION', true),
    'app_title' => env('APP_TITLE'),
    'mpdf_temp_path' => public_path('uploads/temp')
];
