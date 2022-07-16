<?php

namespace App\Http\Controllers;

use App\Models\CashInAdjustment;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\MoneySafe;
use App\Models\MoneySafeTransaction;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\System;
use App\Models\User;
use App\Utils\CashRegisterUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CashController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $cashRegisterUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, CashRegisterUtil $cashRegisterUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $default_currency_id = System::getProperty('currency');
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id')
            ->leftjoin('transactions', 'cash_register_transactions.transaction_id', 'transactions.id');

        if (!auth()->user()->can('superadmin') || auth()->user()->is_admin == 1 || !auth()->user()->can('cash.view_details.view')) {
            $query->where('user_id', Auth::user()->id);
        }

        if (!empty(request()->start_date)) {
            $query->whereDate('cash_registers.created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('cash_registers.created_at', '<=', request()->end_date);
        }
        if (!empty(request()->start_time)) {
            $query->where('cash_registers.created_at', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $query->where('cash_registers.created_at', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty(request()->store_id)) {
            $query->where('store_id', request()->store_id);
        }
        if (!empty(request()->store_pos_id)) {
            $query->where('store_pos_id', request()->store_pos_id);
        }
        if (!empty(request()->user_id)) {
            $query->where('cash_registers.user_id', request()->user_id);
        }
        // $query->where(function ($q) use ($default_currency_id) {
        //     $q->where('transactions.received_currency_id', $default_currency_id)
        //         ->orWhereNull('transactions.received_currency_id');
        // });

        $cash_registers = $query->select(
            'cash_registers.*',
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit' AND dining_table_id IS NOT NULL, amount, 0)) as total_dining_in"),
            DB::raw("SUM(IF(transaction_type = 'refund', amount, 0)) as total_refund"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cash_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cash"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_card_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'card' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_card"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'credit', amount, 0)) as total_bank_transfer_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_bank_transfer"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'gift_card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_gift_card_sales"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cheque' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cheque_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cheque' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cheque"),
            DB::raw("SUM(IF(transaction_type = 'add_stock' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_purchases"),
            DB::raw("SUM(IF(transaction_type = 'expense' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_expenses"),
            DB::raw("SUM(IF(transaction_type = 'sell_return' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_sell_return"),
            DB::raw("SUM(IF(transaction_type = 'cash_in' AND pay_method = 'cash', amount, 0)) as total_cash_in"),
            DB::raw("SUM(IF(transaction_type = 'cash_out' AND pay_method = 'cash', amount, 0)) as total_cash_out"),
            DB::raw("SUM(IF(transaction_type = 'sell_return' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_sell_return"),
            DB::raw("SUM(IF(transaction_type = 'wages_and_compensation' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_wages_and_compensation")
        )
            ->groupBy('cash_registers.id')->orderBy('cash_registers.created_at', 'desc')->get();


        $stores = Store::getDropdown();
        $store_pos = StorePos::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('cash.index')->with(compact(
            'cash_registers',
            'stores',
            'users',
            'store_pos'

        ));
    }
    /**
     * Display a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id')
            ->leftjoin('transactions', 'cash_register_transactions.transaction_id', 'transactions.id');

        $query->where('cash_registers.id', $id);
        if (!empty(request()->start_date)) {
            $query->whereDate('cash_registers.created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('cash_registers.created_at', '<=', request()->end_date);
        }

        $cash_register = $query->select(
            'cash_registers.*',
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit' AND dining_table_id IS NOT NULL, amount, 0)) as total_dining_in"),
            DB::raw("SUM(IF(transaction_type = 'sell', amount, 0)) as total_sale"),
            DB::raw("SUM(IF(transaction_type = 'refund', amount, 0)) as total_refund"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cash_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cash"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_card_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'card' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_card"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'credit', amount, 0)) as total_bank_transfer_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_bank_transfer"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'gift_card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_gift_card_sales"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cheque' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cheque_sales"),
            DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cheque' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cheque"),
            DB::raw("SUM(IF(transaction_type = 'add_stock' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_purchases"),
            DB::raw("SUM(IF(transaction_type = 'expense' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_expenses"),
            DB::raw("SUM(IF(transaction_type = 'sell_return' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_sell_return"),
            DB::raw("SUM(IF(transaction_type = 'cash_in' AND pay_method = 'cash', amount, 0)) as total_cash_in"),
            DB::raw("SUM(IF(transaction_type = 'cash_out' AND pay_method = 'cash', amount, 0)) as total_cash_out"),
            DB::raw("SUM(IF(transaction_type = 'wages_and_compensation' AND pay_method = 'cash', amount, 0)) as total_wages_and_compensation")
        )
            ->first();
        $cash_register->total_cash_sales =  $cash_register->total_cash_sales - $cash_register->total_refund_cash;
        $cash_register->total_card_sales =  $cash_register->total_card_sales - $cash_register->total_refund_card;
        $cash_register->total_bank_transfer_sales =  $cash_register->total_bank_transfer_sales - $cash_register->total_refund_bank_transfer;
        $cash_register->total_cheque_sales =  $cash_register->total_cheque_sales - $cash_register->total_refund_cheque;

        return view('cash.show')->with(compact(
            'cash_register'
        ));
    }

    /**
     * add cash in
     *
     * @param int $cash_register_id
     * @return void
     */
    public function addCashIn($cash_register_id)
    {
        //Check if there is a open register, if yes then redirect to POS screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0 && !auth()->user()->is_superadmin == 1 && !auth()->user()->is_admin == 1) {
            return redirect()->action('CashRegisterController@create');
        }


        $cash_register = CashRegister::where('id', $cash_register_id)->first();
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('cash.add_cash_in')->with(compact(
            'cash_register',
            'cash_register_id',
            'users'
        ));
    }

    /**
     * add cash in save to storage
     *
     * @param int $cash_register_id
     * @return void
     */
    public function saveAddCashIn(Request $request)
    {
        try {
            DB::beginTransaction();
            $amount = $this->cashRegisterUtil->num_uf($request->input('amount'));
            $register = CashRegister::find($request->cash_register_id);
            if ($register->status == 'close') {
                return redirect()->back()->with('status', [
                    'success' => false,
                    'msg' => __('lang.cash_register_is_closed')
                ]);
            }
            $user_id = $register->user_id;
            $cash_register_transaction = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_in', 'debit', $request->source_id, $request->notes);

            if (!empty($request->source_id)) {
                if ($request->source_type == 'user') {
                    $register = $this->cashRegisterUtil->getCurrentCashRegisterOrCreate($request->source_id);
                    $cash_register_transaction_out = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_out', 'credit', $user_id, $request->notes, $cash_register_transaction->id);
                    $cash_register_transaction->referenced_id = $cash_register_transaction_out->id;
                    $cash_register_transaction->save();
                }

                if ($request->source_type == 'safe') {
                    $default_currency_id = System::getProperty('currency');
                    $money_safe = MoneySafe::find($request->source_id);

                    $money_safe_data['money_safe_id'] = $money_safe->id;
                    $money_safe_data['transaction_date'] = Carbon::now();
                    $money_safe_data['transaction_id'] = null;
                    $money_safe_data['transaction_payment_id'] = null;
                    $money_safe_data['currency_id'] = $default_currency_id;
                    $money_safe_data['type'] = 'credit';
                    $money_safe_data['store_id'] = $register->store_id ?? 0;
                    $money_safe_data['amount'] = $amount;
                    $money_safe_data['created_by'] = Auth::user()->id;
                    $money_safe_data['comments'] = __('lang.cash_in');
                    MoneySafeTransaction::create($money_safe_data);
                }
            }
            if ($request->has('image')) {
                $cash_register_transaction->addMedia($request->image)->toMediaCollection('cash_register');
                $cash_register_transaction_out->addMedia($request->image)->toMediaCollection('cash_register');
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    /**
     * add cash out
     *
     * @param int $cash_register_id
     * @return void
     */
    public function addCashOut($cash_register_id)
    {
        //Check if there is a open register, if yes then redirect to POS screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0 && !auth()->user()->is_superadmin == 1 && !auth()->user()->is_admin == 1) {
            return redirect()->action('CashRegisterController@create');
        }


        $cash_register = CashRegister::where('id', $cash_register_id)->first();
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('cash.add_cash_out')->with(compact(
            'cash_register',
            'cash_register_id',
            'users'
        ));
    }

    /**
     * add cash out save to storage
     *
     * @param int $cash_register_id
     * @return void
     */
    public function saveAddCashOut(Request $request)
    {
        try {
            DB::beginTransaction();
            $amount = $this->cashRegisterUtil->num_uf($request->input('amount'));
            $register = CashRegister::find($request->cash_register_id);
            if ($register->status == 'close') {
                return redirect()->back()->with('status', [
                    'success' => false,
                    'msg' => __('lang.cash_register_is_closed')
                ]);
            }
            $user_id = $register->user_id;
            $cash_register_transaction = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_out', 'credit', $request->source_id, $request->notes);

            if (!empty($request->source_id)) {
                if ($request->source_type == 'user') {
                    $register = $this->cashRegisterUtil->getCurrentCashRegisterOrCreate($request->source_id);
                    $cash_register_transaction_in = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_in', 'debit', $user_id, $request->notes, $cash_register_transaction->id);
                    $cash_register_transaction->referenced_id = $cash_register_transaction_in->id;
                    $cash_register_transaction->save();
                }

                if ($request->source_type == 'safe') {
                    $default_currency_id = System::getProperty('currency');
                    $money_safe = MoneySafe::find($request->source_id);

                    $money_safe_data['money_safe_id'] = $money_safe->id;
                    $money_safe_data['transaction_date'] = Carbon::now();
                    $money_safe_data['transaction_id'] = null;
                    $money_safe_data['transaction_payment_id'] = null;
                    $money_safe_data['currency_id'] = $default_currency_id;
                    $money_safe_data['type'] = 'debit';
                    $money_safe_data['store_id'] = $register->store_id ?? 0;
                    $money_safe_data['amount'] = $amount;
                    $money_safe_data['created_by'] = Auth::user()->id;
                    $money_safe_data['comments'] = __('lang.cash_out');
                    MoneySafeTransaction::create($money_safe_data);
                }
            }
            if ($request->has('image')) {
                $cash_register_transaction->addMedia($request->image)->toMediaCollection('cash_register');
                $cash_register_transaction_in->addMedia($request->image)->toMediaCollection('cash_register');
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    /**
     * add closing cash
     *
     * @param int $cash_register_id
     * @return void
     */
    public function addClosingCash($cash_register_id)
    {
        //Check if there is a open register, if yes then redirect to POS screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0 && !auth()->user()->can('superadmin')) {
            return redirect()->action('CashRegisterController@create');
        }
        $exchange_rate_currencies = $this->commonUtil->getExchangeRateCurrencies(true);
        $type = request()->get('type');
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id')
            ->leftjoin('transactions', 'cash_register_transactions.transaction_id', 'transactions.id');
        $query->where('cash_registers.id', $cash_register_id);

        $cr_data = [];
        $total_cash = 0;
        foreach ($exchange_rate_currencies as $currency) {
            $cr_data[$currency['currency_id']]['currency'] = $currency;
            $cr_query = clone $query;

            if (!$currency['is_default']) {
                $cr_query->where('transactions.received_currency_id', $currency['currency_id']);
            } else {
                $cr_query->where(function ($q) use ($currency) {
                    $q->where('transactions.received_currency_id', $currency['currency_id'])
                        ->orWhereNull('transactions.received_currency_id');
                });
            }


            $cash_register = $cr_query->select(
                'cash_registers.*',
                DB::raw("SUM(IF(transaction_type = 'sell', amount, 0)) as total_sale"),
                DB::raw("SUM(IF(transaction_type = 'refund', amount, 0)) as total_refund"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND cash_register_transactions.type = 'credit' AND dining_table_id IS NOT NULL, amount, 0)) as total_dining_in"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit' AND dining_table_id IS NOT NULL, amount, 0)) as total_dining_in_cash"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cash_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cash"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_card_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'card' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_card"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'credit', amount, 0)) as total_bank_transfer_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_bank_transfer"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'gift_card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_gift_card_sales"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cheque' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cheque_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cheque' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cheque"),
                DB::raw("SUM(IF(transaction_type = 'add_stock' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_purchases"),
                DB::raw("SUM(IF(transaction_type = 'expense' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_expenses"),
                DB::raw("SUM(IF(transaction_type = 'cash_in' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_cash_in"),
                DB::raw("SUM(IF(transaction_type = 'cash_out' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cash_out"),
                DB::raw("SUM(IF(transaction_type = 'sell_return' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_sell_return"),
                DB::raw("SUM(IF(transaction_type = 'wages_and_compensation' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_wages_and_compensation"),
            )->first();
            $cash_register->total_cash_sales =  $cash_register->total_cash_sales - $cash_register->total_refund_cash;
            $cash_register->total_card_sales =  $cash_register->total_card_sales - $cash_register->total_refund_card;
            $cash_register->total_bank_transfer_sales =  $cash_register->total_bank_transfer_sales - $cash_register->total_refund_bank_transfer;
            $cash_register->total_cheque_sales =  $cash_register->total_cheque_sales - $cash_register->total_refund_cheque;
            $cr_data[$currency['currency_id']]['cash_register'] = $cash_register;

            if ($currency['is_default']) {
                $total_cash = $cash_register->total_cash_sales +
                    $cash_register->total_cash_in - $cash_register->total_cash_out -
                    $cash_register->total_purchases - $cash_register->total_expenses - $cash_register->total_wages_and_compensation - $cash_register->total_sell_return;
            }
        }


        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        return view('cash.add_closing_cash')->with(compact(
            'cash_register',
            'cr_data',
            'cash_register_id',
            'type',
            'total_cash',
            'users'
        ));
    }

    /**
     * add closing cash save to storage
     *
     * @param int $cash_register_id
     * @return void
     */
    public function saveAddClosingCash(Request $request)
    {
        try {

            DB::beginTransaction();
            $data = $request->except('_token');

            $amount = $this->cashRegisterUtil->num_uf($request->input('amount'));
            $register = CashRegister::find($request->cash_register_id);
            $register->source_type = $request->source_type;
            $register->cash_given_to = $request->cash_given_to;
            $register->closing_amount = $amount;
            $register->closed_at = Carbon::now();
            $register->status = 'close';
            $register->notes = $request->notes;
            $register->save();

            if ($request->submit == 'adjustment') {
                $data['store_id'] = $register->store_id;
                $data['user_id'] = $register->user_id;
                $data['cash_register_id'] = $register->id;
                $data['amount'] = $amount;
                $data['current_cash'] = $this->commonUtil->num_uf($data['current_cash']);
                $data['discrepancy'] = $this->commonUtil->num_uf($data['discrepancy']);
                $data['date_and_time'] = Carbon::now();
                $data['created_by'] = Auth::user()->id;

                CashInAdjustment::create($data);
            }

            $cash_register_transaction = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'closing_cash', 'credit', $request->source_id, $request->notes);

            $user_id = $register->user_id;

            if (!empty($request->cash_given_to)) {
                if ($request->source_type == 'user') {
                    $register = $this->cashRegisterUtil->getCurrentCashRegisterOrCreate($request->cash_given_to);
                    $cash_register_transaction_in = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_in', 'debit', $user_id, $request->notes, $cash_register_transaction->id);
                    $cash_register_transaction->referenced_id = $cash_register_transaction_in->id;
                    $cash_register_transaction->save();
                }
                if ($request->source_type == 'safe') {
                    $default_currency_id = System::getProperty('currency');
                    $money_safe = MoneySafe::find($request->cash_given_to);

                    $money_safe_data['money_safe_id'] = $money_safe->id;
                    $money_safe_data['transaction_date'] = Carbon::now();
                    $money_safe_data['transaction_id'] = null;
                    $money_safe_data['transaction_payment_id'] = null;
                    $money_safe_data['currency_id'] = $default_currency_id;
                    $money_safe_data['type'] = 'credit';
                    $money_safe_data['store_id'] = $register->store_id ?? 0;
                    $money_safe_data['amount'] = $amount;
                    $money_safe_data['created_by'] = Auth::user()->id;
                    $money_safe_data['comments'] = __('lang.closing_cash');
                    MoneySafeTransaction::create($money_safe_data);
                }
            }



            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * add closing cash
     *
     * @param int $cash_register_id
     * @return void
     */
    public function printClosingCash($cash_register_id)
    {
        $exchange_rate_currencies = $this->commonUtil->getExchangeRateCurrencies(true);
        $type = request()->get('type');
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id')
            ->leftjoin('transactions', 'cash_register_transactions.transaction_id', 'transactions.id');
        $query->where('cash_registers.id', $cash_register_id);

        $cr_data = [];
        $total_cash = 0;
        foreach ($exchange_rate_currencies as $currency) {
            $cr_data[$currency['currency_id']]['currency'] = $currency;
            $cr_query = clone $query;

            if (!$currency['is_default']) {
                $cr_query->where('transactions.received_currency_id', $currency['currency_id']);
            } else {
                $cr_query->where(function ($q) use ($currency) {
                    $q->where('transactions.received_currency_id', $currency['currency_id'])
                        ->orWhereNull('transactions.received_currency_id');
                });
            }


            $cash_register = $cr_query->select(
                'cash_registers.*',
                DB::raw("SUM(IF(transaction_type = 'sell', amount, 0)) as total_sale"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND cash_register_transactions.type = 'credit' AND dining_table_id IS NOT NULL, amount, 0)) as total_dining_in"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit' AND dining_table_id IS NOT NULL, amount, 0)) as total_dining_in_cash"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cash_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cash"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_card_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'card' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_card"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'credit', amount, 0)) as total_bank_transfer_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'bank_transfer' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_bank_transfer"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'gift_card' AND cash_register_transactions.type = 'credit', amount, 0)) as total_gift_card_sales"),
                DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cheque' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cheque_sales"),
                DB::raw("SUM(IF(transaction_type = 'refund' AND pay_method = 'cheque' AND cash_register_transactions.type = 'debit', amount, 0)) as total_refund_cheque"),
                DB::raw("SUM(IF(transaction_type = 'add_stock' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_purchases"),
                DB::raw("SUM(IF(transaction_type = 'expense' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_expenses"),
                DB::raw("SUM(IF(transaction_type = 'cash_in' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_cash_in"),
                DB::raw("SUM(IF(transaction_type = 'cash_out' AND pay_method = 'cash' AND cash_register_transactions.type = 'credit', amount, 0)) as total_cash_out"),
                DB::raw("SUM(IF(transaction_type = 'sell_return' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_sell_return"),
                DB::raw("SUM(IF(transaction_type = 'wages_and_compensation' AND pay_method = 'cash' AND cash_register_transactions.type = 'debit', amount, 0)) as total_wages_and_compensation"),
            )->first();
            $cash_register->total_cash_sales =  $cash_register->total_cash_sales - $cash_register->total_refund_cash;
            $cash_register->total_card_sales =  $cash_register->total_card_sales - $cash_register->total_refund_card;
            $cash_register->total_bank_transfer_sales =  $cash_register->total_bank_transfer_sales - $cash_register->total_refund_bank_transfer;
            $cash_register->total_cheque_sales =  $cash_register->total_cheque_sales - $cash_register->total_refund_cheque;
            $cr_data[$currency['currency_id']]['cash_register'] = $cash_register;

            if ($currency['is_default']) {
                $total_cash = $cash_register->total_cash_sales +
                    $cash_register->total_cash_in - $cash_register->total_cash_out -
                    $cash_register->total_purchases - $cash_register->total_expenses - $cash_register->total_wages_and_compensation - $cash_register->total_sell_return;
            }
        }

        return view('cash.print_closing_cash')->with(compact(
            'cash_register',
            'cr_data',
            'cash_register_id',
            'type',
            'total_cash'
        ));
    }
}
