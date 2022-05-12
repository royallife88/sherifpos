<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoneySafesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('money_safes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('name', 60);
            $table->unsignedBigInteger('currency_id');
            $table->string('type', 20);
            $table->string('bank_name', 60)->nullable();
            $table->string('IBAN', 60)->nullable();
            $table->string('bank_address', 60)->nullable();
            $table->unsignedBigInteger('credit_card_currency_id')->nullable();
            $table->unsignedBigInteger('bank_transfer_currency_id')->nullable();
            $table->text('add_money_users');
            $table->text('take_money_users');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->boolean('is_default')->default(0);
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
        Schema::dropIfExists('money_safes');
    }
}
