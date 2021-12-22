<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerTypeStore;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerTypeController extends Controller
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
        $customer_types = CustomerType::leftjoin('customers', 'customer_types.id', 'customers.customer_type_id')
            ->leftjoin('transactions', 'customers.id', 'transactions.customer_id')
            ->select(
                'customer_types.*',
                DB::raw('SUM(IF(transactions.type="sell", total_sp_discount, 0)) as total_sp_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_product_discount, 0)) as total_product_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_coupon_discount, 0)) as total_coupon_discount'),
                DB::raw('SUM(IF(transactions.type="sell", rp_earned, 0)) as total_rp_earned'),
            )->groupBy('customer_types.id')->get();
        $stores = Store::getDropdown();

        return view('customer_type.index')->with(compact(
            'customer_types',
            'stores',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quick_add = request()->quick_add ?? null;

        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');

        return view('customer_type.create')->with(compact(
            'quick_add',
            'customer_types',
            'stores',
            'products',
        ));
    }

    /**
     * store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
        );

        try {
            $data = $request->only('name');
            $data['created_by'] = Auth::user()->id;
            DB::beginTransaction();

            $customer_type = CustomerType::create($data);

            $customer_type_id = $customer_type->id;

            foreach ($request->stores as $store) {
                if (!empty($store)) {
                    CustomerTypeStore::create([
                        'customer_type_id' => $customer_type_id,
                        'store_id' => $store,
                    ]);
                }
            }
            //TODO:: caclulate the reward points and discount


            DB::commit();
            $output = [
                'success' => true,
                'customer_type_id' => $customer_type_id,
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

        return redirect()->to('customer-type')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer_type_id = $id;
        $customer_type = CustomerType::find($id);

        $discount_query = Transaction::leftjoin('customers', 'transactions.customer_id', 'customers.id')
            ->whereIn('transactions.type', ['sell'])
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
        if (!empty($customer_type_id)) {
            $discount_query->where('customers.customer_type_id', $customer_type_id);
        }
        $discounts = $discount_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $query = Customer::leftjoin('transactions', 'customers.id', 'transactions.customer_id')
            ->where('customers.customer_type_id', $customer_type_id)
            ->select(
                'customers.*',
                DB::raw('SUM(IF(transactions.type="sell", final_total, 0)) as total_purchase'),
                DB::raw('SUM(IF(transactions.type="sell", total_sp_discount, 0)) as total_sp_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_product_discount, 0)) as total_product_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_coupon_discount, 0)) as total_coupon_discount'),
            );
        $customers = $query->groupBy('customers.id')->get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('customer_type.show')->with(compact(
            'discounts',
            'customers',
            'customer_type',
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
        $customer_type = CustomerType::find($id);
        $stores = Store::getDropdown();
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');

        return view('customer_type.edit')->with(compact(
            'customer_type',
            'stores',
            'products',
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
            ['name' => ['required', 'max:255']],
        );

        try {
            $data = $request->only('name');
            DB::beginTransaction();

            $customer_type = CustomerType::where('id', $id)->update($data);

            $customer_type_id = $id;

            foreach ($request->stores as $store) {
                if (!empty($store)) {
                    CustomerTypeStore::updateOrCreate([
                        'customer_type_id' => $customer_type_id,
                        'store_id' => $store,
                    ], [
                        'customer_type_id' => $customer_type_id,
                        'store_id' => $store,
                    ]);
                }
            }

            DB::commit();
            $output = [
                'success' => true,
                'customer_type_id' => $customer_type_id,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->to('customer-type')->with('status', $output);
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
            CustomerType::find($id)->delete();
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
        $customer_type = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_type_dp = $this->commonUtil->createDropdownHtml($customer_type, 'Please Select');

        return $customer_type_dp;
    }
    public function getProductDiscountRow()
    {
        $row_id = request()->row_id;
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');

        return view('customer_type.partial.product_discount_row')->with(compact(
            'products',
            'row_id'
        ));
    }
    public function getProductPointRow()
    {
        $row_id = request()->row_id;
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');

        return view('customer_type.partial.product_point_row')->with(compact(
            'products',
            'row_id'
        ));
    }
}
