<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_systems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referred_id');
            $table->string('referred_type');
            $table->unsignedBigInteger('referred_by');
            $table->string('type');
            $table->string('source_type', 60)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('payment_status', 60)->nullable();
            $table->string('method', 60)->nullable();
            $table->decimal('amount', 15, 4)->default(0);
            $table->string('paid_on', 60)->nullable();
            $table->string('ref_number', 60)->nullable();
            $table->string('bank_deposit_date', 60)->nullable();
            $table->string('bank_name', 60)->nullable();
            $table->decimal('loyalty_points', 15, 4)->default(0);
            $table->unsignedBigInteger('gift_card_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('discount_type', 60)->nullable();
            $table->decimal('discount', 15, 4)->default(0);
            $table->string('discount_expiry', 60)->nullable();
            $table->text('product_ids')->nullable();
            $table->text('pct_data')->nullable();
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
        Schema::dropIfExists('reward_systems');
    }
}
