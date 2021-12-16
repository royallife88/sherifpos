<?php

namespace App\Console\Commands;

use App\Models\System;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeSystemType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:changeSystemType {type : pos or restaurant}';

    /**
     * The console Change the system typeRes  i.e. POS, POS+Restaurant
     *
     * @var string
     */
    protected $description = 'Change the system type, i.e. POS, POS+Restaurant';

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
        $type = $this->argument('type');
        if (!empty($type)) {
            System::updateOrCreate(
                ['key' => 'system_type'],
                ['value' => $type, 'date_and_time' => Carbon::now(), 'created_by' => 1]
            );
        }
        echo 'System type changed to ' . $type;
    }
}
