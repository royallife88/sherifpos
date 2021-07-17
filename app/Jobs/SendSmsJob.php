<?php

namespace App\Jobs;

use App\Models\System;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data = [];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;

        $username = System::getProperty('sms_username');
        $password = System::getProperty('sms_password');
        $sender_name = System::getProperty('sms_sender_name');


        $url = 'http://ep.securebulksms.com/developer/api/SendSMS/SubmitSMS/?Username=' . $username . '&Password=' . $password . '&SenderName=' . $sender_name . '&MobileNumbers=' . $data['mobile_number'] . '&Message=' . $data['message'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $response = json_decode(curl_exec($ch));

        $info = curl_getinfo($ch);
        if ($response->Status != 'OK') {
            $this->fail();
        }
    }
}
