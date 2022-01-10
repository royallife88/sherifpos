<?php

namespace App\Http\Controllers;

use App\Models\CashInAdjustment;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Store;
use App\Models\StorePos;
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
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id');

        if (!auth()->user()->can('superadmin') || !auth()->user()->can('cash.view_details.view')) {
            $query->where('user_id', Auth::user()->id);
        }

        if (!empty(request()->start_date)) {
            $query->whereDate('cash_registers.created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('cash_registers.created_at', '<=', request()->end_date);
        }
        if (!empty(request()->store_id)) {
            $query->where('store_id', request()->store_id);
        }
        if (!empty(request()->store_pos_id)) {
            $query->where('store_pos_id', request()->store_pos_id);
        }

        $cash_registers = $query->select(
            'cash_registers.*',
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND type = 'credit', amount, 0)) as total_cash_sales"),
            DB::raw("SUM(IF(transaction_type = 'cash_in' AND pay_method = 'cash', amount, 0)) as total_cash_in"),
            DB::raw("SUM(IF(transaction_type = 'cash_out' AND pay_method = 'cash', amount, 0)) as total_cash_out")
        )
            ->groupBy('cash_registers.id')->orderBy('cash_registers.created_at', 'desc')->get();


        $stores = Store::getDropdown();
        $store_pos = StorePos::orderBy('name', 'asc')->pluck('name', 'id');

        return view('cash.index')->with(compact(
            'cash_registers',
            'stores',
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
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id');

        $query->where('cash_registers.id', $id);
        if (!empty(request()->start_date)) {
            $query->whereDate('created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('created_at', '<=', request()->end_date);
        }


        $cash_register = $query->select(
            'cash_registers.*',
            DB::raw("SUM(IF(transaction_type = 'sell', amount, 0)) as total_sale"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND type = 'credit', amount, 0)) as total_cash_sales"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'card' AND type = 'credit', amount, 0)) as total_card_sales"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'bank_transfer' AND type = 'credit', amount, 0)) as total_bank_transfer_sales"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'gift_card' AND type = 'credit', amount, 0)) as total_gift_card_sales"),
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cheque' AND type = 'credit', amount, 0)) as total_cheque_sales"),
            DB::raw("SUM(IF(transaction_type = 'expense', amount, 0)) as total_expenses"),
            DB::raw("SUM(IF(transaction_type = 'sell_return', amount, 0)) as total_return_sales"),
            DB::raw("SUM(IF(transaction_type = 'cash_in' AND pay_method = 'cash', amount, 0)) as total_cash_in"),
            DB::raw("SUM(IF(transaction_type = 'cash_out' AND pay_method = 'cash', amount, 0)) as total_cash_out")
        )
            ->first();

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
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
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
            $user_id = $register->user_id;
            $cash_register_transaction = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_in', 'debit', $request->source_id, $request->notes);

            if (!empty($request->source_id)) {
                $register = $this->cashRegisterUtil->getCurrentCashRegisterOrCreate($request->source_id);
                $cash_register_transaction_out = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_out', 'credit', $user_id, $request->notes, $cash_register_transaction->id);
                $cash_register_transaction->referenced_id = $cash_register_transaction_out->id;
                $cash_register_transaction->save();
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
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
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
            $user_id = $register->user_id;
            $cash_register_transaction = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_out', 'credit', $request->source_id, $request->notes);

            if (!empty($request->source_id)) {
                $register = $this->cashRegisterUtil->getCurrentCashRegisterOrCreate($request->source_id);
                $cash_register_transaction_in = $this->cashRegisterUtil->createCashRegisterTransaction($register, $amount, 'cash_in', 'debit', $user_id, $request->notes, $cash_register_transaction->id);
                $cash_register_transaction->referenced_id = $cash_register_transaction_in->id;
                $cash_register_transaction->save();
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
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action('CashRegisterController@create');
        }

        $type = request()->get('type');
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id');
        $query->where('cash_registers.id', $cash_register_id);

        $cash_register = $query->select(
            'cash_registers.*',
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND type = 'credit', amount, 0)) as total_cash_sales"),
        )->first();

        $cash_in_amount = CashRegisterTransaction::where('cash_register_id', $cash_register_id)->where('transaction_type', 'cash_in')->sum('amount');
        $cash_out_amount = CashRegisterTransaction::where('cash_register_id', $cash_register_id)->where('transaction_type', 'cash_out')->sum('amount');

        $total_cash = $cash_register->total_cash_sales + $cash_in_amount - $cash_out_amount;

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        return view('cash.add_closing_cash')->with(compact(
            'cash_register',
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
}
