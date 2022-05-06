<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Store;
use App\Models\System;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExchangeRateController extends Controller
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
        $exchange_rates = ExchangeRate::get();

        return view('exchange_rate.index')->with(compact(
            'exchange_rates'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $system_currency = System::getProperty('currency');

        $default_currency = null;
        if (!empty($system_currency)) {
            $default_currency = Currency::find($system_currency);
        }

        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $currencies_all  = $this->commonUtil->allCurrencies();
        $currencies_excl  = $this->commonUtil->allCurrencies([$default_currency->id]);

        $default_store = Store::first();

        return view('exchange_rate.create')->with(compact(
            'stores',
            'default_currency',
            'default_store',
            'currencies_all',
            'currencies_excl'
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

        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => ['required', 'max:20'],
                'received_currency_id' => ['required', 'max:20'],
                'conversion_rate' => ['required', 'max:20'],
                'default_currency_id' => ['required', 'max:20'],
            ]
        );

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $data = $request->except('_token', 'quick_add');
            $data['created_by'] = auth()->user()->id;
            DB::beginTransaction();
            $exchange_rate = ExchangeRate::create($data);

            $exchange_rate_id = $exchange_rate->id;

            DB::commit();
            $output = [
                'success' => true,
                'exchange_rate_id' => $exchange_rate_id,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }


        if ($request->ajax()) {
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

        $exchange_rate = ExchangeRate::find($id);
        $system_currency = System::getProperty('currency');

        $default_currency = null;
        if (!empty($system_currency)) {
            $default_currency = Currency::find($system_currency);
        }

        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $currencies_all  = $this->commonUtil->allCurrencies();
        $currencies_excl  = $this->commonUtil->allCurrencies([$exchange_rate->default_currency_id]);

        return view('exchange_rate.edit')->with(compact(
            'exchange_rate',
            'stores',
            'default_currency',
            'currencies_all',
            'currencies_excl'
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
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => ['required', 'max:20'],
                'received_currency_id' => ['required', 'max:20'],
                'conversion_rate' => ['required', 'max:20'],
                'default_currency_id' => ['required', 'max:20'],
            ]
        );

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $data = $request->except('_token', '_method');
            DB::beginTransaction();
            $exchange_rate = ExchangeRate::find($id);
            $exchange_rate->update($data);
            $exchange_rate_id = $exchange_rate->id;

            DB::commit();
            $output = [
                'success' => true,
                'exchange_rate_id' => $exchange_rate_id,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }


        if ($request->ajax()) {
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
            ExchangeRate::find($id)->delete();
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
     * get the html dropdown for the currency
     *
     * @return void
     */
    public function getExchangeRateCurrencyDropdown()
    {
        $exchange_rate_currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);

        return $this->commonUtil->createDropdownHtml($exchange_rate_currencies);
    }
    /**
     * get the exchange rate details for the currency
     *
     * @return void
     */
    public function getExchangeRateByCurrency()
    {
        $currency_id = request()->currency_id;
        $store_id = request()->store_id;

        $default_currency_id = System::getProperty('currency');

        if ($default_currency_id == $currency_id) {
            return ['conversion_rate' => 1];
        }

        $query = ExchangeRate::where('received_currency_id', $currency_id);

        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }
        $exchange_rate = $query->first();


        return $exchange_rate;
    }
}
