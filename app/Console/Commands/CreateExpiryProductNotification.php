<?php

namespace App\Console\Commands;

use App\Models\AddStockLine;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\User;
use App\Utils\NotificationUtil;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateExpiryProductNotification extends Command
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
    protected $signature = 'pos:createExpiryProductNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create expiry notification for products';

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
        $users = User::get();

        $add_stock_lines = AddStockLine::leftjoin('transactions', 'add_stock_lines.transaction_id', 'transactions.id')
            ->select(
                'add_stock_lines.id',
                'transactions.id as transaction_id',
                'transactions.store_id',
                'product_id',
                'variation_id',
                'expiry_date',
                'expiry_warning',
                'convert_status_expire',
                DB::raw('SUM(quantity - quantity_sold) as remaining_qty')
            )
            ->having('remaining_qty', '>', 0)
            ->groupBy('add_stock_lines.id')
            ->get();

        foreach ($add_stock_lines as $item) {
            if (!empty($item->expiry_date) && !empty($item->expiry_warning)) {
                $warning_date = Carbon::parse($item->expiry_date)->subDays($item->expiry_warning);
                if (Carbon::now()->gt($warning_date) && Carbon::now()->lt(Carbon::parse($item->expiry_date))) {
                    $days = Carbon::now()->diffInDays(Carbon::parse($item->expiry_date), true);
                    foreach ($users as $user) {
                        $notification_data = [
                            'user_id' => $user->id,
                            'product_id' => $item->product_id,
                            'qty_available' => $item->qty,
                            'days' => $days,
                            'type' => 'expiry_alert',
                            'status' => 'unread',
                            'created_by' => 1,
                        ];
                        $this->notificationUtil->createNotification($notification_data);
                    }
                } else if (Carbon::now()->gt(Carbon::parse($item->expiry_date))) {
                    $days = Carbon::parse($item->expiry_date)->diffInDays(Carbon::now(), true);
                    foreach ($users as $user) {
                        $notification_data = [
                            'user_id' => $user->id,
                            'product_id' => $item->product_id,
                            'qty_available' => $item->qty,
                            'days' => $days,
                            'type' => 'expired',
                            'status' => 'unread',
                            'created_by' => 1,
                        ];
                        $this->notificationUtil->createNotification($notification_data);
                    }
                }
            }

            //change status to expired qunatity
            if (!empty($item->expiry_date) && !empty($item->convert_status_expire)) {
                $expired_date = Carbon::parse($item->expiry_date)->subDays($item->convert_status_expire)->format('Y-m-d');
                if (Carbon::now()->format('Y-m-d') == $expired_date) {
                    $ps = ProductStore::where('product_stores.product_id', $item->product_id)
                        ->where('product_stores.variation_id', $item->variation_id)
                        ->where('product_stores.store_id', $item->store_id)
                        ->first();
                    $ps->expired_qauntity = $ps->expired_qauntity + $item->remaining_qty;
                    $ps->save();
                    $item->update(['expired_qauntity' => $item->remaining_qty]);
                }
            }
        }
    }
}
