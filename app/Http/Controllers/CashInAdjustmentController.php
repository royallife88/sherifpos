<?php

namespace App\Http\Controllers;

use App\Models\CashInAdjustment;
use App\Models\CashRegister;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\User;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashInAdjustmentController extends Controller
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
        $cash_in_adjustments = CashInAdjustment::get();

        return view('cash_in_adjustment.index')->with(compact(
            'cash_in_adjustments'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::getDropdown();
        $users = User::pluck('name', 'id');

        return view('cash_in_adjustment.create')->with(compact(
            'stores',
            'users',
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->except('_token');
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['current_cash'] = $this->commonUtil->num_uf($data['current_cash']);
            $data['discrepancy'] = $this->commonUtil->num_uf($data['discrepancy']);
            $data['date_and_time'] = Carbon::now();
            $data['created_by'] = Auth::user()->id;

            CashInAdjustment::create($data);

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
        $stores = Store::getDropdown();
        $users = User::pluck('name', 'id');
        $cash_in_adjustment = CashInAdjustment::find($id);

        return view('cash_in_adjustment.edit')->with(compact(
            'stores',
            'users',
            'cash_in_adjustment'
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
            $data = $request->except('_token', '_method');
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['current_cash'] = $this->commonUtil->num_uf($data['current_cash']);
            $data['discrepancy'] = $this->commonUtil->num_uf($data['discrepancy']);

            CashInAdjustment::where('id', $id)->update($data);

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
            CashInAdjustment::find($id)->delete();
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

    /**
     * get the current cash for user
     */
    public function getCashDetails($user_id)
    {
        $query = CashRegister::leftjoin('cash_register_transactions', 'cash_registers.id', 'cash_register_transactions.cash_register_id')
            ->where('cash_registers.user_id', $user_id)
            ->where('status', 'open');

        $cash_register = $query->select(
            'cash_registers.*',
            DB::raw("SUM(IF(transaction_type = 'sell' AND pay_method = 'cash' AND type = 'credit', amount, 0)) as total_cash_sales"),
        )->first();

        $total_cash = $cash_register->total_cash_sales;

        $store_pos = StorePos::where('user_id', $user_id)->first();
        $store_id = null;
        if (!empty($store_pos)) {
            $store_id = $store_pos->store_id;
        }


        return ['current_cash' => $total_cash, 'store_id' => $store_id, 'cash_register_id' => $cash_register->id];
    }
}
