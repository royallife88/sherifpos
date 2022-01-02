<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:clearCache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $system_urls = [
            'https://pos.sherifshalaby.tech',
            'https://hawana.sherifshalaby.tech',
            'https://twaaq.sherifshalaby.tech',
            'https://rm.sherifshalaby.tech',
            'https://cy.sherifshalaby.tech',
            'https://as.sherifshalaby.tech',
            'https://nana.sherifshalaby.tech',
            'https://elaga.sherifshalaby.tech',
            'https://ha.sherifshalaby.tech',
            'https://pos.g.sherifshalaby.tech',
            'https://pos.r.sherifshalaby.tech',
            'https://g.evet.sherifshalaby.tech',
            'https://r.fries.sherifshalaby.tech',
            'https://s.albutul.sherifshalaby.tech',
            'https://s.albutul.sherifshalaby.tech',
            'https://g.rakeya.sherifshalaby.tech',
            'https://r.mortaja.sherifshalaby.tech',
            'https://s.panda.sherifshalaby.tech',
        ];


        foreach ($system_urls as $url) {
            Http::get($url . '/clear-cache');
        }
        echo 'done';
    }
}
