<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiningTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dining_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dining_room_id');
            $table->string('name')->unique();
            $table->string('status')->default('available');
            $table->string('customer_mobile_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('date_and_time')->nullable();
            $table->unsignedBigInteger('current_transaction_id')->nullable();
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
        Schema::dropIfExists('dining_tables');
    }
}
