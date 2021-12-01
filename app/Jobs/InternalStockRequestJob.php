<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InternalStockRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data = [];
    protected $from = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $from)
    {
        $this->data = $data;
        $this->from = $from;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        $from = $this->from;
        Mail::send('notification_template.plain_html', ['content' => $this->data['content']], function ($message) use ($data, $from) {
            $message->from($from)->to($data["email"], $data["email"])
                ->subject($data["subject"]);
        });
    }
}
