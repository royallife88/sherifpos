<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_category_id')->nullable();
            $table->string('name', 30);
            $table->string('company_name', 30)->nullable();
            $table->string('vat_number', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_number', 30)->nullable();
            $table->string('address', 60);
            $table->string('city', 30)->nullable();
            $table->string('state', 30)->nullable();
            $table->string('country', 30)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->text('products')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
