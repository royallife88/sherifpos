<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendSaleDataMall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:sendSaleDataMall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send pos sales amount to mall api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mode = env('MALL_API_MODE', 'sandbox');
        $live_url = env('MALL_API_URL_LIVE', null);
        $sandbox_url = env('MALL_API_URL_SANDBOX', null);
        $token = env('MALL_API_TOKEN', null);
        $username = env('MALL_API_USERNAME', null);
        $password = env('MALL_API_PASSWORD', null);
        $store_name = env('STORE_NAME', null);
        $outlet_name = env('OUTLET_NAME', null);



        $url = null;
        if ($mode == 'sandbox') {
            $url = $sandbox_url;
        }
        if ($mode == 'live') {
            $url = $live_url;
        }
        $store = Store::where('name', $store_name)->first();
        if (!empty($url) && !empty($token) && !empty($username) && !empty($password) && !empty($store) && !empty($outlet_name)) {
            $today_date = Carbon::now()->format('Y-m-d');
            $sales_total = Transaction::where('type', 'sell')
                ->where('store_id', $store->id)
                ->whereDate('transaction_date', $today_date)
                ->select('final_total')
                ->get();

            $amount = !empty($sales_total->sum('final_total')) ? $sales_total->sum('final_total') : 0;
            $data_string = '[{"Date":"' . $today_date . '", "Outlet" :"' . $outlet_name . '", "Revenue" : ' . $amount . '}]';
            Log::info($data_string);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data_string,
                CURLOPT_HTTPHEADER => array(
                    'token: ' . $token,
                    'username: ' . $username,
                    'password: ' . $password,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;
            Log::info($response);
        }
    }
}
