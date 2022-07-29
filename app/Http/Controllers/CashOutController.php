<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\User;
use App\Utils\CashRegisterUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashOutController extends Controller
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
        $query = CashRegisterTransaction::leftjoin('cash_registers', 'cash_register_transactions.cash_register_id', 'cash_registers.id')
            ->leftjoin('users as cashier', 'cash_registers.user_id', 'cashier.id')
            ->leftjoin('employees', 'cash_registers.user_id', 'employees.user_id')
            ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id');

        if (!auth()->user()->can('superadmin') && !auth()->user()->can('cash.add_cash_out.view') && auth()->user()->is_admin != 1) {
            $query->where('cash_registers.user_id', Auth::user()->id);
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
        if (!empty(request()->user_id)) {
            $query->where('cash_registers.user_id', request()->user_id);
        }
        if (!empty(request()->receiver_id)) {
            $query->where('cash_register_transactions.source_id', request()->receiver_id);
        }


        $query->whereIn('transaction_type', ['cash_out', 'add_stock', 'expense', 'wages_and_compensation']);
        $cash_registers = $query->select(
            'cash_register_transactions.*',
            'cash_registers.user_id',
            'cashier.name as cashier_name',
            'employees.id as employee_id',
            'job_types.job_title'
        )
            ->groupBy('cash_register_transactions.id')->orderBy('created_at', 'desc')->get();

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('cash_out.index')->with(compact(
            'users',
            'cash_registers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cash_out = CashRegisterTransaction::find($id);
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('cash_out.edit')->with(compact(
            'cash_out',
            'users'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $amount = $this->commonUtil->num_uf($request->input('amount'));
            $register = CashRegister::find($request->cash_register_id);
            $user_id = $register->user_id;
            $cr_transaction = CashRegisterTransaction::where('id', $id)->first();
            DB::begintransaction();
            $referenced_id = $cr_transaction->referenced_id;

            $cash_register_transaction = $this->cashRegisterUtil->updateCashRegisterTransaction($id, $register, $amount, 'cash_out', 'credit', $request->source_id, $request->notes);

            $refercene_transaction = CashRegisterTransaction::where('id', $referenced_id)->first();

            if (!empty($request->source_id)) {
                $register = $this->cashRegisterUtil->getCurrentCashRegisterOrCreate($request->source_id);
                $cash_register_transaction_in = $this->cashRegisterUtil->updateCashRegisterTransaction($refercene_transaction->id, $register, $amount, 'cash_in', 'debit',  $user_id, $request->notes, $cash_register_transaction->id);
                $cash_register_transaction->referenced_id = $cash_register_transaction_in->id;
                $cash_register_transaction->save();
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            CashRegisterTransaction::where('id', $id)->delete();
            CashRegisterTransaction::where('referenced_id', $id)->delete();

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

        return $output;
    }
}
