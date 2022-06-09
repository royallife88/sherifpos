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
     * @return double
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
            if ($to_currency_id == $default_currency_id && $from_currency_id == $default_currency_id) {
                $amount = $amount;
            } else if (!empty($from_currency_exchange_rate) && empty($to_currency_exchange_rate)) {
                $amount = $amount * $from_currency_exchange_rate->conversion_rate;
            } else if (empty($from_currency_exchange_rate) && !empty($to_currency_exchange_rate)) {
                $amount = $amount / $to_currency_exchange_rate->conversion_rate;
            } else {
                $amount = $amount;
            }
        }

        return $amount;
    }

    /**
     * get money safe balance
     *
     * @param int $id
     * @return double
     */
    public function getSafeBalance($id, $currency_id = null)
    {
        $query = MoneySafe::leftjoin('money_safe_transactions', 'money_safes.id', '=', 'money_safe_transactions.money_safe_id')
            ->where('money_safes.id', $id);

        if (!empty($currency_id)) {
            $query->where('money_safe_transactions.currency_id', $currency_id);
        }

        $safe = $query->select(DB::raw('SUM(IF(money_safe_transactions.type="credit", money_safe_transactions.amount, -1 * money_safe_transactions.amount)) as balance'))->first();
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
    public function addPayment($transaction, $payment_data, $type, $transaction_payment_id = null, $money_safe = null)
    {
        if (empty($money_safe)) {
            $money_safe = MoneySafe::where('store_id', $transaction->store_id)->where('type', 'bank')->first();
            if (empty($money_safe)) {
                $money_safe = MoneySafe::where('is_default', 1)->first();
            }
        }
        $payment_data['amount'] = $this->num_uf($payment_data['amount']);
        $employee = Employee::where('user_id', auth()->user()->id)->first();

        if (!empty($employee)) {
            $data['source_id'] = $employee->id;
            $data['job_type_id'] = $employee->job_type_id;
            $data['source_type'] = 'employee';
        }

        $currency_id = null;
        if ($transaction->type == 'sell') {
            $currency_id = $transaction->received_currency_id;
        }
        if ($transaction->type == 'add_stock') {
            $currency_id = $transaction->paying_currency_id;
            $data['comments'] = __('lang.add_stock');
        }
        if ($transaction->type == 'expense') {
            $currency_id = $payment_data['currency_id'];
            $data['comments'] = __('lang.expense');
        }
        if ($transaction->type == 'wages_and_compensation') {
            $currency_id = $payment_data['currency_id'];
            $data['comments'] = __('lang.wages_and_compensation');
        }
        if (!empty($money_safe)) {
            if ($type == 'credit') {
                $data['money_safe_id'] = $money_safe->id;
                $data['transaction_date'] = $transaction->transaction_date;
                $data['transaction_id'] = $transaction->id;
                $data['transaction_payment_id'] = $transaction_payment_id;
                $data['currency_id'] = $currency_id;
                $data['type'] = $type;
                $data['store_id'] = $transaction->store_id;
                $data['amount'] = $this->num_uf($payment_data['amount']);
                $data['created_by'] = $transaction->created_by;

                MoneySafeTransaction::create($data);
            }
            if ($type == 'debit') {
                $exchange_rate_currencies =  $this->getCurrenciesExchangeRateArray(true);
                $amount = $this->num_uf($payment_data['amount']);

                $data['money_safe_id'] = $money_safe->id;
                $data['transaction_date'] = $transaction->transaction_date;
                $data['transaction_id'] = $transaction->id;
                $data['transaction_payment_id'] = $transaction_payment_id;
                $data['currency_id'] = $currency_id;
                $data['type'] = $type;
                $data['store_id'] = $transaction->store_id;
                $data['amount'] = $amount;
                $data['created_by'] = $transaction->created_by;


                $safe_balance = $this->getSafeBalance($money_safe->id, $currency_id);
                if ($safe_balance > $amount) {
                    $data['amount'] = $amount;
                    $amount = 0;
                    MoneySafeTransaction::create($data);
                } elseif ($safe_balance < $amount && $safe_balance > 0) {
                    $data['amount'] = $safe_balance;
                    $amount -= $safe_balance;
                    MoneySafeTransaction::create($data);
                }
                unset($exchange_rate_currencies[$currency_id]);
                foreach ($exchange_rate_currencies as $key => $currency) {
                    $amount_to_debit = 0;
                    if ($amount > 0) {
                        $safe_balance = $this->getSafeBalance($money_safe->id, $key);
                        $converted_amount = $this->convertCurrencyAmount($amount, $currency_id, $key, $transaction->store_id);
                        if ($safe_balance >= $converted_amount) {
                            $amount_to_debit = $converted_amount;
                            $amount = 0;
                        } else {
                            $amount_to_debit = $safe_balance;
                            $revert_amount = $this->convertCurrencyAmount($safe_balance, $key, $currency_id, $transaction->store_id);
                            $amount = $amount - $revert_amount;
                        }

                        $data['currency_id'] = $key;
                        $data['amount'] = $amount_to_debit;
                        if ($data['amount'] > 0) {
                            MoneySafeTransaction::create($data);
                        }
                    }
                }
            }
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
    public function updatePayment($transaction, $payment_data, $type, $transaction_payment_id = null, $old_tp = null, $money_safe  = null)
    {
        if (empty($money_safe)) {
            $money_safe = MoneySafe::where('store_id', $transaction->store_id)->where('type', 'bank')->first();
            if (empty($money_safe)) {
                $money_safe = MoneySafe::where('is_default', 1)->first();
            }
        }

        if (!empty($old_tp)) {
            if (($old_tp->method == 'card' || $old_tp->method == 'bank_transfer') && $payment_data['method'] == 'cash') {
                MoneySafeTransaction::where('transaction_payment_id', $transaction_payment_id)->delete();
            }
        }

        if ($transaction->type == 'sell') {

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
        }

        if ($transaction->type == 'add_stock' || $transaction->type == 'expense' || $transaction->type == 'wages_and_compensation') {
            $old_ms_transaction = MoneySafeTransaction::where('transaction_id', $transaction->id)->first();
            if ($old_ms_transaction->money_safe_id != $money_safe->id) {
                MoneySafeTransaction::where('transaction_id', $transaction->id)->delete();
            }

            $default_currency_id = (int) System::getProperty('currency');
            $money_safe_transactions = MoneySafeTransaction::where('transaction_payment_id', $transaction_payment_id)->get();
            $total_paid_amount_base = 0;
            foreach ($money_safe_transactions as $mst) {
                $coverted_amount_base = $this->convertCurrencyAmount($mst->amount, $mst->currency_id, $default_currency_id, $transaction->store_id);
                $total_paid_amount_base += $coverted_amount_base;
            }
            $total_amount_base = $this->convertCurrencyAmount($transaction->final_total, $transaction->paying_currency_id, $default_currency_id, $transaction->store_id);
            $remaing_amount_base = $total_amount_base - $total_paid_amount_base;
            $remaing_amount = $this->convertCurrencyAmount($remaing_amount_base, $default_currency_id, $transaction->paying_currency_id, $transaction->store_id);

            $exchange_rate_currencies =  $this->getCurrenciesExchangeRateArray(true);


            if ($remaing_amount > 0) {
                if ($transaction->type == 'add_stock') {
                    $currency_id = $transaction->paying_currency_id;
                    $data['comments'] = __('lang.add_stock');
                }
                if ($transaction->type == 'expense') {
                    $currency_id = $payment_data['currency_id'];
                    $data['comments'] = __('lang.expense');
                }
                if ($transaction->type == 'wages_and_compensation') {
                    $currency_id = $payment_data['currency_id'];
                    $data['comments'] = __('lang.wages_and_compensation');
                }
                $amount = $this->num_uf($remaing_amount);
                $data['money_safe_id'] = $money_safe->id;
                $data['transaction_date'] = $transaction->transaction_date;
                $data['transaction_id'] = $transaction->id;
                $data['transaction_payment_id'] = $transaction_payment_id;
                $data['currency_id'] = $currency_id;
                $data['type'] = $type;
                $data['store_id'] = $transaction->store_id;
                $data['amount'] = $amount;
                $data['created_by'] = $transaction->created_by;
                $data['source_id'] = null;


                $safe_balance = $this->getSafeBalance($money_safe->id, $currency_id);
                if ($safe_balance > $amount) {
                    $data['amount'] = $amount;
                    $amount = 0;
                    MoneySafeTransaction::create($data);
                } elseif ($safe_balance < $amount && $safe_balance > 0) {
                    $data['amount'] = $safe_balance;
                    $amount -= $safe_balance;
                    MoneySafeTransaction::create($data);
                }
                unset($exchange_rate_currencies[$currency_id]); // remove from array if balance is zero
                foreach ($exchange_rate_currencies as $key => $currency) {
                    $amount_to_debit = 0;
                    if ($amount > 0) {
                        $safe_balance = $this->getSafeBalance($money_safe->id, $key);
                        $converted_amount = $this->convertCurrencyAmount($amount, $currency_id, $key, $transaction->store_id);
                        if ($safe_balance >= $converted_amount) {
                            $amount_to_debit = $converted_amount;
                            $amount = 0;
                        } else {
                            $amount_to_debit = $safe_balance;
                            $revert_amount = $this->convertCurrencyAmount($safe_balance, $key, $currency_id, $transaction->store_id);
                            $amount = $amount - $revert_amount;
                        }

                        $data['currency_id'] = $key;
                        $data['amount'] = $amount_to_debit;
                        if ($data['amount'] > 0) {
                            MoneySafeTransaction::create($data);
                        }
                    }
                }
            } else {
                $amount = abs($remaing_amount);

                foreach ($money_safe_transactions as $money_safe_transaction) {
                    if ($amount > 0) {
                        $amount_to_base = $this->convertCurrencyAmount($amount, $transaction->paying_currency_id, $default_currency_id, $transaction->store_id);
                        $mst_amount_base = $this->convertCurrencyAmount($money_safe_transaction->amount, $money_safe_transaction->currency_id, $default_currency_id, $transaction->store_id);
                        if ($mst_amount_base <= $amount_to_base) {
                            $money_safe_transaction->delete();

                            $remaing_base = $amount_to_base - $mst_amount_base;
                            $remaing = $this->convertCurrencyAmount($remaing_base, $default_currency_id, $transaction->paying_currency_id, $transaction->store_id);
                            $amount = $remaing;
                        } else {
                            $money_safe_transaction->amount -= $this->convertCurrencyAmount($amount_to_base, $default_currency_id, $money_safe_transaction->currency_id, $transaction->store_id);
                            $money_safe_transaction->save();
                            $amount = 0;
                        }
                    }
                }
            }
        }



        return true;
    }
}
