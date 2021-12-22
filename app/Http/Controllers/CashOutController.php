<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterTransaction;
use App\Models\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CashOutController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
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

        if (!auth()->user()->can('superadmin') || !auth()->user()->can('cash.view_details.view')) {
            $query->where('cash_registers.user_id', Auth::user()->id);
        }

        if (!empty(request()->start_date)) {
            $query->whereDate('created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('created_at', '<=', request()->end_date);
        }

        $query->where('transaction_type', 'cash_out');
        $cash_registers = $query->select(
            'cash_register_transactions.*',
            'cash_registers.user_id',
            'cashier.name as cashier_name',
            'job_types.job_title'
        )
            ->groupBy('cash_register_transactions.id')->get();

        return view('cash_out.index')->with(compact(
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

            CashRegisterTransaction::where('id', $id)->update([
                'amount' => $amount,
                'source_id' => $request->source_id,
                'notes' => $request->notes,
            ]);

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
        //
    }
}
