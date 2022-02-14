<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\EarningOfPoint;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\RedemptionOfPoint;
use App\Models\Store;
use App\Models\Transaction;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RedemptionOfPointController extends Controller
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
        $redemption_of_points = RedemptionOfPoint::get();
        $stores = Store::getDropdown();

        return view('redemption_of_point.index')->with(compact(
            'redemption_of_points',
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
        $product_classes = ProductClass::get();
        $earning_of_points = EarningOfPoint::pluck('number', 'id');

        return view('redemption_of_point.create')->with(compact(
            'stores',
            'product_classes',
            'products',
            'earning_of_points'
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
            ['earning_of_point_ids' => ['required', 'max:255']],
            ['value_of_1000_points' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', 'search_product');
            $data['created_by'] = Auth::user()->id;
            $data['number'] = $this->productUtil->getNumberByType('redemption_of_point');
            $data['product_ids'] = $this->productUtil->extractProductIdsfromProductTree($request->pct);
            $data['pct_data'] = $request->pct ?? [];
            $data['start_date'] = !empty($request->start_date) ? $request->start_date : null;
            $data['end_date'] = !empty($request->end_date) ? $request->end_date : null;

            DB::beginTransaction();

            $redemption_of_point = RedemptionOfPoint::create($data);


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

        return redirect()->to('redemption-of-points')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $redemption_of_point = RedemptionOfPoint::find($id);

        return view('redemption_of_point.show')->with(compact(
            'redemption_of_point',
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
        $redemption_of_point = RedemptionOfPoint::find($id);
        $stores = Store::getDropdown();
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $product_classes = ProductClass::get();
        $pct_data = $redemption_of_point->pct_data;
        $earning_of_points = EarningOfPoint::pluck('number', 'id');

        return view('redemption_of_point.edit')->with(compact(
            'redemption_of_point',
            'pct_data',
            'product_classes',
            'stores',
            'products',
            'earning_of_points'
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
            ['earning_of_point_ids' => ['required', 'max:255']],
            ['value_of_1000_points' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method', 'pct', 'search_product');
            $data['product_ids'] = $this->productUtil->extractProductIdsfromProductTree($request->pct);
            $data['pct_data'] = $request->pct ?? [];
            DB::beginTransaction();

            RedemptionOfPoint::where('id', $id)->update($data);


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

        return redirect()->to('redemption-of-points')->with('status', $output);
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
            RedemptionOfPoint::find($id)->delete();
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
     * return the list of redeemed point sales
     *
     * @return void
     */
    public function getListOfRedeemedPoint()
    {
        $transactions = Transaction::where('rp_redeemed', '>', 0)->orderBy('transaction_date', 'desc')->get();

        return view('redemption_of_point.get_list_of_redeemed_points')->with(compact(
            'transactions'
        ));
    }
}
