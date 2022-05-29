<?php

namespace App\Utils;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\StorePos;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Notifications\PurchaseOrderToSupplierNotification;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Notification;

class CashRegisterUtil extends Util
{
    /**
     * Returns number of opened Cash Registers for the
     * current logged in user
     *
     * @return int
     */
    public function countOpenedRegister()
    {
        $user_id = auth()->user()->id;
        $count =  CashRegister::where('user_id', $user_id)
            ->where('status', 'open')
            ->count();
        return $count;
    }


    /**
     * Retrieves the currently opened cash register for the user
     *
     * @param $int user_id
     *
     * @return obj
     */
    public function getCurrentCashRegister($user_id)
    {
        $register =  CashRegister::where('user_id', $user_id)
            ->where('status', 'open')
            ->first();

        return $register;
    }

    /**
     * Retrieves the currently opened cash register for the user
     *
     * @param $int user_id
     *
     * @return obj
     */
    public function getCurrentCashRegisterOrCreate($user_id)
    {
        $register =  CashRegister::where('user_id', $user_id)
            ->where('status', 'open')
            ->first();

        if (empty($register)) {
            $store_pos = StorePos::where('user_id', $user_id)->first();
            $register = CashRegister::create([
                'user_id' => $user_id,
                'status' => 'open',
                'store_id' => !empty($store_pos) ? $store_pos->store_id : null,
                'store_pos_id' => !empty($store_pos) ? $store_pos->id : null
            ]);
        }

        return $register;
    }

    public function createCashRegisterTransaction($register, $amount, $transaction_type, $type, $source_id, $notes, $referenced_id = null)
    {
        $cash_register_transaction = CashRegisterTransaction::create([
            'cash_register_id' => $register->id,
            'amount' => $amount,
            'pay_method' => 'cash',
            'type' => $type,
            'transaction_type' => $transaction_type,
            'source_id' => $source_id,
            'referenced_id' => $referenced_id,
            'notes' => $notes,
        ]);

        return $cash_register_transaction;
    }

    public function updateCashRegisterTransaction($id, $register, $amount, $transaction_type, $type, $source_id, $notes, $referenced_id = null)
    {
        $data = [
            'cash_register_id' => $register->id,
            'amount' => $amount,
            'pay_method' => 'cash',
            'type' => $type,
            'transaction_type' => $transaction_type,
            'source_id' => $source_id,
            'referenced_id' => $referenced_id,
            'notes' => $notes,
        ];
        $cash_register_transaction = CashRegisterTransaction::where('id', $id)->first();
        if (!empty($cash_register_transaction)) {
            $cash_register_transaction->update($data);
        } else {
            $cash_register_transaction = CashRegisterTransaction::create($data);
        }
        return $cash_register_transaction;
    }

    /**
     * Adds sell payments to currently opened cash register
     *
     * @param object/int $transaction
     * @param array $payments
     *
     * @return boolean
     */
    public function addPayments($transaction, $payment, $type = 'credit', $user_id = null, $transaction_payment_id = null)
    {
        if (empty($user_id)) {
            $user_id = auth()->user()->id;
        }
        $register =  $this->getCurrentCashRegisterOrCreate($user_id);

        if ($transaction->type == 'sell_return') {
            $cr_transaction = CashRegisterTransaction::where('transaction_id', $transaction->id)->first();
            if (!empty($cr_transaction)) {
                $cr_transaction->update([
                    'amount' => $this->num_uf($payment['amount']),
                    'pay_method' => $payment['method'],
                    'type' => $type,
                    'transaction_type' => $transaction->type,
                    'transaction_id' => $transaction->id,
                    'transaction_payment_id' => $transaction_payment_id
                ]);

                return true;
            } else {
                CashRegisterTransaction::create([
                    'cash_register_id' => $register->id,
                    'amount' => $this->num_uf($payment['amount']),
                    'pay_method' =>  $payment['method'],
                    'type' => $type,
                    'transaction_type' => $transaction->type,
                    'transaction_id' => $transaction->id,
                    'transaction_payment_id' => $transaction_payment_id
                ]);
                return true;
            }
        } else {
            $payments_formatted[] = new CashRegisterTransaction([
                'amount' => $this->num_uf($payment['amount']),
                'pay_method' => $payment['method'],
                'type' => $type,
                'transaction_type' => $transaction->type,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => $transaction_payment_id
            ]);
        }


        //add to cash register pos return amount as sell amount
        if (!empty($pos_return_transactions)) {
            $payments_formatted[0]['amount'] = $payments_formatted[0]['amount'] + !empty($pos_return_transactions) ? $this->num_uf($pos_return_transactions->final_total) : 0;
        }

        if (!empty($payments_formatted) && !empty($register)) {
            $register->cash_register_transactions()->saveMany($payments_formatted);
        }

        return true;
    }


