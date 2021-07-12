<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Utils\CashRegisterUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CashController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $cashRegisterUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, CashRegisterUtil $cashRegisterUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
    }


    public function addCash()
    {
        //Check if there is a open register, if yes then redirect to POS screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action('CashRegisterController@create');
        }


        $cash_register = CashRegister::where('user_id', Auth::user()->id)->where('status', 'open')->first();

        return view('cash.add_cash')->with(compact(
            'cash_register'
        ));
    }

    public function saveAddCash(Request $request)
    {
        try {
            $amount = $this->cashRegisterUtil->num_uf($request->input('amount'));
            $register = CashRegister::find($request->cash_register_id);
            $register->cash_register_transactions()->create([
                'amount' => $amount,
                'pay_method' => 'cash',
                'type' => 'credit',
                'transaction_type' => 'add_cash'
            ]);
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
}
