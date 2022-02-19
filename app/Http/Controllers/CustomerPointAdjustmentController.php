<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPointAdjustment;
use App\Models\Store;
use App\Models\User;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerPointAdjustmentController extends Controller
{
     /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_point_adjustments = CustomerPointAdjustment::get();

        return view('customer_point_adjustment.index')->with(compact(
            'customer_point_adjustments'
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
        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');

        return view('customer_point_adjustment.create')->with(compact(
            'stores',
            'users',
            'customers',
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
            $data['current_balance'] = $this->commonUtil->num_uf($data['current_balance']);
            $data['add_new_balance'] = $this->commonUtil->num_uf($data['add_new_balance']);
            $data['new_balance'] = $this->commonUtil->num_uf($data['new_balance']);
            $data['date_and_time'] = Carbon::now();
            $data['created_by'] = Auth::user()->id;

            CustomerPointAdjustment::create($data);

            $this->transactionUtil->updateCustomerRewardPoints($data['customer_id'], $data['add_new_balance'], 0, 0, 0);

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
        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_point_adjustment = CustomerPointAdjustment::find($id);

        return view('customer_point_adjustment.edit')->with(compact(
            'stores',
            'users',
            'customers',
            'customer_point_adjustment'
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
            $data['current_balance'] = $this->commonUtil->num_uf($data['current_balance']);
            $data['add_new_balance'] = $this->commonUtil->num_uf($data['add_new_balance']);
            $data['new_balance'] = $this->commonUtil->num_uf($data['new_balance']);

            $cpa = CustomerPointAdjustment::where('id', $id)->first();
            $balance_before = $cpa->add_new_balance;
            $cpa->update($data);

            $this->transactionUtil->updateCustomerRewardPoints($data['customer_id'], $data['add_new_balance'], $balance_before, 0, 0);
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
            CustomerPointAdjustment::find($id)->delete();
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
