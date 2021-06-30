<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
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
        $query = Coupon::where('id', '>', 0);

        if (!empty(request()->type)) {
            $query->where('type', request()->type);
        }
        if (!empty(request()->created_by)) {
            $query->where('created_by', request()->created_by);
        }
        if (!empty(request()->start_date)) {
            $query->where('created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->where('created_at', '<=', request()->end_date);
        }

        $coupons = $query->get();

        $customers = Customer::pluck('name', 'id');
        $users = User::pluck('name', 'id');

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

        $products = Product::pluck('name', 'id');

        return view('coupon.create')->with(compact(
            'quick_add',
            'products'
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
            ['product_ids' => ['required']],
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['amount_to_be_purchase_checkbox'] = !empty($data['amount_to_be_purchase_checkbox']) ? 1 : 0;
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['all_products'] = !empty($data['all_products']) ? 1 : 0;
            $data['active'] = 1;
            $data['expiry_date'] = !empty($data['expiry_date']) ? $this->commonUtil->uf_date($data['expiry_date']) : null;
            $data['created_by'] = Auth::user()->id;
            $data['used'] = 0;
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

        $products = Product::pluck('name', 'id');

        return view('coupon.edit')->with(compact(
            'coupon',
            'products'
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
            ['product_ids' => ['required']],
        );

        try {
            $data = $request->except('_token', '_method');
            $data['amount_to_be_purchase_checkbox'] = !empty($data['amount_to_be_purchase_checkbox']) ? 1 : 0;
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['all_products'] = !empty($data['all_products']) ? 1 : 0;
            $data['active'] = 1;
            $data['expiry_date'] = !empty($data['expiry_date']) ? $this->commonUtil->uf_date($data['expiry_date']) : null;
            $data['created_by'] = Auth::user()->id;
            $data['used'] = 0;
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
        $coupon = Coupon::pluck('name', 'id');
        $coupon_dp = $this->commonUtil->createDropdownHtml($coupon, 'Please Select');

        return $coupon_dp;
    }

    public function generateCode()
    {
        $id = \Keygen\Keygen::alphanum(10)->generate();
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

    public function getDetails($coupon_code){
        $coupon_details = Coupon::where('coupon_code', $coupon_code)->where('used', 0)->first();

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
