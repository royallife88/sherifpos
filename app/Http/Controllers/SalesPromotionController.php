<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\Product;
use App\Models\SalesPromotion;
use App\Models\Store;
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
        $earning_of_points = SalesPromotion::get();
        $stores = Store::pluck('name', 'id');

        return view('earning_of_point.index')->with(compact(
            'earning_of_points',
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

        return view('earning_of_point.create')->with(compact(
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
            ['store_ids' => ['required', 'max:255']],
            ['customer_type_ids' => ['required', 'max:255']],
            ['product_ids' => ['required', 'max:255']],
            ['points_on_per_amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token');
            $data['created_by'] = Auth::user()->id;
            $data['number'] = $this->productUtil->getNumberByType('earning_of_point');
            DB::beginTransaction();

            $earning_of_point = SalesPromotion::create($data);


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

        return redirect()->to('earning-of-points')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $earning_of_point = SalesPromotion::find($id);

        return view('earning_of_point.show')->with(compact(
            'earning_of_point'
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
        $earning_of_point = SalesPromotion::find($id);
        $stores = Store::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types  = CustomerType::pluck('name', 'id');

        return view('earning_of_point.edit')->with(compact(
            'earning_of_point',
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
            ['store_ids' => ['required', 'max:255']],
            ['customer_type_ids' => ['required', 'max:255']],
            ['product_ids' => ['required', 'max:255']],
            ['points_on_per_amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method');
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

        return redirect()->to('earning-of-points')->with('status', $output);
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
