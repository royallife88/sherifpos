<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionSellLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_sell_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id');
            $table->decimal('quantity', 15, 4);
            $table->decimal('quantity_returned', 15, 4)->default(0);
            $table->decimal('sell_price', 15, 4);
            $table->decimal('sub_total', 15, 4);
            $table->decimal('sub_total', 15, 4);
            $table->decimal('coupon_discount', 15, 4)->nullable();
            $table->string('coupon_discount_type')->nullable();
            $table->decimal('coupon_discount_amount', 15, 4)->nullable();
            $table->boolean('point_earned')->default(0);
            $table->boolean('point_redeemed')->default(0);

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
        Schema::dropIfExists('transaction_sell_lines');
    }
}
