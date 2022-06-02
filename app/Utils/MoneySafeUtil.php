<?php

namespace App\Utils;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Currency;
use App\Models\Employee;
use App\Models\ExchangeRate;
use App\Models\MoneySafe;
use App\Models\MoneySafeTransaction;
use App\Models\StorePos;
use App\Models\Supplier;
use App\Models\System;
use App\Models\Transaction;
use App\Notifications\PurchaseOrderToSupplierNotification;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Notification;

class MoneySafeUtil extends Util
{
    /**
     * converty currency base on exchange rate
     *
     * @param float $amount
     * @param int $from_currency_id
     * @param int $to_currency_id
     * @param int $store_id
     * @return void
     */
    public function convertCurrencyAmount($amount, $from_currency_id, $to_currency_id, $store_id = null)
    {
        $amount = $this->num_uf($amount);
        $default_currency_id = System::getProperty('currency');
        $default_currency = Currency::find($default_currency_id);
        $from_currency_query = ExchangeRate::where('received_currency_id', $from_currency_id);
        if (!empty($store_id)) {
            $from_currency_query->where('store_id', $store_id);
        }
        $from_currency_exchange_rate = $from_currency_query->first();

        $to_currency_query = ExchangeRate::where('received_currency_id', $to_currency_id);
        if (!empty($store_id)) {
            $to_currency_query->where('store_id', $store_id);
        }
        $to_currency_exchange_rate = $to_currency_query->first();
        if (!empty($from_currency_exchange_rate) && !empty($to_currency_exchange_rate)) {
            $amount_to_base = $amount * $from_currency_exchange_rate->conversion_rate;
            $amount = $amount_to_base / $to_currency_exchange_rate->conversion_rate;
        } else {
            if ($to_currency_id == $default_currency_id) {
                $amount = $amount;
            } else {
                $amount_to_base = $amount;
                if (!empty($to_currency_exchange_rate)) {
                    $amount = $amount_to_base / $to_currency_exchange_rate->conversion_rate;
                } else {
                    $amount = $amount;
                }
            }
        }

        return $amount;
    }

    /**
     * get money safe balance
     *
     * @param int $id
     * @return void
     */
    public function getSafeBalance($id)
    {
        $safe = MoneySafe::leftjoin('money_safe_transactions', 'money_safes.id', '=', 'money_safe_transactions.money_safe_id')
            ->where('money_safes.id', $id)
            ->select(DB::raw('SUM(IF(money_safe_transactions.type="credit", money_safe_transactions.amount, -1 * money_safe_transactions.amount)) as balance'))
            ->first();

        return $safe->balance ?? 0;
    }

    /**
     * add payment to safe
     *
     * @param object $transaction
     * @param array $payment_data
     * @param string $type
     * @return void
     */
    public function addPayment($transaction, $payment_data, $type, $transaction_payment_id = null)
    {
        $money_safe = MoneySafe::where('store_id', $transaction->store_id)->where('type', 'bank')->first();
        if (empty($money_safe)) {
            $money_safe = MoneySafe::where('is_default', 1)->first();
        }
        $payment_data['amount'] = $this->convertCurrencyAmount($payment_data['amount'], $transaction->received_currency_id, $money_safe->currency_id, $transaction->store_id);
        $employee = Employee::where('user_id', auth()->user()->id)->first();

        if (!empty($employee)) {
            $data['source_id'] = $employee->id;
            $data['job_type_id'] = $employee->job_type_id;
            $data['source_type'] = 'employee';
        }

        if (!empty($money_safe)) {
            $data['money_safe_id'] = $money_safe->id;
            $data['transaction_date'] = $transaction->transaction_date;
            $data['transaction_id'] = $transaction->id;
            $data['transaction_payment_id'] = $transaction_payment_id;
            $data['currency_id'] = $transaction->received_currency_id;
            $data['type'] = $type;
            $data['store_id'] = $transaction->store_id;
            $data['amount'] = $this->num_uf($payment_data['amount']);
            $data['created_by'] = $transaction->created_by;

            MoneySafeTransaction::create($data);
        }

        return true;
    }

    /**
     * add payment to safe
     *
     * @param object $transaction
     * @param array $payment_data
     * @param string $type
     * @return void
     */
    public function updatePayment($transaction, $payment_data, $type, $transaction_payment_id = null, $old_tp = null)
    {
        $money_safe = MoneySafe::where('store_id', $transaction->store_id)->where('type', 'bank')->first();
        if (empty($money_safe)) {
            $money_safe = MoneySafe::where('is_default', 1)->first();
        }

        if (!empty($old_tp)) {
            if (($old_tp->method == 'card' || $old_tp->method == 'bank_transfer') && $payment_data['method'] == 'cash') {
                MoneySafeTransaction::where('transaction_payment_id', $transaction_payment_id)->delete();
            }
        }

        if ($payment_data['method'] == 'bank_transfer' || $payment_data['method'] == 'card') {
            $money_safe_transaction = MoneySafeTransaction::where('transaction_payment_id', $transaction_payment_id)->first();
            if (empty($money_safe_transaction)) {
                $money_safe_transaction = new MoneySafeTransaction();

                $employee = Employee::where('user_id', auth()->user()->id)->first();

                if (!empty($employee)) {
                    $money_safe_transaction->source_id = $employee->id;
                    $money_safe_transaction->job_type_id = $employee->job_type_id;
                    $money_safe_transaction->source_type = 'employee';
                }
            }

            if (!empty($money_safe)) {
                $money_safe_transaction->money_safe_id = $money_safe->id;
                $money_safe_transaction->transaction_date = $transaction->transaction_date;
                $money_safe_transaction->transaction_id = $transaction->id;
                $money_safe_transaction->transaction_payment_id = $transaction_payment_id;
                $money_safe_transaction->currency_id = $transaction->received_currency_id;
                $money_safe_transaction->type = $type;
                $money_safe_transaction->store_id = $transaction->store_id;
                $money_safe_transaction->amount = $this->num_uf($payment_data['amount']);
                $money_safe_transaction->created_by = $transaction->created_by;

                $money_safe_transaction->save();
            }
        }



        return true;
    }
}
