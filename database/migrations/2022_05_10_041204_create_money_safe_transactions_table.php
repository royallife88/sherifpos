<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoneySafeTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('money_safe_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('money_safe_id');
            $table->string('source_type')->nullable();
            $table->string('transaction_date');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('job_type_id')->nullable();
            $table->decimal('amount', 15, 4);
            $table->string('type', 20);
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('transaction_payment_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('money_safe_transactions');
    }
}
