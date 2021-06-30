<?php

namespace App\Utils;

use App\Http\Controllers\PurchaseOrderController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductStore;
use App\Models\PurchaseOrderLine;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\TransactionSellLine;
use App\Models\Variation;
use App\Utils\Util;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class TransactionUtil extends Util
{

    /**
     * createOrUpdateTransactionPayment
     *
     * @param object $transaction
     * @param array $payment_data
     * @return object
     */
    public function createOrUpdateTransactionPayment($transaction, $payment_data)
    {
        if (!empty(!empty($payment_data['transaction_payment_id']))) {
            $transaction_payment = TransactionPayment::find($payment_data['transaction_payment_id']);
            $transaction_payment->amount = $payment_data['amount'];
            $transaction_payment->method = $payment_data['method'];
            $transaction_payment->paid_on = $payment_data['paid_on'];
            $transaction_payment->ref_number = $payment_data['ref_number'];
            $transaction_payment->bank_deposit_date = !empty($payment_data['bank_deposit_date']) ? $payment_data['bank_deposit_date'] : null;
            $transaction_payment->bank_name = !empty($payment_data['bank_name']) ?  $payment_data['bank_name'] : null;
            $transaction_payment->card_number = !empty($payment_data['card_number']) ?  $payment_data['card_number'] : null;
            $transaction_payment->card_security = !empty($payment_data['card_security']) ?  $payment_data['card_security'] : null;
            $transaction_payment->card_month = !empty($payment_data['card_month']) ?  $payment_data['card_month'] : null;
            $transaction_payment->cheque_number = !empty($payment_data['cheque_number']) ?  $payment_data['cheque_number'] : null;
            $transaction_payment->gift_card_number = !empty($payment_data['gift_card_number']) ?  $payment_data['gift_card_number'] : null;
            $transaction_payment->amount_to_be_used = !empty($payment_data['amount_to_be_used']) ?  $this->num_uf($payment_data['amount_to_be_used']) : 0;
            $transaction_payment->payment_note = !empty($payment_data['payment_note']) ?  $payment_data['payment_note'] : null;
            $transaction_payment->save();
        } else {
            $transaction_payment = TransactionPayment::create($payment_data);
        }

        return $transaction_payment;
    }

    /**
     * updateTransactionPaymentStatus function
     *
     * @param integer $transaction_id
     * @return void
     */
    public function updateTransactionPaymentStatus($transaction_id)
    {
        $transaction_payments = TransactionPayment::where('transaction_id', $transaction_id)->get();

        $total_paid = $transaction_payments->sum('amount');

        $transaction = Transaction::find($transaction_id);
        $final_amount = $transaction->final_total;

        $payment_status = 'pending';
        if ($final_amount <= $total_paid) {
            $payment_status = 'paid';
        } elseif ($total_paid > 0 && $final_amount > $total_paid) {
            $payment_status = 'partial';
        }
        $transaction->payment_status = $payment_status;
        $transaction->save();
    }


    /**
     * create sell line for product in sale
     *
     * @param object $transaction
     * @param array $transaction_sell_lines
     * @return boolean
     */
    public function createOrUpdateTransactionSellLine($transaction, $transaction_sell_lines)
    {
        $keep_sell_lines = [];

        foreach ($transaction_sell_lines as $line) {
            if (!empty($transaction_sell_line['transaction_sell_line_id'])) {
                $transaction_sell_line = TransactionSellLine::find($line['transaction_sell_line_id']);
                $transaction_sell_line->product_id = $line['product_id'];
                $transaction_sell_line->variation_id = $line['variation_id'];
                $transaction_sell_line->coupon_discount = !empty($line['coupon_discount']) ? $this->num_uf($line['coupon_discount']) : 0;
                $transaction_sell_line->coupon_discount_type = !empty($line['coupon_discount_type']) ? $line['coupon_discount_type'] : null;
                $transaction_sell_line->coupon_discount_amount = !empty($line['coupon_discount_amount']) ? $this->num_uf($line['coupon_discount_amount']) : 0;
                $transaction_sell_line->quantity = $this->num_uf($line['quantity']);
                $transaction_sell_line->sell_price = $this->num_uf($line['sell_price']);
                $transaction_sell_line->sub_total = $this->num_uf($line['sub_total']);
                $transaction_sell_line->save();
                $keep_sell_lines[] = $line['transaction_sell_line_id'];
            } else {
                $transaction_sell_line = new TransactionSellLine();
                $transaction_sell_line->transaction_id = $transaction->id;
                $transaction_sell_line->product_id = $line['product_id'];
                $transaction_sell_line->variation_id = $line['variation_id'];
                $transaction_sell_line->coupon_discount = !empty($line['coupon_discount']) ? $this->num_uf($line['coupon_discount']) : 0;
                $transaction_sell_line->coupon_discount_type = !empty($line['coupon_discount_type']) ? $line['coupon_discount_type'] : null;
                $transaction_sell_line->coupon_discount_amount = !empty($line['coupon_discount_amount']) ? $this->num_uf($line['coupon_discount_amount']) : 0;
                $transaction_sell_line->quantity = $this->num_uf($line['quantity']);
                $transaction_sell_line->sell_price = $this->num_uf($line['sell_price']);
                $transaction_sell_line->sub_total = $this->num_uf($line['sub_total']);
                $transaction_sell_line->save();
                $keep_sell_lines[] = $transaction_sell_line->id;
            }
        }

        //delete sell lines remove by user
        TransactionSellLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_sell_lines)->delete();

        return true;
    }
}
