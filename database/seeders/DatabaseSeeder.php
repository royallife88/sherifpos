<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Employee;
use App\Models\JobType;
use App\Models\MoneySafe;
use App\Models\ProductClass;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\System;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user_data = [
            'name' => 'superadmin',
            'email' => 'superadmin@sherifshalaby.tech',
            'password' => Hash::make('123456'),
            'is_superadmin' => 1,
            'is_admin' => 0,
            'is_detault' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $user = User::create($user_data);

        $employee_data = [
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'date_of_start_working' => Carbon::now(),
            'date_of_birth' => '1995-02-03',
            'annual_leave_per_year' => '10',
            'sick_leave_per_year' => '10',
            'mobile' => '123456789',
        ];

        Employee::create($employee_data);

        $user_data = [
            'name' => 'Admin',
            'email' => 'admin@sherifshalaby.tech',
            'password' => Hash::make('123456'),
            'is_superadmin' => 0,
            'is_admin' => 1,
            'is_detault' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $user = User::create($user_data);

        $employee_data = [
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'date_of_start_working' => Carbon::now(),
            'date_of_birth' => '1995-02-03',
            'annual_leave_per_year' => '10',
            'sick_leave_per_year' => '10',
            'mobile' => '123456789',
        ];

        Employee::create($employee_data);


        $modules = User::modulePermissionArray();
        $module_settings = [];
        foreach ($modules as $key => $value) {
            $module_settings[$key] = 1;
        }
        System::insert(
            [
                ['key' => 'sender_email', 'value' => 'admin@gmail.com', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'sms_username', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'sms_password', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'sms_sender_name', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'time_format', 'value' => 24, 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'timezone', 'value' => 'Asia/Qatar', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'language', 'value' => 'en', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'logo', 'value' => 'sharifshalaby.png', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'site_title', 'value' => 'sherifsalaby.tech', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'developed_by', 'value' => '<a target="_blank" href="http://www.fiverr.com/derbari">Derbari</a>', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'help_page_content', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'invoice_lang', 'value' => 'en', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'system_type', 'value' => 'pos', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'module_settings', 'value' => json_encode($module_settings), 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'tutorial_guide_url', 'value' => 'https://pos.sherifshalaby.tech', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'show_the_window_printing_prompt', 'value' => '1', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'enable_the_table_reservation', 'value' => '1', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['key' => 'currency', 'value' => '119', 'created_by' => 1, 'date_and_time' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]

        );

        CustomerType::create([
            'name' => 'Walk in',
            'created_by' => 1,
        ]);

        Customer::create([
            'name' => 'Walk-in-customer',
            'customer_type_id' => 1,
            'mobile_number' => '12345678',
            'address' => '',
            'email' => null,
            'is_default' => 1,
            'created_by' => 1,
        ]);

        CustomerType::create([
            'name' => 'Retailer',
            'created_by' => 1,
        ]);

        $store = Store::create([
            'name' => 'Default Store',
            'location' => '',
            'phone_number' => '',
            'email' => '',
            'manager_name' => 'superadmin',
            'manager_mobile_number' => '',
            'details' => '',
            'created_by' => 1
        ]);

        StorePos::create([
            'name' => 'Default',
            'store_id' => 1,
            'user_id' => 1,
            'created_by' => 1
        ]);

        MoneySafe::create([
            'name' => 'Bank Safe',
            'store_id' => $store->id,
            'currency_id' => 119,
            'type' => 'bank',
            'add_money_users' => [],
            'take_money_users' => [],
            'created_by' => 1,
        ]);

        JobType::insert(
            [
                ['job_title' => 'Cashier', 'date_of_creation' => Carbon::now(), 'created_by' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['job_title' => 'Deliveryman', 'date_of_creation' => Carbon::now(), 'created_by' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            ]
        );
        if (env('SYSTEM_MODE') == 'restaurant') {
            JobType::insert(
                [
                    ['job_title' => 'Chef', 'date_of_creation' => Carbon::now(), 'created_by' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ]
            );
        }

        if (env('SYSTEM_MODE') == 'pos' || env('SYSTEM_MODE') == 'supermarket') {
            Unit::insert([
                ['name' => 'g', 'is_active' => 1],
                ['name' => 'L', 'is_active' => 1],
                ['name' => 'cm', 'is_active' => 1],
                ['name' => 'Piece', 'is_active' => 1],
                ['name' => 'Carton', 'is_active' => 1],
            ]);
        }

        if (env('SYSTEM_MODE') == 'restaurant') {
            Unit::insert([
                ['name' => 'Piece', 'is_active' => 1],
                ['name' => 'One Person', 'is_active' => 1],
                ['name' => 'Two Person', 'is_active' => 1],
            ]);
            ProductClass::insert([
                ['name' => 'Extras'],
            ]);
        }

        //call the permission and currencies seeder
        $this->call([
            PermissionTableSeeder::class,
            CurrenciesTableSeeder::class
        ]);
    }
}
