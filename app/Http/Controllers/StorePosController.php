<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StorePos;
use App\Models\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StorePosController extends Controller
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
        $store_poses = StorePos::get();

        $query = StorePos::leftjoin('transactions', function ($join) {
            $join->on('store_pos.id', 'transactions.store_pos_id')->whereIn('type', ['sell', 'sell_return']);
        })
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id');
        $query->select(
            'store_pos.*',
            DB::raw('SUM(IF(transactions.type="sell" AND status="final", final_total, 0)) as total_sales'),
            DB::raw('SUM(IF(transactions.type="sell_return" AND status="final", final_total, 0)) as total_sales_return'),
            DB::raw('SUM(IF(transactions.delivery_cost > 0  AND status="final", final_total, 0)) as total_delivery_sales'),
            DB::raw('SUM(IF(method="cash", amount, 0)) as total_cash'),
            DB::raw('SUM(IF(method="card", amount, 0)) as total_card'),
            DB::raw('COUNT(CASE WHEN transactions.status!="final" THEN 1 END) as pending_orders'),
            DB::raw('SUM(IF(transactions.type="sell" AND transactions.payment_status="pending", final_total, 0)) as pay_later_sales')

        );

        $store_poses = $query->groupBy('store_pos.id')->get();

        return view('store_pos.index')->with(compact(
            'store_poses'
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

        $stores = Store::getDropdown();
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('store_pos.create')->with(compact(
            'quick_add',
            'stores',
            'users'
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
            ['name' => ['required', 'max:255']],
            ['store_id' => ['required', 'max:255']],
            ['user_id' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['created_by'] = Auth::user()->id;
            DB::beginTransaction();

            $store_pos = StorePos::create($data);

            $store_pos_id = $store_pos->id;

            DB::commit();
            $output = [
                'success' => true,
                'store_id' => $store_pos_id,
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
        $store_pos = StorePos::find($id);

        $stores = Store::getDropdown();
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('store_pos.edit')->with(compact(
            'store_pos',
            'stores',
            'users'
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
            ['store_id' => ['required', 'max:255']],
            ['user_id' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', 'quick_add', '_method');
            DB::beginTransaction();

            $store_pos = StorePos::where('id', $id)->update($data);

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
            StorePos::find($id)->delete();
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
     * get store pos details by store id
     *
     * @param int $store_id
     * @return void
     */
    public function getPosDetailsByStore($store_id)
    {
        $store_pos = StorePos::where('store_id', $store_id)->where('user_id', Auth::user()->id)->first();
        if(empty($store_pos)){
            $store_pos = StorePos::where('store_id', $store_id)->first();
        }
        return $store_pos;
    }
}
