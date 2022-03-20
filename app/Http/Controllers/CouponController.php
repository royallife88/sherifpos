<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\Store;
use App\Models\User;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Coupon::where('id', '>', 0);

        if (!empty(request()->type)) {
            $query->where('type', request()->type);
        }
        if (!empty(request()->created_by)) {
            $query->where('created_by', request()->created_by);
        }
        if (!empty(request()->start_date)) {
            $query->whereDate('created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('created_at', '<=', request()->end_date);
        }
        if (!empty(request()->status)) {
            $query->where('used', request()->status);
        }

        $coupons = $query->get();

        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('coupon.index')->with(compact(
            'coupons',
            'users',
            'customers',
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

        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $product_classes = ProductClass::get();
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $code = $this->generateCode();

        return view('coupon.create')->with(compact(
            'quick_add',
            'products',
            'customer_types',
            'stores',
            'code',
            'product_classes'
        ));
    }

    /**
     * coupon a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate(
            $request,
            ['coupon_code' => ['required', 'max:255']],
            ['type' => ['required', 'max:255']],
            ['amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', 'quick_add', 'search_product');
            $data['amount_to_be_purchase_checkbox'] = !empty($data['amount_to_be_purchase_checkbox']) ? 1 : 0;
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['all_products'] = !empty($data['all_products']) ? 1 : 0;
            $data['active'] = 1;
            $data['expiry_date'] = !empty($data['expiry_date']) ? $this->commonUtil->uf_date($data['expiry_date']) : null;
            $data['created_by'] = Auth::user()->id;
            $data['used'] = 0;
            $data['product_ids'] = $this->productUtil->extractProductIdsfromProductTree($request->pct);
            $data['pct_data'] = $request->pct;
            DB::beginTransaction();

            $coupon = Coupon::create($data);

            $coupon_id = $coupon->id;

            DB::commit();
            $output = [
                'success' => true,
                'coupon_id' => $coupon_id,
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
        $coupon = Coupon::find($id);

        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $product_classes = ProductClass::get();
        $pct_data = $coupon->pct_data;
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();

        return view('coupon.edit')->with(compact(
            'coupon',
            'products',
            'product_classes',
            'customer_types',
            'stores',
            'pct_data'
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
            ['coupon_code' => ['required', 'max:255']],
            ['type' => ['required', 'max:255']],
            ['amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method', 'pct', 'submit', 'search_product');
            $data['amount_to_be_purchase_checkbox'] = !empty($data['amount_to_be_purchase_checkbox']) ? 1 : 0;
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['all_products'] = !empty($data['all_products']) ? 1 : 0;
            $data['active'] = 1;
            $data['expiry_date'] = !empty($data['expiry_date']) ? $this->commonUtil->uf_date($data['expiry_date']) : null;
            $data['created_by'] = Auth::user()->id;
            $data['used'] = 0;
            $data['product_ids'] = $this->productUtil->extractProductIdsfromProductTree($request->pct);
            $data['pct_data'] = $request->pct;
            DB::beginTransaction();

            $coupon = Coupon::where('id', $id)->update($data);

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


        if ($request->quick_add) {
            return $output;
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
            Coupon::find($id)->delete();
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
        $coupon = Coupon::orderBy('name', 'asc')->pluck('name', 'id');
        $coupon_dp = $this->commonUtil->createDropdownHtml($coupon, 'Please Select');

        return $coupon_dp;
    }

    public function generateCode()
    {
        $date = date('Y-m-d');
        $coupon_count = Coupon::whereDate('created_at', $date)->count() + 1;
        $count = str_pad($coupon_count, 2, '0', STR_PAD_LEFT);
        $id = date('Y') . date('m') . date('d') . $count;
        return $id;
    }

    public function toggleActive($id)
    {
        try {
            $coupon = Coupon::where('id', $id)->first();
            $coupon->active = !$coupon->active;

            $coupon->save();
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

    public function getDetails($coupon_code, $customer_id)
    {
        $store_id = request()->get('store_id');
        $customer = Customer::find($customer_id);
        $customer_type_id = (string) $customer->customer_type_id;
        $coupon_details = Coupon::where('coupon_code', $coupon_code)->whereJsonContains('customer_type_ids', $customer_type_id)->whereJsonContains('store_ids', $store_id)->where('used', 0)->first();

        if (empty($coupon_details)) {
            return [
                'success' => false,
                'msg' => __('lang.invalid_coupon_code')
            ];
        }
        if ($coupon_details->active == 0) {
            return [
                'success' => false,
                'msg' => __('lang.coupon_suspended')
            ];
        }
        if (!empty($coupon_details->expiry_date)) {
            if (Carbon::now()->gt(Carbon::parse($coupon_details->expiry_date))) {
                return [
                    'success' => false,
                    'msg' => __('lang.coupon_expired')
                ];
            }
        }

        return [
            'success' => true,
            'data' => $coupon_details->toArray()
        ];
    }
}
