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
            $table->string('name', 25);
            $table->text('translations')->nullable();
            $table->unsignedBigInteger('product_class_id')->nullable();
            $table->foreign('product_class_id')->references('id')->on('product_classes')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('sku');
            $table->string('multiple_units')->nullable();
            $table->string('multiple_colors')->nullable();
            $table->string('multiple_sizes')->nullable();
            $table->string('multiple_grades')->nullable();
            $table->boolean('is_service')->default(0);
            $table->text('product_details')->nullable();
            $table->string('barcode_type');
            $table->integer('alert_quantity')->nullable();
            $table->unsignedBigInteger('alert_quantity_unit_id')->nullable();
            $table->decimal('other_cost', 15, 4)->default(0);
            $table->decimal('purchase_price', 15, 4);
            $table->decimal('sell_price', 15, 4);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->string('tax_method')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('discount')->nullable();
            $table->string('discount_start_date')->nullable();
            $table->string('discount_end_date')->nullable();
            $table->text('discount_customer_types')->nullable();
            $table->text('discount_customers')->nullable();
            $table->boolean('show_to_customer')->default(1);
            $table->text('show_to_customer_types')->nullable();
            $table->boolean('different_prices_for_stores')->default(0);
            $table->boolean('this_product_have_variant')->default(0);
            $table->enum('type', ['single', 'variable'])->default('single');
            $table->boolean('active')->default(1);
            $table->boolean('price_based_on_raw_material')->default(0);
            $table->boolean('is_raw_material')->default(0);
            $table->boolean('automatic_consumption')->default(0);
            $table->boolean('buy_from_supplier')->default(0);
            $table->unsignedBigInteger('edited_by')->nullable();
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
