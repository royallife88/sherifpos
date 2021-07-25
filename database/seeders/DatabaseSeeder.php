<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Employee;
use App\Models\JobType;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\System;
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
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('123456'),
            'is_superadmin' => 1,
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

        System::create(
            ['key' => 'sender_email', 'value' => 'admin@gmail.com', 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'sms_username', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'sms_password', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'sms_sender_name', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'time_format', 'value' => 24, 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'timezone', 'value' => 'Asia/Sakhalin', 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'language', 'value' => 'en', 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'logo', 'value' => '1626262996_download.png', 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'site_title', 'value' => 'sherifsalaby.tech', 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'developed_by', 'value' => '<a target="_blank" href="http://www.fiverr.com/derbari">Derbari</a>', 'created_by' => 1, 'date_and_time' => Carbon::now()],
            ['key' => 'help_page_content', 'value' => null, 'created_by' => 1, 'date_and_time' => Carbon::now()],

        );

        CustomerType::create([
            'name' => 'Retailer',
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

        Store::create([
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

        JobType::create(
            ['job_title' => 'Cashier', 'date_of_creation' => Carbon::now(), 'created_by' => 1],
            ['job_title' => 'Deliveryman', 'date_of_creation' => Carbon::now(), 'created_by' => 1]
        );


        //call the permission and currencies seeder
        $this->call([
            PermissionTableSeeder::class,
            CurrenciesTableSeeder::class
        ]);
    }
}
