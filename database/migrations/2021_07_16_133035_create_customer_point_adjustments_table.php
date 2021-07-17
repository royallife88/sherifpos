<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPointAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_point_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('current_balance', 15, 4)->default(0);
            $table->decimal('add_new_balance', 15, 4)->default(0);
            $table->decimal('new_balance', 15, 4)->default(0);
            $table->text('notes')->nullable();
            $table->dateTime('date_and_time');
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('customer_point_adjustments');
    }
}
