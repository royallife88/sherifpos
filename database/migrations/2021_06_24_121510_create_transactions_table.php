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
            $table->unsignedBigInteger('store_pos_id')->nullable();
            $table->string('type')->nullable();
            $table->string('sub_type')->nullable();
            $table->enum('status', ['received', 'pending', 'ordered', 'final', 'draft', 'sent_admin', 'sent_supplier', 'partially_received', 'approved', 'rejected', 'expired', 'valid', 'declined', 'send_the_goods', 'compensated']);
            $table->string('order_date')->nullable();
            $table->string('transaction_date');
            $table->enum('payment_status', ['paid', 'pending', 'partial'])->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('po_no')->nullable();
            $table->boolean('is_direct_sale')->default(0);
            $table->boolean('is_return')->default(0);
            $table->boolean('is_quotation')->default(0);
            $table->boolean('is_internal_stock_transfer')->default(0);
            $table->boolean('block_qty')->default(0);
            $table->integer('block_for_days')->default(0);
            $table->integer('validity_days')->default(0);
            $table->unsignedBigInteger('parent_sale_id')->nullable();
            $table->unsignedBigInteger('return_parent_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('add_stock_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('gift_card_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('total_tax', 15, 4)->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 15, 4)->nullable()->comment('discount value applied by user');
            $table->decimal('discount_amount', 15, 4)->nullable()->comment('amount calculated based on type and value');
            $table->decimal('total_sp_discount', 15, 4)->default(0)->comment('total of sale promotion discount');
            $table->decimal('total_product_surplus', 15, 4)->default(0)->comment('total of product surplus');
            $table->decimal('total_product_discount', 15, 4)->default(0)->comment('total of product discount');
            $table->decimal('total_coupon_discount', 15, 4)->default(0)->comment('total of coupon discount');
            $table->string('ref_no')->nullable();
            $table->decimal('grand_total', 15, 4)->nullable();
            $table->decimal('final_total', 15, 4)->default(0.0000);
            $table->text('deliveryman_id')->nullable()->comment('employee id foriegn key from employees table');
            $table->string('delivery_status')->nullable();
            $table->decimal('delivery_cost', 15, 4)->default(0);
            $table->boolean('delivery_cost_paid_by_customer')->default(1);
            $table->text('delivery_address')->nullable();
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->unsignedBigInteger('expense_beneficiary_id')->nullable();
            $table->string('next_payment_date')->nullable();
            $table->unsignedBigInteger('sender_store_id')->nullable();
            $table->unsignedBigInteger('receiver_store_id')->nullable();
            $table->integer('rp_earned')->default(0);
            $table->integer('rp_redeemed')->default(0);
            $table->integer('rp_redeemed_value')->default(0);
            $table->decimal('current_deposit_balance', 15, 4)->default(0);
            $table->decimal('used_deposit_balance', 15, 4)->default(0);
            $table->decimal('remaining_deposit_balance', 15, 4)->default(0);
            $table->decimal('add_to_deposit', 15, 4)->default(0);
            $table->text('details')->nullable();
            $table->text('reason')->nullable();
            $table->text('sale_note')->nullable();
            $table->text('staff_note')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('terms_and_condition_id')->nullable();
            $table->decimal('compensated_value', 15, 4)->default(0);
            $table->string('compensated_at')->nullable();
            $table->string('compensated_invoice_no')->nullable();
            $table->string('approved_at')->nullable();
            $table->string('received_at')->nullable();
            $table->string('declined_at')->nullable();
            $table->string('received_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('requested_by')->nullable();
            $table->string('declined_by')->nullable();
            $table->string('due_date')->nullable();
            $table->boolean('notify_me')->default(0);
            $table->integer('notify_before_days')->default(0);
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
