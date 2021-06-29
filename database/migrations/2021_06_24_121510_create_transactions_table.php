<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('type')->nullable();
            $table->string('sub_type')->nullable();
            $table->enum('status', ['received', 'pending', 'ordered', 'final', 'draft', 'sent_admin', 'sent_supplier', 'partially_received']);
            $table->string('order_date')->nullable();
            $table->string('transaction_date');
            $table->enum('payment_status', ['paid', 'pending', 'partial'])->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('po_no')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('ref_no')->nullable();
            $table->decimal('final_total', 15, 4)->default(0.0000);
            $table->text('details')->nullable();
            $table->text('notes')->nullable();
            $table->string('due_date')->nullable();
            $table->integer('notify_me')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable();

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
        Schema::dropIfExists('transactions');
    }
}
