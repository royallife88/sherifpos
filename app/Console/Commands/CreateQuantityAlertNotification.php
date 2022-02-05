<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use App\Utils\NotificationUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateQuantityAlertNotification extends Command
{
    /**
     * All Utils instance.
     *
     */
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(NotificationUtil $notificationUtil)
    {
        parent::__construct();

        $this->notificationUtil = $notificationUtil;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:createQuantityAlertNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Quantity Alert Notification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = Product::leftjoin('product_stores', 'products.id', 'product_stores.product_id')
            ->select(DB::raw('SUM(qty_available) as qty'), 'products.*')
            ->havingRaw('qty < alert_quantity');

        $items = $query->groupBy('products.id')->get();

        $users = User::where('is_superadmin', 1)->get();

        foreach($items as $item){
            foreach ($users as $user) {
                $notification_data = [
                    'user_id' => $user->id,
                    'product_id' => $item->id,
                    'qty_available' => $item->qty,
                    'alert_quantity' => $item->alert_quantity,
                    'type' => 'quantity_alert',
                    'status' => 'unread',
                    'created_by' => 1,
                ];
                $notification_exist = Notification::where('user_id', $user->id)->where('type', 'quantity_alert')->where('product_id',$item->id )->where('status', 'unread')->first();
                if(empty($notification_exist)){
                    $this->notificationUtil->createNotification($notification_data);
                }
            }
        }
    }
}
