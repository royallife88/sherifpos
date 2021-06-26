<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\CustomerTypePoint;
use App\Models\CustomerTypeStore;
use App\Models\Store;
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
        $customer_types = CustomerType::get();
        $stores = Store::pluck('name', 'id');

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

        $customer_types = CustomerType::pluck('name', 'id');
        $stores = Store::pluck('name', 'id');
        // $products = Product::pluck('name', 'id');
        $products = [];

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
            ['value_of_1000_points' => ['required', 'max:255']],
        );

        try {
            $data = $request->only('name', 'value_of_1000_points');
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

            foreach ($request->product_point as $product_point) {
                if (!empty($product_point['product_id']) && $product_point['discount']) {
                    CustomerTypePoint::create([
                        'customer_type_id' => $customer_type_id,
                        'product_id' => $product_point['product_id'],
                        'point' => $product_point['point']
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
        $customer_type = CustomerType::find($id);
        $stores = Store::pluck('name', 'id');
        // $products = Product::pluck('name', 'id');
        $products = [];
// print_r($stores); die();
        return view('customer_type.show')->with(compact(
            'customer_type',
            'stores',
            'products',
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
        $stores = Store::pluck('name', 'id');
        // $products = Product::pluck('name', 'id');
        $products = [];

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
            ['value_of_1000_points' => ['required', 'max:255']],
        );

        try {
            $data = $request->only('name', 'value_of_1000_points');
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

            foreach ($request->product_point as $product_point) {
                if (!empty($product_point['product_id']) && $product_point['discount']) {
                    CustomerTypePoint::updateOrCreate([
                        'customer_type_id' => $customer_type_id,
                        'product_id' => $product_point['product_id'],
                    ], [

                        'point' => $product_point['point']
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
        $customer_type = CustomerType::pluck('name', 'id');
        $customer_type_dp = $this->commonUtil->createDropdownHtml($customer_type, 'Please Select');

        return $customer_type_dp;
    }
    public function getProductDiscountRow()
    {
        $row_id = request()->row_id;
        // $products = Product::pluck('name', 'id');
        $products = [];

        return view('customer_type.partial.product_discount_row')->with(compact(
            'products',
            'row_id'
        ));
    }
    public function getProductPointRow()
    {
        $row_id = request()->row_id;
        // $products = Product::pluck('name', 'id');
        $products = [];

        return view('customer_type.partial.product_point_row')->with(compact(
            'products',
            'row_id'
        ));
    }
}
