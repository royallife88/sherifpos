<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('pos:createCustomerImportantDateNotification')
            ->daily();
        $schedule->command('pos:createQuantityAlertNotification')
            ->daily();
        $schedule->command('pos:createExpiryProductNotification')
            ->daily();
        $schedule->command('pos:addStockDueNotify')
            ->daily();
        $schedule->command('pos:expenseDueNotify')
            ->daily();
        $schedule->command('pos:reverseBlockQuantity')
            ->daily();
        $schedule->command('pos:changeQuotationStatusToExpire')
            ->daily();
        $schedule->command('pos:sendSaleDataMall')
            ->dailyAt('23:55')->timezone('Asia/Qatar');
        $schedule->command('queue:work')
            ->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