    /**
     * Adds sell payments to currently opened cash register
     *
     * @param object/int $transaction
     * @param array $payments
     *
     * @return boolean
     */
    public function updateAddStockAndExpensePayments($transaction, $payment_data, $request)
    {
        $user_id = null;
        if (!empty($request->source_id)) {
            if ($request->source_type == 'pos') {
                $user_id = StorePos::where('id', $request->source_id)->first()->user_id;
            }
            if ($request->source_type == 'user') {
                $user_id = $request->source_id;
            }
        }

        $cr_transaction = CashRegisterTransaction::where('transaction_id', $transaction->id)->where('transaction_type', $transaction->type)->first();
        $register = CashRegister::find($cr_transaction->cash_register_id);
        if ($register->user_id != $user_id) {
            $register =  $this->getCurrentCashRegisterOrCreate($user_id);
        }

        $referenced_id = $cr_transaction->referenced_id;
        $refercene_transaction = CashRegisterTransaction::where('id', $referenced_id)->first();
        $refercene_register = CashRegister::find($refercene_transaction->cash_register_id);

        $cash_register_transaction = $this->updateCashRegisterTransaction($cr_transaction->id, $register, $payment_data['amount'], $transaction->type, 'debit', $refercene_register->user_id, '', $referenced_id);

        $refercene_transaction = CashRegisterTransaction::where('id', $referenced_id)->first();


        $user_id = $register->user_id;
        $cash_register_transaction_out = $this->updateCashRegisterTransaction($referenced_id, $refercene_register, $payment_data['amount'], 'cash_out', 'credit', $user_id, '', $cr_transaction->id);

        $cash_register_transaction->transaction_id = $transaction->id;
        $cash_register_transaction->referenced_id = $cash_register_transaction_out->id;
        $cash_register_transaction->save();
        $cash_register_transaction_out->transaction_id = $transaction->id;
        $cash_register_transaction_out->save();

        return true;
    }
    /**
     * Adds sell payments to currently opened cash register
     *
     * @param object/int $transaction
     * @param array $payments
     *
     * @return boolean
     */
    public function updateSellPaymentsBasedOnPaymentDate($transaction, $payments)
    {
        $transaction = Transaction::find($transaction->id);
        $opened_register =  CashRegister::where('user_id', $transaction->created_by)
            ->where('status', 'open')
            ->first();
        if ($transaction->status == 'final') {
            $payment_methods = [
                'cash',
                'card',
                'cheque',
                'bank_transfer',

            ];

            $prev_payments = CashRegisterTransaction::where('transaction_id', $transaction->id)
                ->select(
                    DB::raw("SUM(IF(pay_method='cash', IF(type='credit', amount, -1 * amount), 0)) as total_cash"),
                    DB::raw("SUM(IF(pay_method='card', IF(type='credit', amount, -1 * amount), 0)) as total_card"),
                    DB::raw("SUM(IF(pay_method='cheque', IF(type='credit', amount, -1 * amount), 0)) as total_cheque"),
                    DB::raw("SUM(IF(pay_method='bank_transfer', IF(type='credit', amount, -1 * amount), 0)) as total_bank_transfer")
                )->first();
            if (!empty($prev_payments)) {
                foreach ($payment_methods as $payment_method) {
                    $total_query = CashRegisterTransaction::where('transaction_id', $transaction->id)
                        ->where('pay_method', $payment_method)
                        ->select(
                            DB::raw("SUM(IF(type='credit', amount, -1 * amount)) as total"),
                            'cash_register_id',
                            'transaction_payment_id',
                        )->groupBy('cash_register_id')->having('total', '>', 0)->first();
                    $payment_diffs[$payment_method] = [
                        'value' => $total_query->total ?? 0,
                        'transaction_payment_id' => $total_query->transaction_payment_id ?? null,
                        'cash_register_id' => $total_query->cash_register_id ?? null
                    ];
                }
                foreach ($payments as $payment) {
                    $amount = $this->num_uf($payment['amount']);
                    $change_amount = !empty($payment['change_amount']) ? $this->num_uf($payment['change_amount']) : 0;
                    $amount = $amount - $change_amount;
                    $payment_diffs[$payment['method']]['transaction_payment_id'] = $payment['transaction_payment_id'];
                    $payment_diffs[$payment['method']]['new_cash_register_id'] = !empty($payment['cash_register_id']) ? $payment['cash_register_id'] : null;
                    if (isset($payment['is_return']) && $payment['is_return'] == 1) {
                        $payment_diffs[$payment['method']]['value'] = $payment_diffs[$payment['method']]['value'] + $this->num_uf($amount);
                    } else {
                        if (!empty($payment['cash_register_id']) && $payment['cash_register_id'] != $payment_diffs[$payment['method']]['cash_register_id']) {
                            $payment_diffs[$payment['method']]['value'] = $payment_diffs[$payment['method']]['value'];
                            $payment_diffs[$payment['method']]['diff'] = $amount - $payment_diffs[$payment['method']]['value'];
                        } else {
                            $payment_diffs[$payment['method']]['value'] = $payment_diffs[$payment['method']]['value'] - $this->num_uf($amount);
                            $payment_diffs[$payment['method']]['diff'] = 0;
                        }
                    }
                }

                $payments_formatted = [];
                foreach ($payment_diffs as $key => $value) {
                    if ($value['value'] > 0) {
                        $payments_formatted[] = CashRegisterTransaction::create([
                            'amount' => $value['value'],
                            'pay_method' => $key,
                            'type' => 'debit',
                            'transaction_type' => 'refund',
                            'transaction_id' => $transaction->id,
                            'transaction_payment_id' => $value['transaction_payment_id'],
                            'cash_register_id' => $value['cash_register_id'] ?? $opened_register->id,
                        ]);

                        if (!empty($value['cash_register_id'])) {
                            $pre_register = CashRegister::find($value['cash_register_id']);
                            if (!empty($pre_register->closed_at)) {
                                $pre_register->closing_amount = $pre_register->closing_amount - $this->num_uf($value['value']);
                                $pre_register->save();
                            }
                        }

                        if (!empty($value['new_cash_register_id']) && $value['new_cash_register_id'] != $value['cash_register_id']) {
                            $register = CashRegister::find($value['new_cash_register_id']);

                            $payments_formatted[] = CashRegisterTransaction::create([
                                'amount' => $value['value'] + $payment_diffs[$payment['method']]['diff'],
                                'pay_method' => $key,
                                'type' => 'credit',
                                'transaction_type' => 'sell',
                                'transaction_id' => $transaction->id,
                                'transaction_payment_id' => $value['transaction_payment_id'],
                                'cash_register_id' => $register->id,
                            ]);


                            if (!empty($register->closed_at)) {
                                $register->closing_amount = $register->closing_amount + $this->num_uf($value['value'] + $payment_diffs[$payment['method']]['diff']);
                                $register->save();
                            }

                            $pre_register = CashRegister::find($value['cash_register_id']);
                            if (!empty($pre_register->closed_at)) {
                                $pre_register->closing_amount = $pre_register->closing_amount - $this->num_uf($value['value'] + $payment_diffs[$payment['method']]['diff']);
                                $pre_register->save();
                            }
                        }
                    } elseif ($value['value'] < 0) {
                        $payments_formatted[] = CashRegisterTransaction::create([
                            'amount' => -1 * $value['value'],
                            'pay_method' => $key,
                            'type' => 'credit',
                            'transaction_type' => 'sell',
                            'transaction_id' => $transaction->id,
                            'transaction_payment_id' => $value['transaction_payment_id'],
                            'cash_register_id' => $value['cash_register_id'] ?? $opened_register->id,
                        ]);

                        if (!empty($value['cash_register_id'])) {
                            $pre_register = CashRegister::find($value['cash_register_id']);
                            if (!empty($pre_register->closed_at)) {
                                $pre_register->closing_amount = $pre_register->closing_amount + $this->num_uf(-1 * $value['value']);
                                $pre_register->save();
                            }
                        }

                        if (!empty($value['new_cash_register_id']) && $value['new_cash_register_id'] != $value['cash_register_id']) {
                            $register = CashRegister::find($value['new_cash_register_id']);

                            $payments_formatted[] = CashRegisterTransaction::create([
                                'amount' => -1 * $value['value'],
                                'pay_method' => $key,
                                'type' => 'debit',
                                'transaction_type' => 'refund',
                                'transaction_id' => $transaction->id,
                                'transaction_payment_id' => $value['transaction_payment_id'],
                                'cash_register_id' => $register->id,
                            ]);

                            if (!empty($register->closed_at)) {
                                $register->closing_amount = $register->closing_amount - $this->num_uf(-1 * $value['value']);
                                $register->save();
                            }

                            $pre_register = CashRegister::find($value['cash_register_id']);
                            if (!empty($pre_register->closed_at)) {
                                $pre_register->closing_amount = $pre_register->closing_amount + $this->num_uf(-1 * $value['value']);
                                $pre_register->save();
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
    /**
     * Adds sell payments to currently opened cash register
     *
     * @param object/int $transaction
     * @param array $payments
     *
     * @return boolean
     */
    public function updateSellPayments($transaction, $payments)
    {
        $user_id = auth()->user()->id;
        $transaction = Transaction::find($transaction->id);
        $register =  CashRegister::where('user_id', $transaction->created_by)
            ->where('status', 'open')
            ->first();
        if ($transaction->status == 'final') {
            $prev_payments = CashRegisterTransaction::where('transaction_id', $transaction->id)
                ->select(
                    DB::raw("SUM(IF(pay_method='cash', IF(type='credit', amount, -1 * amount), 0)) as total_cash"),
                    DB::raw("SUM(IF(pay_method='card', IF(type='credit', amount, -1 * amount), 0)) as total_card"),
                    DB::raw("SUM(IF(pay_method='cheque', IF(type='credit', amount, -1 * amount), 0)) as total_cheque"),
                    DB::raw("SUM(IF(pay_method='bank_transfer', IF(type='credit', amount, -1 * amount), 0)) as total_bank_transfer")
                )->first();
            if (!empty($prev_payments)) {
                $payment_diffs = [
                    'cash' => $prev_payments->total_cash,
                    'card' => $prev_payments->total_card,
                    'cheque' => $prev_payments->total_cheque,
                    'bank_transfer' => $prev_payments->total_bank_transfer,

                ];

                foreach ($payments as $payment) {
                    $amount = $this->num_uf($payment['amount']);
                    $change_amount = !empty($payment['change_amount']) ? $this->num_uf($payment['change_amount']) : 0;
                    $amount = $amount - $change_amount;
                    if (isset($payment['is_return']) && $payment['is_return'] == 1) {
                        $payment_diffs[$payment['method']] += $this->num_uf($amount);
                    } else {
                        $payment_diffs[$payment['method']] -= $this->num_uf($amount);
                    }
                }
                $payments_formatted = [];
                foreach ($payment_diffs as $key => $value) {
                    if ($value > 0) {
                        $payments_formatted[] = new CashRegisterTransaction([
                            'amount' => $value,
                            'pay_method' => $key,
                            'type' => 'debit',
                            'transaction_type' => 'refund',
                            'transaction_id' => $transaction->id
                        ]);
                    } elseif ($value < 0) {
                        $payments_formatted[] = new CashRegisterTransaction([
                            'amount' => -1 * $value,
                            'pay_method' => $key,
                            'type' => 'credit',
                            'transaction_type' => 'sell',
                            'transaction_id' => $transaction->id
                        ]);
                    }
                }
                if (!empty($payments_formatted)) {
                    $register->cash_register_transactions()->saveMany($payments_formatted);
                }
            }
        }

        return true;
    }
}
