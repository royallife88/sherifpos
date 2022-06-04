<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Employee;
use App\Models\ExchangeRate;
use App\Models\JobType;
use App\Models\MoneySafe;
use App\Models\MoneySafeTransaction;
use App\Models\Store;
use App\Models\System;
use App\Utils\MoneySafeUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MoneySafeTransferController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $moneysafeUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @param MoneySafeUtil $moneysafeUtil
     * @return void
     */
    public function __construct(Util $commonUtil, MoneySafeUtil $moneysafeUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moneysafeUtil = $moneysafeUtil;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    /**
     * add money to safe
     *
     * @param Request $request
     * @return void
     */
    public function getAddMoneyToSafe($id, Request $request)
    {
        $stores = Store::getDropdown();
        $emplooyes = Employee::getDropdown();
        $currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $job_types = JobType::getDropdown();
        $money_safe_id = $id;

        return view('money_safe_transfer.add_money')->with(compact(
            'stores',
            'emplooyes',
            'currencies',
            'job_types',
            'money_safe_id',
        ));
    }
    /**
     * save add money to safe
     *
     * @param Request $request
     * @return void
     */
    public function postAddMoneyToSafe($id, Request $request)
    {
        if (!auth()->user()->can('safe_module.add_money_to_safe.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'source_id' => ['required', 'max:255'],
            'store_id' => ['required', 'max:255'],
            'amount' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return redirect()->back()->with('status', $output);
        }


        try {
            $data = $request->except('_token');
            $data['money_safe_id'] = $id;
            $data['type'] = 'credit';
            $data['created_by'] = Auth::user()->id;
            $data['transaction_date'] = Carbon::now();
            $amount = $data['amount'];

            $safe = MoneySafe::find($id);
            if ($safe->currency_id != $data['currency_id']) {
                $data['amount'] = $this->moneysafeUtil->convertCurrencyAmount($amount, $data['currency_id'], $safe->currency_id, $data['store_id']);
            }

            DB::beginTransaction();
            MoneySafeTransaction::create($data);

            if ($data['source_type'] == 'safe') {
                $from_safe = MoneySafe::find($data['source_id']);
                $data['amount'] = $this->moneysafeUtil->convertCurrencyAmount($amount, $data['currency_id'], $from_safe->currency_id, $data['store_id']);
                $data['type'] = 'debit';
                $data['money_safe_id'] = $from_safe->id;
                MoneySafeTransaction::create($data);
            }

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
     * take money to safe
     *
     * @param Request $request
     * @return void
     */
    public function getTakeMoneyFromSafe($id, Request $request)
    {
        $stores = Store::getDropdown();
        $emplooyes = Employee::getDropdown();
        $currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $job_types = JobType::getDropdown();
        $money_safe_id = $id;

        return view('money_safe_transfer.take_money')->with(compact(
            'stores',
            'emplooyes',
            'currencies',
            'job_types',
            'money_safe_id',
        ));
    }
    /**
     * save take money to safe
     *
     * @param Request $request
     * @return void
     */
    public function postTakeMoneyFromSafe($id, Request $request)
    {
        if (!auth()->user()->can('safe_module.take_money_from_safe.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'source_id' => ['required', 'max:255'],
            'store_id' => ['required', 'max:255'],
            'amount' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return redirect()->back()->with('status', $output);
        }


        try {
            $data = $request->except('_token');
            $data['money_safe_id'] = $id;
            $data['type'] = 'debit';
            $data['created_by'] = Auth::user()->id;
            $data['transaction_date'] = Carbon::now();
            $amount = $data['amount'];

            $safe = MoneySafe::find($id);
            if ($safe->currency_id != $data['currency_id']) {
                $data['amount'] = $this->moneysafeUtil->convertCurrencyAmount($amount, $data['currency_id'], $safe->currency_id, $data['store_id']);
            }

            MoneySafeTransaction::create($data);

            if ($data['source_type'] == 'safe') {
                $to_safe = MoneySafe::find($data['source_id']);
                $data['amount'] = $this->moneysafeUtil->convertCurrencyAmount($amount, $data['currency_id'], $to_safe->currency_id, $data['store_id']);
                $data['type'] = 'credit';
                $data['money_safe_id'] = $to_safe->id;
                MoneySafeTransaction::create($data);
            }


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
     * get statement of the safe
     *
     * @param int $id
     * @param Request $request
     * @return void
     */
    public function getStatement($id, Request $request)
    {
        if (!auth()->user()->can('safe_module.statement.create_and_edit') || !auth()->user()->can('safe_module.statement.view')) {
            abort(403, 'Unauthorized action.');
        }


        $money_safe = MoneySafe::findOrFail($id);

        if (request()->ajax()) {
            $query = MoneySafe::leftjoin('money_safe_transactions', 'money_safes.id', '=', 'money_safe_transactions.money_safe_id')
                ->leftjoin('currencies', 'money_safe_transactions.currency_id', '=', 'currencies.id')
                ->leftjoin('stores', 'money_safe_transactions.store_id', '=', 'stores.id')
                ->leftjoin('job_types', 'money_safe_transactions.job_type_id', '=', 'job_types.id')
                ->where('money_safes.id', $id);

            if (!empty(request()->store_id)) {
                $query->where('store_id', request()->store_id);
            }
            if (!empty(request()->start_date)) {
                $query->whereDate('money_safe_transactions.transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->whereDate('money_safe_transactions.transaction_date', '<=', request()->end_date);
            }


            $money_safes = $query->select(
                'money_safe_transactions.*',
                'stores.name as store_name',
                'job_types.job_title as job_type',
                'currencies.symbol as currency',
                DB::raw('(SELECT SUM(IF(mst.type = "credit", mst.amount, -1 * mst.amount)) FROM money_safe_transactions as mst WHERE mst.money_safe_id = money_safes.id AND money_safe_transactions.id >= mst.id AND money_safe_transactions.currency_id = mst.currency_id) as balance'),
            )->orderBy('money_safe_transactions.id', 'asc')->groupBy('money_safe_transactions.id');

            return DataTables::of($money_safes)
                // ->setTotalRecords()
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->editColumn('amount', function ($row) {
                    if ($row->type == 'debit') {
                        return '<span class="text-red" data-orig_value="' . $row->amount . '" data-type="' . $row->type . '">' . $this->commonUtil->num_f($row->amount) . '</span>';
                    } else {
                        return '<span class="text-green" data-orig_value="' . $row->amount . '" data-type="' . $row->type . '">' . $this->commonUtil->num_f($row->amount) . '</span>';
                    }
                })
                ->addColumn('source', function ($row) {
                    if ($row->source_type == 'safe') {
                        return MoneySafe::find($row->source_id)->name;
                    }
                    if ($row->source_type == 'employee') {
                        return Employee::find($row->source_id)->employee_name;
                    }
                })

                ->editColumn('balance', function ($row) {
                    $balance = $this->commonUtil->num_f($row->balance);
                    $currency_id = $row->currency_id;
                    return '<span class="currency_id' . $currency_id . '" data-currency_id="' . $currency_id . '">' . $balance . '</span>';
                })
                ->editColumn('created_by_user', function ($row) {
                    return !empty($row->created_by_user) ? $row->created_by_user->name : '';
                })
                ->editColumn('edited_by_user', function ($row) {
                    return !empty($row->edited_by_user) ? $row->edited_by_user->name : '';
                })
                ->rawColumns([
                    'amount',
                    'balance',
                    'created_by',
                ])
                ->make(true);
        }

        $balance = $this->moneysafeUtil->getSafeBalance($id);

        return view('money_safe_transfer.statement')->with(compact(
            'money_safe',
            'balance',
        ));
    }
}
