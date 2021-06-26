<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('product_class_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->unsignedBigInteger('brand_id');
            $table->string('sku');
            $table->string('multiple_units')->nullable();
            $table->string('multiple_colors')->nullable();
            $table->string('multiple_sizes')->nullable();
            $table->string('multiple_grades')->nullable();
            $table->text('product_details')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('barcode_type');
            $table->string('manufacturing_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->integer('expiry_warning')->nullable();
            $table->integer('convert_status_expire')->nullable();
            $table->integer('alert_quantity')->nullable();
            $table->decimal('purchase_price', 15, 4);
            $table->decimal('sell_price', 15, 4);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->string('tax_method')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('discount')->nullable();
            $table->string('discount_start_date')->nullable();
            $table->string('discount_end_date')->nullable();
            $table->text('discount_customers')->nullable();
            $table->boolean('show_to_customer')->default(1);
            $table->text('show_to_customer_types')->nullable();
            $table->boolean('different_prices_for_stores')->default(0);
            $table->boolean('this_product_have_variant')->default(0);
            $table->enum('type', ['single', 'variable'])->default('single');
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
        Schema::dropIfExists('products');
    }
}
