<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBalanceAdjustment;
use App\Models\CustomerType;
use App\Models\Transaction;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
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
        $query = Customer::leftjoin('transactions', 'customers.id', 'transactions.customer_id')
            ->select(
                'customers.*',
                DB::raw('SUM(IF(transactions.type="sell", final_total, 0)) as total_purchase'),
                DB::raw('SUM(IF(transactions.type="sell", total_sp_discount, 0)) as total_sp_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_product_discount, 0)) as total_product_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_coupon_discount, 0)) as total_coupon_discount'),
            );

        $customers = $query->groupBy('customers.id')->get();

        $balances = [];
        foreach ($customers as $customer) {
            $balances[$customer->id] = $this->transactionUtil->getCustomerBalance($customer->id)['balance'];
        }

        return view('customer.index')->with(compact(
            'customers',
            'balances'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $customer_types = CustomerType::pluck('name', 'id');

        $quick_add = request()->quick_add ?? null;

        if ($quick_add) {
            return view('customer.quick_add')->with(compact(
                'customer_types',
                'quick_add'
            ));
        }

        return view('customer.create')->with(compact(
            'customer_types',
            'quick_add'
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
        $this->validate(
            $request,
            ['mobile_number' => ['required', 'max:255']],
            ['customer_type_id' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            $customer = Customer::create($data);

            if ($request->has('image')) {
                $customer->addMedia($request->image)->toMediaCollection('customer_photo');
            }

            $customer_id = $customer->id;

            DB::commit();
            $output = [
                'success' => true,
                'customer_id' => $customer_id,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }


        if ($request->quick_add) {
            return $output;
        }

        return redirect()->to('customer')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer_id = $id;
        $customer = Customer::find($id);

        $sale_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final']);

        if (!empty(request()->start_date)) {
            $sale_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $sale_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($customer_id)) {
            $sale_query->where('transactions.customer_id', $customer_id);
        }
        $sales = $sale_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->get();


        $discount_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final'])
            ->where(function ($q) {
                $q->where('total_sp_discount', '>', 0);
                $q->orWhere('total_product_discount', '>', 0);
                $q->orWhere('total_coupon_discount', '>', 0);
            });

        if (!empty(request()->start_date)) {
            $discount_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $discount_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($customer_id)) {
            $discount_query->where('transactions.customer_id', $customer_id);
        }
        $discounts = $discount_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $point_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final'])
            ->where(function ($q) {
                $q->where('rp_earned', '>', 0);
            });

        if (!empty(request()->start_date)) {
            $point_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $point_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($customer_id)) {
            $point_query->where('transactions.customer_id', $customer_id);
        }
        $points = $point_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $customers = Customer::pluck('name', 'id');
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $balance = $this->transactionUtil->getCustomerBalance($customer->id)['balance'];

        return view('customer.show')->with(compact(
            'sales',
            'points',
            'discounts',
            'customers',
            'customer',
            'balance'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);
        $customer_types = CustomerType::pluck('name', 'id');

        return view('customer.edit')->with(compact(
            'customer',
            'customer_types',
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
        $this->validate(
            $request,
            ['mobile_number' => ['required', 'max:255']],
            ['customer_type_id' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', '_method');

            DB::beginTransaction();
            $customer = Customer::find($id);
            $customer->update($data);

            if ($request->has('image')) {
                if ($customer->getFirstMedia('customer_photo')) {
                    $customer->getFirstMedia('customer_photo')->delete();
                }
                $customer->addMedia($request->image)->toMediaCollection('customer_photo');
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

        return redirect()->to('customer')->with('status', $output);
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
            Customer::find($id)->delete();
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

    public function getDropdown()
    {
        $customer = Customer::get();
        $html = '';
        if (!empty($append_text)) {
            $html = '<option value="">Please Select</option>';
        }
        foreach ($customer as $value) {
            $html .= '<option value="' . $value->id . '">' . $value->name  . ' ' . $value->mobile_number . '</option>';
        }

        return $html;
    }

    public function getDetailsByTransactionType($customer_id, $type)
    {
        $query = Customer::join('transactions as t', 'customers.id', 't.customer_id')
            ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
            ->where('customers.id', $customer_id);
        if ($type == 'sell') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'customers.name',
                'customers.address',
                'customers.deposit_balance',
                'customers.id as customer_id',
                'customer_types.name as customer_type'
            );
        }

        $customer_details = $query->first();

        $balance_adjustment = CustomerBalanceAdjustment::where('customer_id', $customer_id)->sum('add_new_balance');

        $customer_details->due = $customer_details->total_invoice - $customer_details->total_paid + $balance_adjustment;

        return $customer_details;
    }

    /**
     * get customer balance
     *
     * @param int $customer_id
     * @return void
     */
    public function getCustomerBalance($customer_id)
    {
        return $this->transactionUtil->getCustomerBalance($customer_id);
    }

    /**
     * Shows contact's payment due modal
     *
     * @param  int  $customer_id
     * @return \Illuminate\Http\Response
     */
    public function getPayContactDue($customer_id)
    {
        if (request()->ajax()) {

            $due_payment_type = request()->input('type');
            $query = Customer::where('customers.id', $customer_id)
                ->join('transactions AS t', 'customers.id', '=', 't.customer_id');
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'customers.name',
                'customers.mobile_number',
                'customers.id as customer_id'
            );


            $customer_details = $query->first();
            $payment_type_array = $this->commonUtil->getPaymentTypeArray();

            return view('customer.partial.pay_customer_due')
                ->with(compact('customer_details', 'payment_type_array',));
        }
    }

    /**
     * Adds Payments for Contact due
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPayContactDue(Request  $request)
    {
        try {
            DB::beginTransaction();

            $this->transactionUtil->payCustomer($request);

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
}
