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
            $table->enum('transaction_type', ['initial', 'sell', 'transfer', 'refund', 'add_stock', 'cash_in', 'cash_out', 'expense', 'sell_return', 'closing_cash', 'wages_and_compensation'])->nullable();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('transaction_payment_id')->nullable();
            $table->string('source_type', 25)->nullable();
            $table->unsignedBigInteger('source_id')->nullable()->comment('Other users in the system as source.');
            $table->unsignedBigInteger('referenced_id')->nullable()->comment('used for cash in and cash out.');
            $table->text('notes')->nullable();
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
