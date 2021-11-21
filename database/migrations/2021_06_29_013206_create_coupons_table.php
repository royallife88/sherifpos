<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_code');
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('amount', 15, 4);
            $table->boolean('amount_to_be_purchase_checkbox');
            $table->decimal('amount_to_be_purchase', 15, 4)->nullable();
            $table->boolean('all_products');
            $table->text('product_ids');
            $table->text('customer_type_ids');
            $table->text('store_ids');
            $table->text('pct_data');
            $table->date('expiry_date')->nullable();
            $table->boolean('used')->default(0);
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('coupons');
    }
}
