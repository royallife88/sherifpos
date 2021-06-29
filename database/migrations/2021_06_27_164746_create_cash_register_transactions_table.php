<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashRegisterTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_register_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_register_id');
            $table->decimal('amount', 15, 4);
            $table->string('pay_method');
            $table->enum('type', ['debit', 'credit'])->nullable();
            $table->enum('transaction_type', ['initial', 'sell', 'transfer', 'refund'])->nullable();
            $table->unsignedBigInteger('transaction_id');
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
        Schema::dropIfExists('cash_register_transactions');
    }
}
