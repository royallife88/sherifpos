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
            $transaction_payment->bank_deposit_date = $payment_data['bank_deposit_date'];
            $transaction_payment->bank_name = $payment_data['bank_name'];
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
}
