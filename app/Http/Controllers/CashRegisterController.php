<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\StorePos;
use App\Models\User;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashRegisterController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $notificationUtil;
    protected $cashRegisterUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, NotificationUtil $notificationUtil, CashRegisterUtil $cashRegisterUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Check if there is a open register, if yes then redirect to POS screen.
        if ($this->cashRegisterUtil->countOpenedRegister() != 0) {
            return redirect()->action('SellPosController@create');
        }

        $is_pos = request()->is_pos;
        $users = User::pluck('name', 'id');

        return view('cash_register.create')->with(compact('is_pos', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $initial_amount = 0;
            if (!empty($request->input('amount'))) {
                $initial_amount = $this->cashRegisterUtil->num_uf($request->input('amount'));
            }
            DB::beginTransaction();
            $user_id = Auth::user()->id;
            $store_pos = StorePos::where('user_id', $user_id)->first();

            $register = CashRegister::create([
                'user_id' => $user_id,
                'status' => 'open',
                'store_id' => !empty($store_pos) ? $store_pos->store_id : null,
                'store_pos_id' => !empty($store_pos) ? $store_pos->id : null
            ]);
            $cash_register_transaction = $this->cashRegisterUtil->createCashRegisterTransaction($register, $initial_amount, 'cash_in', 'debit', $request->source_id, $request->notes);

            if (!empty($request->source_id)) {
                $register = $this->cashRegisterUtil->getCurrentCashRegisterOrCreate($request->source_id);
                $cash_register_transaction_out = $this->cashRegisterUtil->createCashRegisterTransaction($register, $initial_amount, 'cash_out', 'debit', $request->source_id, $request->notes);
            }
            if ($request->has('image')) {
                $cash_register_transaction->addMedia($request->image)->toMediaCollection('cash_register');
                $cash_register_transaction_out->addMedia($request->image)->toMediaCollection('cash_register');
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }

        return redirect()->action('SellPosController@create');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
