<?php

namespace Database\Seeders;

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
        // \App\Models\User::factory(10)->create();
        $user_data = [
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('123456'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $user = User::create($user_data);

        //call the permission seeder
        $this->call([
            PermissionTableSeeder::class
        ]);
    }
}
