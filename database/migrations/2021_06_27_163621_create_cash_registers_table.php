<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('store_pos_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['open', 'close']);
            $table->dateTime('closed_at')->nullable();
            $table->decimal('closing_amount', '15', 4)->default(0);
            $table->decimal('discrepancy', '15', 4)->default(0);
            $table->string('source_type', 25)->nullable();
            $table->unsignedBigInteger('cash_given_to')->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('cash_registers');
    }
}
