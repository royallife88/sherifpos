<?php

namespace App\Console\Commands;

use App\Models\CustomerImportantDate;
use App\Models\User;
use App\Utils\NotificationUtil;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateCustomerImportantDateNotification extends Command
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
    protected $signature = 'pos:createCustomerImportantDateNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create user notification for customer important dates';

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

        $customer_important_dates = CustomerImportantDate::whereDate('date', '>=', date('Y-m-d'))
            ->where('is_notified', false)
            ->get();

        foreach ($customer_important_dates as $important_date) {
            $date = Carbon::parse($important_date->date)->subDays($important_date->notify_before_days);
            if (Carbon::now()->format('Y-m-d') == $date->format('Y-m-d')) {
                foreach ($users as $user) {
                    $notification_data = [
                        'user_id' => $user->id,
                        'customer_id' => $important_date->customer_id,
                        'message' => $important_date->details,
                        'days' => $important_date->notify_before_days,
                        'type' => 'important_date',
                        'status' => 'unread',
                        'created_by' => 1,
                    ];
                    $this->notificationUtil->createNotification($notification_data);
                }

                $important_date->is_notified = true;
                $important_date->save();
            }
        }
    }
}
