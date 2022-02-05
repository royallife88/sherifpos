<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Utils\NotificationUtil;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddStockDueNotify extends Command
{
    /**
     * All Utils instance.
     *
     */
    protected $notificationUtil;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:addStockDueNotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notify user for add stock due date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationUtil $notificationUtil)
    {
        parent::__construct();

        $this->notificationUtil = $notificationUtil;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = Transaction::where('type', 'add_stock')->where('payment_status', '!=', 'paid')->whereNotNull('due_date')->where('notify_me', 1)->get();

        foreach ($transactions as $transaction) {
            $warning_date = Carbon::parse($transaction->due_date)->subDays($transaction->notify_before_days);
            if (Carbon::now()->gt($warning_date) && Carbon::now()->lt(Carbon::parse($transaction->due_date))) {
                $days = Carbon::now()->diffInDays(Carbon::parse($transaction->due_date), true);
                $notification_data = [
                    'user_id' => $transaction->created_by,
                    'product_id' => null,
                    'transaction_id' => $transaction->id,
                    'qty_available' => 0,
                    'days' => $days,
                    'type' => 'add_stock_due',
                    'status' => 'unread',
                    'created_by' => 1,
                ];
                $this->notificationUtil->createNotification($notification_data);
            }
        }
    }
}
