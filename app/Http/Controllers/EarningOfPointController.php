<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\EarningOfPoint;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\Store;
use App\Models\Transaction;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EarningOfPointController extends Controller
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
        $earning_of_points = EarningOfPoint::get();
        $stores = Store::getDropdown();

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
        $stores = Store::getDropdown();
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types  = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $product_classes = ProductClass::get();

        return view('earning_of_point.create')->with(compact(
            'stores',
            'products',
            'customer_types',
            'product_classes'
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
            ['points_on_per_amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', 'search_product');
            $data['created_by'] = Auth::user()->id;
            $data['number'] = $request->number;
            $data['product_ids'] = $this->productUtil->extractProductIdsfromProductTree($request->pct);
            $data['pct_data'] = $request->pct ?? [];
            $data['start_date'] = !empty($request->start_date) ? $request->start_date : null;
            $data['end_date'] = !empty($request->end_date) ? $request->end_date : null;

            DB::beginTransaction();

            $earning_of_point = EarningOfPoint::create($data);


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
        $earning_of_point = EarningOfPoint::find($id);

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
        $earning_of_point = EarningOfPoint::find($id);
        $stores = Store::getDropdown();
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types  = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $product_classes = ProductClass::get();
        $pct_data = $earning_of_point->pct_data;

        return view('earning_of_point.edit')->with(compact(
            'earning_of_point',
            'stores',
            'products',
            'customer_types',
            'product_classes',
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
            ['store_ids' => ['required', 'max:255']],
            ['customer_type_ids' => ['required', 'max:255']],
            ['points_on_per_amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method', 'pct', 'search_product');
            $data['product_ids'] = $this->productUtil->extractProductIdsfromProductTree($request->pct);
            $data['pct_data'] = $request->pct ?? [];
            DB::beginTransaction();

            EarningOfPoint::where('id', $id)->update($data);


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
            EarningOfPoint::find($id)->delete();
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
     * return the list of earned point sales
     *
     * @return void
     */
    public function getListOfEarnedPoint()
    {
        $transactions = Transaction::where('rp_earned', '>', 0)->orderBy('transaction_date', 'desc')->get();

        return view('earning_of_point.get_list_of_earned_points')->with(compact(
            'transactions'
        ));
    }
}
