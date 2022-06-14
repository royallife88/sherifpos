<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Employee;
use App\Models\MoneySafe;
use App\Models\Store;
use App\Utils\MoneySafeUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MoneySafeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $moneySafeUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, MoneySafeUtil $moneySafeUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moneySafeUtil = $moneySafeUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!auth()->user()->can('safe_module.money_safe.view')) {
            abort(403, 'Unauthorized action.');
        }

        $exchange_rate_currencies = $this->commonUtil->getExchangeRateCurrencies(true);
        if (request()->ajax()) {

            $query = MoneySafe::leftjoin('stores', 'money_safes.store_id', '=', 'stores.id')
                ->leftjoin('currencies', 'money_safes.currency_id', '=', 'currencies.id')
                ->leftjoin('money_safe_transactions', 'money_safes.id', '=', 'money_safe_transactions.money_safe_id');

            if (!empty(request()->store_id)) {
                $query->where('store_id', request()->store_id);
            }


            $money_safes = $query->select(
                'money_safes.*',
                'stores.name as store_name',
                'currencies.symbol as currency',
                DB::raw('SUM(IF(money_safe_transactions.type = "credit", money_safe_transactions.amount, -1 * money_safe_transactions.amount)) as balance'),
            )->groupBy('money_safes.id');

            return DataTables::of($money_safes)
                // ->setTotalRecords()
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                ->editColumn('type', '{{ucfirst($type)}}')

                ->addColumn('balance', function ($row) use ($exchange_rate_currencies) {
                    $html = '<div class="">';
                    foreach ($exchange_rate_currencies as $currency) {
                        $html .= '<h6>';
                        $balance = $this->moneySafeUtil->getSafeBalance($row->id, $currency['currency_id']);
                        $html .= '<span style="padding-right: 20px;" class="currency_total_ms currency_total currency_total_' . $currency['currency_id'] . '"
                            data-currency_id="' . $currency['currency_id'] . '"
                            data-is_default="' . $currency['is_default'] . '"
                            data-conversion_rate="' . $currency['conversion_rate'] . '"
                            data-base_conversion="' . $currency['conversion_rate'] * $balance . '"
                            data-orig_value="' . $balance . '">
                            <span class="symbol">
                                ' . $currency['symbol'] . '</span>
                            <span class="total">' . $this->commonUtil->num_f($balance) . '</span>
                        </span>';
                        $html .= "</h6>";
                    }
                    $html .= "</div>";
                    return $html;
                })
                ->editColumn('created_by_user', function ($row) {
                    return !empty($row->created_by_user) ? $row->created_by_user->name : '';
                })
                ->editColumn('edited_by_user', function ($row) {
                    return !empty($row->edited_by_user) ? $row->edited_by_user->name : '';
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">' . __('lang.action') . '
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('safe_module.add_money_to_safe.create_and_edit') || auth()->user()->can('superadmin')  || auth()->user()->is_admin == 1) {
                            if (in_array(auth()->user()->id, $row->add_money_users) || auth()->user()->can('superadmin')  || auth()->user()->is_admin == 1) {
                                $html .=
                                    '<li>
                                <a data-href="' . action('MoneySafeTransferController@getAddMoneyToSafe', $row->id) . '" data-container=".view_modal"
                                    class="btn btn-modal"><i class="fa fa-plus"></i> ' . __('lang.add_money') . '</a>
                                </li>';
                            }
                        }
                        if (auth()->user()->can('safe_module.take_money_to_safe.create_and_edit') || auth()->user()->can('superadmin')  || auth()->user()->is_admin == 1) {
                            if (in_array(auth()->user()->id, $row->take_money_users) || auth()->user()->can('superadmin')  || auth()->user()->is_admin == 1) {
                                $html .=
                                    '<li>
                                <a data-href="' . action('MoneySafeTransferController@getTakeMoneyFromSafe', $row->id) . '" data-container=".view_modal"
                                    class="btn btn-modal"><i class="fa fa-minus"></i> ' . __('lang.withdraw_money') . '</a>
                                </li>';
                            }
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('safe_module.statement.create_and_edit') || auth()->user()->can('safe_module.statement.view')) {
                            $html .=
                                '<li>
                                <a href="' . action('MoneySafeTransferController@getStatement', $row->id) . '" class="btn" ><i
                                        class="fa fa-file-text-o"></i> ' . __('lang.view_statement') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('safe_module.money_safe.create_and_edit')) {
                            $html .=
                                '<li>
                                <a data-href="' . action('MoneySafeController@edit', $row->id) . '" class="btn btn-modal" data-container=".view_modal"><i
                                        class="dripicons-document-edit"></i> ' . __('lang.edit') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('safe_module.money_safe.delete')) {
                            if ($row->is_default != 1) {
                                $html .=
                                    '<li>
                                    <a data-href="' . action('MoneySafeController@destroy', $row->id) . '"
                                        data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                        class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                        ' . __('lang.delete') . '</a>
                                    </li>';
                            }
                        }
                        $html .= '</div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
                    'balance',
                    'created_by',
                ])
                ->make(true);
        }

        return view('money_safe.index')->with(compact(
            'exchange_rate_currencies'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('safe_module.money_safe.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $money_safes = MoneySafe::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::pluck('name', 'id');
        $currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $employees = Employee::getDropdown();

        return view('money_safe.create')->with(compact(
            'money_safes',
            'stores',
            'currencies',
            'employees',
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
        if (!auth()->user()->can('safe_module.money_safe.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:255'],
            'store_id' => ['required', 'max:255'],
            'currency_id' => ['required', 'max:255'],
            'type' => ['required', 'max:255'],
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $data = $request->except('_token', 'quick_add');
            $data['add_money_users'] = !empty($data['add_money_users']) ? $data['add_money_users'] : [];
            $data['take_money_users'] = !empty($data['take_money_users']) ? $data['take_money_users'] : [];
            $data['created_by'] = Auth::user()->id;
            DB::beginTransaction();
            $money_safe = MoneySafe::create($data);

            $money_safe_id = $money_safe->id;

            DB::commit();
            $output = [
                'success' => true,
                'money_safe_id' => $money_safe_id,
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
        if (!auth()->user()->can('safe_module.money_safe.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }


        $money_safe = MoneySafe::find($id);
        $money_safes = MoneySafe::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::pluck('name', 'id');
        $currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $employees = Employee::getDropdown();

        return view('money_safe.edit')->with(compact(
            'money_safe',
            'money_safes',
            'stores',
            'currencies',
            'employees',
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
        if (!auth()->user()->can('safe_module.money_safe.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:255'],
            'store_id' => ['required', 'max:255'],
            'currency_id' => ['required', 'max:255'],
            'type' => ['required', 'max:255'],
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $data = $request->except('_token', '_method');
            $data['add_money_users'] = !empty($data['add_money_users']) ? $data['add_money_users'] : [];
            $data['take_money_users'] = !empty($data['take_money_users']) ? $data['take_money_users'] : [];
            $data['edited_by'] = Auth::user()->id;
            DB::beginTransaction();
            MoneySafe::where('id', $id)->update($data);


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
        if (!auth()->user()->can('safe_module.money_safe.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            MoneySafe::find($id)->delete();
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
     * get details
     *
     * @return void
     */
    public function getDetailsById($id)
    {
        $money_safe = MoneySafe::find($id);

        return $money_safe;
    }
    /**
     * get dropdown html
     *
     * @return void
     */
    public function getDropdown()
    {
        $money_safe = MoneySafe::orderBy('name', 'asc')->pluck('name', 'id');
        $money_safe_dp = $this->commonUtil->createDropdownHtml($money_safe, 'Please Select');

        return $money_safe_dp;
    }
}
