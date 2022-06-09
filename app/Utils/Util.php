<?php

namespace App\Utils;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ReceivedInvoice;
use App\Models\ReceivedInvoicePayment;
use App\Models\System;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Util
{
    /**
     * This function unformats a number and returns them in plain eng format
     *
     * @param int $input_number
     *
     * @return float
     */
    public function num_uf($input_number, $currency_details = null)
    {
        $thousand_separator  = ',';
        $decimal_separator  = '.';

        $num = str_replace($thousand_separator, '', $input_number);
        $num = str_replace($decimal_separator, '.', $num);

        return (float)$num;
    }

    /**
     * This function formats a number and returns them in specified format
     *
     * @param int $input_number
     * @param boolean $add_symbol = false
     * @param object $business_details = null
     * @param boolean $is_quantity = false; If number represents quantity
     *
     * @return string
     */
    public function num_f($input_number, $add_symbol = false, $business_details = null, $is_quantity = false)
    {
        $thousand_separator = ',';
        $decimal_separator = '.';

        $currency_precision =  2;

        if ($is_quantity) {
            $currency_precision = 2;
        }

        $formatted = number_format($input_number, $currency_precision, $decimal_separator, $thousand_separator);

        if ($add_symbol) {
            $currency_symbol_placement = !empty($business_details) ? $business_details->currency_symbol_placement : session('business.currency_symbol_placement');
            $symbol = !empty($business_details->currency_symbol) ? $business_details->currency_symbol : session('currency')['symbol'];

            if ($currency_symbol_placement == 'after') {
                $formatted = $formatted . ' ' . $symbol;
            } else {
                $formatted = $symbol . ' ' . $formatted;
            }
        }

        return $formatted;
    }

    /**
     * Calculates percentage for a given number
     *
     * @param int $number
     * @param int $percent
     * @param int $addition default = 0
     *
     * @return float
     */
    public function calc_percentage($number, $percent, $addition = 0)
    {
        return ($addition + ($number * ($percent / 100)));
    }

    /**
     * Calculates base value on which percentage is calculated
     *
     * @param int $number
     * @param int $percent
     *
     * @return float
     */
    public function calc_percentage_base($number, $percent)
    {
        return ($number * 100) / (100 + $percent);
    }

    /**
     * Calculates percentage
     *
     * @param int $base
     * @param int $number
     *
     * @return float
     */
    public function get_percent($base, $number)
    {
        if ($base == 0) {
            return 0;
        }

        $diff = $number - $base;
        return ($diff / $base) * 100;
    }

    /**
     * Converts date in business format to mysql format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return strin
     */
    public function uf_date($date, $time = false)
    {
        $date_format = 'm/d/Y';
        $mysql_format = 'Y-m-d';
        if ($time) {
            if (System::getProperty('time_format') == 12) {
                $date_format = $date_format . ' h:i A';
            } else {
                $date_format = $date_format . ' H:i';
            }
            $mysql_format = 'Y-m-d H:i:s';
        }

        return !empty($date_format) ? Carbon::createFromFormat($date_format, $date)->format($mysql_format) : null;
    }

    /**
     * Converts time in business format to mysql format
     *
     * @param string $time
     * @return strin
     */
    public function uf_time($time)
    {
        $time_format = 'H:i';
        if (System::getProperty('time_format') == 12) {
            $time_format = 'h:i A';
        }
        return !empty($time_format) ? Carbon::createFromFormat($time_format, $time)->format('H:i') : null;
    }

    /**
     * Converts time in business format to mysql format
     *
     * @param string $time
     * @return strin
     */
    public function format_time($time)
    {
        $time_format = 'H:i';
        if (System::getProperty('time_format') == 12) {
            $time_format = 'h:i A';
        }
        return !empty($time) ? Carbon::createFromFormat('H:i:s', $time)->format($time_format) : null;
    }

    /**
     * Converts date in mysql format to business format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return strin
     */
    public function format_date($date, $show_time = false, $business_details = null)
    {
        $format = 'm/d/Y';
        if (!empty($show_time)) {
            $time_format = '';
            if ($time_format == 12) {
                $format .= ' h:i A';
            } else {
                $format .= ' H:i';
            }
        }

        return !empty($date) ? Carbon::createFromTimestamp(strtotime($date))->format($format) : null;
    }


    /**
     * Sends SMS notification.
     *
     * @param  array $data
     * @return void
     */
    public function sendSms($data)
    {
        $sms_settings = $data['sms_settings'];
        $request_data = [
            $sms_settings['send_to_param_name'] => $data['mobile_number'],
            $sms_settings['msg_param_name'] => $data['sms_body'],
        ];

        if (!empty($sms_settings['param_1'])) {
            $request_data[$sms_settings['param_1']] = $sms_settings['param_val_1'];
        }
        if (!empty($sms_settings['param_2'])) {
            $request_data[$sms_settings['param_2']] = $sms_settings['param_val_2'];
        }
        if (!empty($sms_settings['param_3'])) {
            $request_data[$sms_settings['param_3']] = $sms_settings['param_val_3'];
        }
        if (!empty($sms_settings['param_4'])) {
            $request_data[$sms_settings['param_4']] = $sms_settings['param_val_4'];
        }
        if (!empty($sms_settings['param_5'])) {
            $request_data[$sms_settings['param_5']] = $sms_settings['param_val_5'];
        }
        if (!empty($sms_settings['param_6'])) {
            $request_data[$sms_settings['param_6']] = $sms_settings['param_val_6'];
        }
        if (!empty($sms_settings['param_7'])) {
            $request_data[$sms_settings['param_7']] = $sms_settings['param_val_7'];
        }
        if (!empty($sms_settings['param_8'])) {
            $request_data[$sms_settings['param_8']] = $sms_settings['param_val_8'];
        }
        if (!empty($sms_settings['param_9'])) {
            $request_data[$sms_settings['param_9']] = $sms_settings['param_val_9'];
        }
        if (!empty($sms_settings['param_10'])) {
            $request_data[$sms_settings['param_10']] = $sms_settings['param_val_10'];
        }

        $client = new Client();

        if ($sms_settings['request_method'] == 'get') {
            $response = $client->get($sms_settings['url'] . '?' . http_build_query($request_data));
        } else {
            $response = $client->post($sms_settings['url'], [
                'form_params' => $request_data
            ]);
        }

        return $response;
    }


    /**
     * Generates unique token
     *
     * @param void
     *
     * @return string
     */
    public function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }


    /**
     * Uploads document to the server if present in the request
     * @param obj $request, string $file_name, string dir_name
     *
     * @return string
     */
    public function uploadFile($request, $file_name, $dir_name, $file_type = 'document')
    {
        //If app environment is demo return null
        if (config('app.env') == 'demo') {
            return null;
        }

        $uploaded_file_name = null;
        if ($request->hasFile($file_name) && $request->file($file_name)->isValid()) {

            //Check if mime type is image
            if ($file_type == 'image') {
                if (strpos($request->$file_name->getClientMimeType(), 'image/') === false) {
                    throw new \Exception("Invalid image file");
                }
            }

            if ($file_type == 'document') {
                if (!in_array($request->$file_name->getClientMimeType(), array_keys(config('constants.document_upload_mimes_types')))) {
                    throw new \Exception("Invalid document file");
                }
            }

            $new_file_name = time() . '_' . $request->$file_name->getClientOriginalName();
            if ($request->$file_name->storeAs($dir_name, $new_file_name)) {
                $uploaded_file_name = $new_file_name;
            }
        }

        return $uploaded_file_name;
    }

    /**
     * Checks whether mail is configured or not
     *
     * @return boolean
     */
    public function IsMailConfigured()
    {
        $is_mail_configured = false;

        if (
            !empty(env('MAIL_DRIVER')) &&
            !empty(env('MAIL_HOST')) &&
            !empty(env('MAIL_PORT')) &&
            !empty(env('MAIL_USERNAME')) &&
            !empty(env('MAIL_PASSWORD')) &&
            !empty(env('MAIL_FROM_ADDRESS'))
        ) {
            $is_mail_configured = true;
        }

        return $is_mail_configured;
    }


    /**
     * Retrieves user role name.
     *
     * @return string
     */
    public function getUserRoleName($user_id)
    {
        $user = User::findOrFail($user_id);

        $roles = $user->getRoleNames();

        $role_name = '';

        if (!empty($roles[0])) {
            $array = explode('#', $roles[0], 2);
            $role_name = !empty($array[0]) ? $array[0] : '';
        }
        return $role_name;
    }

    /**
     * Retrieves IP address of the user
     *
     * @return string
     */
    public function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function createDropdownHtml($array, $append_text = null)
    {
        $html = '';
        if (!empty($append_text)) {
            $html = '<option value="">' . $append_text . '</option>';
        }
        foreach ($array as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }

        return $html;
    }

    public function getPurchaseOrderStatusArray()
    {
        return [
            'draft' => __('lang.draft'),
            'sent_admin' => __('lang.sent_to_admin'),
            'sent_supplier' => __('lang.sent_to_supplier'),
            'received' => __('lang.received'),
            'pending' => __('lang.pending'),
            'partially_received' => __('lang.partially_received'),
        ];
    }
    public function getPaymentStatusArray()
    {
        return [
            'partial' => __('lang.partially_paid'),
            'paid' => __('lang.paid'),
            'pending' => __('lang.pay_later'),
        ];
    }

    public function getPaymentTypeArray()
    {
        return [
            'cash' => __('lang.cash'),
            'card' => __('lang.credit_card'),
            'bank_transfer' => __('lang.bank_transfer'),
            'cheque' => __('lang.cheque'),
            'money_transfer' => 'Money Transfer',
        ];
    }
    public function getPaymentTypeArrayForPos()
    {
        return [
            'cash' => __('lang.cash'),
            'card' => __('lang.credit_card'),
            'cheque' => __('lang.cheque'),
            'gift_card' => __('lang.gift_card'),
            'bank_transfer' => __('lang.bank_transfer'),
            'deposit' => __('lang.use_the_balance'),
            'paypal' => __('lang.paypal'),
        ];
    }

    /**
     * Gives a list of all currencies
     *
     * @return array
     */
    public function allCurrencies($exclude_array = [])
    {
        $query = Currency::select('id', DB::raw("concat(country, ' - ',currency, '(', code, ') ', symbol) as info"))
            ->orderBy('country');
        if (!empty($exclude_array)) {
            $query->whereNotIn('id', $exclude_array);
        }

        $currencies = $query->pluck('info', 'id');

        return $currencies;
    }

    /**
     * Gives a list of exchange rate currencies
     *
     * @return array
     */
    public function getCurrenciesExchangeRateArray($include_default = false)
    {
        $store_id = request()->store_id;

        $query = ExchangeRate::leftjoin('currencies', 'exchange_rates.received_currency_id', '=', 'currencies.id')
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', date('Y-m-d'));
            });
        if (!empty($store_id)) {
            $query->where('exchange_rates.store_id', $store_id);
        }
        $query->select('received_currency_id', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
            ->orderBy('country');
        $exchange_rate_currencies = $query->pluck('info', 'received_currency_id')->toArray();

        if (!empty($include_default)) {
            $default_currency_id = System::getProperty('currency');
            $default_currency = Currency::where('id', $default_currency_id)->select('id', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))->pluck('info', 'id')->toArray();

            $exchange_rate_currencies = $exchange_rate_currencies + $default_currency;
        }

        return $exchange_rate_currencies;
    }

    /**
     * list exchange rate currencies
     *
     * @return array
     */
    public function getExchangeRateCurrencies($include_default = false)
    {
        $currencies_obj = ExchangeRate::leftjoin('currencies', 'exchange_rates.received_currency_id', 'currencies.id')
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', date('Y-m-d'));
            })
            ->select('received_currency_id as currency_id', 'currencies.symbol', 'conversion_rate')
            ->orderBy('exchange_rates.created_at', 'asc')
            ->get();
        $currencies = [];

        $default_currency_id = System::getProperty('currency');
        if (!empty($default_currency_id)) {
            $default_currency = Currency::where('id', $default_currency_id)
                ->select('id as currency_id', 'symbol')
                ->first();

            $d['currency_id'] = $default_currency->currency_id;
            $d['symbol'] = $default_currency->symbol;
            $d['conversion_rate'] = 1;
            $d['is_default'] = true;
            $currencies[] = $d;
        }
        foreach ($currencies_obj as $cur_obj) {
            $currencies[] = ['currency_id' => $cur_obj->currency_id, 'symbol' => $cur_obj->symbol, 'conversion_rate' => $cur_obj->conversion_rate, 'is_default' => false];
        }

        return $currencies;
    }

    /**
     * get the exchange rate of the currency
     *
     * @param int $currency_id
     * @param int $store_id
     * @return void
     */
    public function getExchangeRateByCurrency($currency_id, $store_id = null)
    {
        $default_currency_id = System::getProperty('currency');

        if ($default_currency_id == $currency_id) {
            return 1;
        }

        $query = ExchangeRate::where('received_currency_id', $currency_id);
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }
        $exchange_rate = $query->first();
        if (!empty($exchange_rate)) {
            return $exchange_rate->conversion_rate;
        }
        return 1;
    }
    /**
     * Gives a list of all timezone with gmt offset
     *
     * @return array
     */
    public function allTimeZones()
    {
        $timezones = [];
        foreach (timezone_identifiers_list() as $key => $zone) {
            $timezone = timezone_open($zone);

            $datetime_eur = date_create("now", timezone_open("Europe/London"));
            $gmt_offset = timezone_offset_get($timezone, $datetime_eur);
            $offset = $this->convertToHoursMins($gmt_offset);
            if ($gmt_offset > 0) {
                $timezones[$zone] = $zone . ' (GMT +' . $offset . ')';
            } else if ($gmt_offset < 0) {
                $timezones[$zone] = $zone . ' (GMT ' . $offset . ')';
            } else {
                $timezones[$zone] = $zone . ' (GMT +00:00)';
            }
        }
        return $timezones;
    }


    function convertToHoursMins($time)
    {
        $hours = floor($time / 3600);
        $x = $time / 3600;
        $remain_nimutes = $x - floor($x);;
        $minutes = ($remain_nimutes * 60);
        return $hours . ':' . str_pad($minutes, 2, "0");;
    }
    /**
     * find user of sepcific role
     *
     * @return void
     */
    public function getTheUserByRole($role_name)
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->hasRole($role_name)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * generate random string of defined length
     *
     * @param int $length
     * @return string
     */
    function randString($length, $prefix = '')
    {
        $str = '';
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $prefix . $str;
    }

    /**
     * converty currency base on exchange rate
     *
     * @param float $amount
     * @param int $from_currency_id
     * @param int $to_currency_id
     * @param int $store_id
     * @return double
     */
    public function convertCurrencyAmount($amount, $from_currency_id, $to_currency_id, $store_id = null)
    {
        $amount = $this->num_uf($amount);
        $default_currency_id = System::getProperty('currency');
        $default_currency = Currency::find($default_currency_id);
        $from_currency_query = ExchangeRate::where('received_currency_id', $from_currency_id);
        if (!empty($store_id)) {
            $from_currency_query->where('store_id', $store_id);
        }
        $from_currency_exchange_rate = $from_currency_query->first();
        $to_currency_query = ExchangeRate::where('received_currency_id', $to_currency_id);
        if (!empty($store_id)) {
            $to_currency_query->where('store_id', $store_id);
        }
        $to_currency_exchange_rate = $to_currency_query->first();
        if (!empty($from_currency_exchange_rate) && !empty($to_currency_exchange_rate)) {
            $amount_to_base = $amount * $from_currency_exchange_rate->conversion_rate;
            $amount = $amount_to_base / $to_currency_exchange_rate->conversion_rate;
        } else {
            if ($to_currency_id == $default_currency_id && $from_currency_id == $default_currency_id) {
                $amount = $amount;
            } else if (!empty($from_currency_exchange_rate) && empty($to_currency_exchange_rate)) {
                $amount = $amount * $from_currency_exchange_rate->conversion_rate;
            } else if (empty($from_currency_exchange_rate) && !empty($to_currency_exchange_rate)) {
                $amount = $amount / $to_currency_exchange_rate->conversion_rate;
            } else {
                $amount = $amount;
            }
        }

        return $amount;
    }
}
