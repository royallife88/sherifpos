<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\Product;
use App\Models\SalesPromotion;
use App\Models\Store;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesPromotionController extends Controller
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
        $sales_promotions = SalesPromotion::get();
        $stores = Store::pluck('name', 'id');

        return view('sales_promotion.index')->with(compact(
            'sales_promotions',
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
        $stores = Store::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types  = CustomerType::pluck('name', 'id');

        return view('sales_promotion.create')->with(compact(
            'stores',
            'products',
            'customer_types'
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
            ['store_ids' => ['required', 'max:255']],
            ['customer_type_ids' => ['required', 'max:255']],
            ['discount_type' => ['required', 'max:255']],
            ['discount_value' => ['required', 'max:255']],
            ['start_date' => ['required', 'max:255']],
            ['end_date' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token');
            $data['created_by'] = Auth::user()->id;
            $data['product_condition'] = !empty($request->product_condition) ? 1 : 0;
            $data['purchase_condition'] = !empty($request->purchase_condition) ? 1 : 0;
            $data['product_ids'] = !empty($request->product_ids) ? $request->product_ids : [];
            $data['discount_value'] = !empty($request->discount_value) ? $this->productUtil->num_uf($request->discount_value) : 0;
            $data['purchase_condition_amount'] = !empty($request->purchase_condition_amount) ? $this->productUtil->num_uf($request->purchase_condition_amount) : 0;

            DB::beginTransaction();

            SalesPromotion::create($data);


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

        return redirect()->to('sales-promotion')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sales_promotion = SalesPromotion::find($id);

        return view('sales_promotion.show')->with(compact(
            'sales_promotion'
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
        $sales_promotion = SalesPromotion::find($id);
        $stores = Store::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types  = CustomerType::pluck('name', 'id');

        return view('sales_promotion.edit')->with(compact(
            'sales_promotion',
            'stores',
            'products',
            'customer_types'
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
            ['store_ids' => ['required', 'max:255']],
            ['customer_type_ids' => ['required', 'max:255']],
            ['discount_type' => ['required', 'max:255']],
            ['discount_value' => ['required', 'max:255']],
            ['start_date' => ['required', 'max:255']],
            ['end_date' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method');
            $data['created_by'] = Auth::user()->id;
            $data['product_condition'] = !empty($request->product_condition) ? 1 : 0;
            $data['purchase_condition'] = !empty($request->purchase_condition) ? 1 : 0;
            $data['product_ids'] = !empty($request->product_ids) ? $request->product_ids : [];
            $data['discount_value'] = !empty($request->discount_value) ? $this->productUtil->num_uf($request->discount_value) : 0;
            $data['purchase_condition_amount'] = !empty($request->purchase_condition_amount) ? $this->productUtil->num_uf($request->purchase_condition_amount) : 0;

            DB::beginTransaction();

            SalesPromotion::where('id', $id)->update($data);


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

        return redirect()->to('sales-promotion')->with('status', $output);
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
            SalesPromotion::find($id)->delete();
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
