<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('rejoining_date');
            $table->integer('number_of_days');
            $table->enum('paid_or_not_paid', ['paid', 'not_paid']);
            $table->decimal('amount_to_paid')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('details')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->enum('status', ['pending', 'rejected', 'approved'])->default('pending');
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
        Schema::dropIfExists('leaves');
    }
}
