<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('code');
            $table->text('store_ids');
            $table->text('customer_type_ids');
            $table->text('product_ids');
            $table->text('pct_data');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_value', 15, 4)->default(0);
            $table->decimal('actual_sell_price', 15, 4)->default(0);
            $table->boolean('purchase_condition')->default(0);
            $table->decimal('purchase_condition_amount', 15, 4)->default(0);
            $table->boolean('product_condition')->default(0);
            $table->text('package_promotion_qty');
            $table->text('condition_product_ids');
            $table->text('pci_data')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('generate_barcode')->default(0);
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
        Schema::dropIfExists('sales_promotions');
    }
}
