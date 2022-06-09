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
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('store_pos_id')->nullable();
            $table->string('type')->nullable();
            $table->string('sub_type')->nullable();
            $table->enum('status', ['received', 'pending', 'ordered', 'final', 'draft', 'sent_admin', 'sent_supplier', 'partially_received', 'approved', 'rejected', 'expired', 'valid', 'declined', 'send_the_goods', 'compensated', 'canceled']);
            $table->integer('ticket_number')->default(0)->comment('used for restaurant only');
            $table->string('order_date')->nullable();
            $table->string('transaction_date');
            $table->enum('payment_status', ['paid', 'pending', 'partial'])->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('po_no')->nullable();
            $table->boolean('is_raw_material')->default(0);
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
            $table->decimal('gift_card_amount', 15, 4)->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->string('tax_method', 25)->nullable();
            $table->decimal('total_tax', 15, 4)->nullable();
            $table->decimal('total_item_tax', 15, 4)->default(0);
            $table->decimal('other_expenses', 15, 4)->default(0);
            $table->decimal('other_payments', 15, 4)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 15, 4)->default(0)->comment('discount value applied by user');
            $table->decimal('discount_amount', 15, 4)->default(0)->comment('amount calculated based on type and value');
            $table->decimal('total_sp_discount', 15, 4)->default(0)->comment('total of sale promotion discount');
            $table->decimal('total_product_surplus', 15, 4)->default(0)->comment('total of product surplus');
            $table->decimal('total_product_discount', 15, 4)->default(0)->comment('total of product discount');
            $table->decimal('total_coupon_discount', 15, 4)->default(0)->comment('total of coupon discount');
            $table->string('ref_no')->nullable();
            $table->decimal('grand_total', 15, 4)->nullable();
            $table->decimal('final_total', 15, 4)->default(0.0000);
            $table->decimal('exchange_rate', 15, 4)->default(1);
            $table->unsignedBigInteger('default_currency_id')->nullable()->comment('system default currency id');
            $table->unsignedBigInteger('received_currency_id')->nullable()->comment('currency id of received currency');
            $table->unsignedBigInteger('paying_currency_id')->nullable()->comment('currency id of paying currency');
            $table->unsignedBigInteger('deliveryman_id')->nullable()->comment('employee id foriegn key from employees table');
            $table->string('delivery_status')->nullable();
            $table->decimal('delivery_cost', 15, 4)->default(0);
            $table->boolean('delivery_cost_paid_by_customer')->default(1);
            $table->boolean('delivery_cost_given_to_deliveryman')->default(0);
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
            $table->unsignedBigInteger('customer_size_id')->nullable();
            $table->string('fabric_name')->nullable();
            $table->string('fabric_squatch')->nullable();
            $table->string('prova_datetime')->nullable();
            $table->string('delivery_datetime')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
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
            $table->unsignedBigInteger('wages_and_compensation_id')->nullable();
            $table->unsignedBigInteger('restaurant_order_id')->nullable();
            $table->unsignedBigInteger('dining_room_id')->nullable();
            $table->unsignedBigInteger('dining_table_id')->nullable();
            $table->unsignedBigInteger('service_fee_id')->nullable();
            $table->decimal('service_fee_rate', 15, 4)->nullable();
            $table->decimal('service_fee_value', 15, 4)->nullable();
            $table->unsignedBigInteger('delivery_zone_id')->nullable();
            $table->string('manual_delivery_zone')->nullable();
            $table->string('table_no')->nullable();
            $table->text('commissioned_employees')->nullable();
            $table->boolean('shared_commission')->default(0);
            $table->unsignedBigInteger('canceled_by')->nullable();
            $table->foreign('canceled_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');

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
