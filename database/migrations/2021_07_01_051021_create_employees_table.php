<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('store_id');
            $table->string('pass_string')->nullable();
            $table->string('employee_name');
            $table->date('date_of_start_working')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->unsignedBigInteger('job_type_id')->nullable();
            $table->string('mobile');
            $table->integer('annual_leave_per_year')->nullable();
            $table->integer('sick_leave_per_year')->nullable();
            $table->integer('number_of_days_any_leave_added')->nullable();
            $table->boolean('fixed_wage')->default('0');
            $table->decimal('fixed_wage_value')->default('0');
            $table->enum('payment_cycle', [
                'daily',
                'weekly',
                'bi-weekly',
                'monthly'
            ])->nullable();
            $table->boolean('commission')->default('0');
            $table->decimal('commission_value')->default('0');
            $table->enum('commission_type', [
                'sales',
                'profit'
            ])->nullable();
            $table->enum('commission_calculation_period', [
                'daily',
                'weekly',
                'one_month',
                'three_month',
                'six_month',
                'one_year'
            ])->nullable();
            $table->text('commissioned_products')->nullable();
            $table->text('commission_customer_types')->nullable();
            $table->text('commission_stores')->nullable();
            $table->text('commission_cashiers')->nullable();

            $table->string('working_day_per_week')->nullable();
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
