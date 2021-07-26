<?php

namespace App\Console\Commands;

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
        $query = Product::leftjoin('product_stores', 'products.id', 'product_stores.product_id')
            ->select(DB::raw('SUM(qty_available) as qty'), 'products.*');

        $items = $query->groupBy('product_stores.variation_id')->get();

        $users = User::get();

        foreach ($items as $item) {
            if (!empty($item->expiry_date) && !empty($item->expiry_warning)) {
                $warning_date = Carbon::parse($item->expiry_date)->subDays($item->expiry_warning);
                if (Carbon::now()->gt($warning_date) && Carbon::now()->lt(Carbon::parse($item->expiry_date))) {
                    $days = Carbon::now()->diffInDays(Carbon::parse($item->expiry_date), true);
                    foreach ($users as $user) {
                        $notification_data = [
                            'user_id' => $user->id,
                            'product_id' => $item->id,
                            'qty_available' => $item->qty,
                            'days' => $days,
                            'type' => 'expiry_alert',
                            'status' => 'unread',
                            'created_by' => 0,
                        ];
                        $this->notificationUtil->createNotification($notification_data);
                    }
                } else if (Carbon::now()->gt(Carbon::parse($item->expiry_date))) {
                    $days = Carbon::parse($item->expiry_date)->diffInDays(Carbon::now(), true);
                    foreach ($users as $user) {
                        $notification_data = [
                            'user_id' => $user->id,
                            'product_id' => $item->id,
                            'qty_available' => $item->qty,
                            'days' => $days,
                            'type' => 'expired',
                            'status' => 'unread',
                            'created_by' => 0,
                        ];
                        $this->notificationUtil->createNotification($notification_data);
                    }
                }
            }

            //change status to expired qunatity
            if (!empty($item->expiry_date) && !empty($item->convert_status_expire)) {
                $expired_date = Carbon::parse($item->expiry_date)->subDays($item->convert_status_expire)->format('Y-m-d');
                if (Carbon::now()->format('Y-m-d') == $expired_date) {
                    $product_stores = Product::leftjoin('variations', 'products.id', 'variations.product_id')
                        ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id')
                        ->where('product_stores.product_id', $item->id)
                        ->select('product_stores.*')
                        ->groupBy('product_stores.id')->get();
                    foreach ($product_stores as $product) {
                        $ps = ProductStore::find($product->id);
                        $ps->expired_qauntity = $ps->expired_qauntity + $ps->qty_available;
                        $ps->qty_available = 0;
                        $ps->save();
                    }
                }
            }
        }
    }
}
