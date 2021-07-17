<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data = [];
    protected $files = [];
    protected $from = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $files, $from)
    {
        $this->data = $data;
        $this->files = $files;
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
        $files = $this->files;
        $from = $this->from;

        Mail::send('email.partials.email_template', $this->data, function ($message) use ($data, $files, $from) {
            $message->from($from)->to($data["email"], $data["email"])
                ->subject($data["subject"]);

            foreach ($files as $file) {
                $message->attach($file);
            }
        });
    }
}
