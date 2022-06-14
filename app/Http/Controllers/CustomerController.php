<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerBalanceAdjustment;
use App\Models\CustomerSize;
use App\Models\CustomerType;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\Referred;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\System;
use App\Models\Transaction;
use App\Models\User;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @param TransactionUtil $transactionUtil
     * @param ProductUtils $productUtil
     * @return void
     */
    public function __construct(Util $commonUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Customer::leftjoin('transactions', 'customers.id', 'transactions.customer_id')
            ->select(
                'customers.*',
                DB::raw('SUM(IF(transactions.type="sell_return", final_total, 0)) as total_return'),
                DB::raw('SUM(IF(transactions.type="sell", final_total, 0)) as total_purchase'),
                DB::raw('SUM(IF(transactions.type="sell", total_sp_discount, 0)) as total_sp_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_product_discount, 0)) as total_product_discount'),
                DB::raw('SUM(IF(transactions.type="sell", total_coupon_discount, 0)) as total_coupon_discount'),
            );

        $customers = $query->groupBy('customers.id')->get();

        $balances = [];
        foreach ($customers as $customer) {
            $balances[$customer->id] = $this->transactionUtil->getCustomerBalance($customer->id)['balance'];
        }

        return view('customer.index')->with(compact(
            'customers',
            'balances'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $customer_types = CustomerType::pluck('name', 'id');

        $quick_add = request()->quick_add ?? null;
        $getAttributeListArray = CustomerSize::getAttributeListArray();
        $customers = Customer::getCustomerArrayWithMobile();

        if ($quick_add) {
            return view('customer.quick_add')->with(compact(
                'customer_types',
                'customers',
                'getAttributeListArray',
                'quick_add'
            ));
        }

        return view('customer.create')->with(compact(
            'customer_types',
            'customers',
            'getAttributeListArray',
            'quick_add'
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
            ['mobile_number' => ['required', 'max:255']],
            ['customer_type_id' => ['required', 'max:255']]
        );

        // try {
        $data = $request->except('_token', 'quick_add', 'size_data', 'reward_system', 'referred', 'referred_type', 'referred_by');
        $data['created_by'] = Auth::user()->id;

        DB::beginTransaction();
        $customer = Customer::create($data);

        if ($request->has('image')) {
            $customer->addMedia($request->image)->toMediaCollection('customer_photo');
        }

        $size_data = $request->size_data;

        if (!empty($size_data)) {
            $size_data['customer_id'] = $customer->id;
            $size_data['created_by'] = Auth::user()->id;

            $customer_size = CustomerSize::create($size_data);
        }

        $customer_id = $customer->id;

        if (!empty($request->important_dates)) {
            $this->transactionUtil->createOrUpdateCustomerImportantDate($customer_id, $request->important_dates);
        }

        $this->transactionUtil->createReferredRewardSystem($customer_id, $request);

        DB::commit();
        $output = [
            'success' => true,
            'customer_id' => $customer_id,
            'msg' => __('lang.success')
        ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('lang.something_went_wrong')
        //     ];
        // }


        if ($request->quick_add) {
            return $output;
        }

        return redirect()->to('customer')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer_id = $id;
        $customer = Customer::find($id);

        if (request()->ajax()) {
            $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
            $default_currency_id = System::getProperty('currency');
            $request = request();
            $store_id = request()->store_id;
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('stores', 'transactions.store_id', 'stores.id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('users', 'transactions.created_by', 'users.id')
                ->leftjoin('currencies as received_currency', 'transactions.received_currency_id', 'received_currency.id')
                ->where('transactions.type', 'sell')->whereIn('status', ['final', 'canceled']);

            $query->where('customer_id', $id);


            $sales = $query->select(
                'transactions.final_total',
                'transactions.payment_status',
                'transactions.status',
                'transactions.id',
                'transactions.transaction_date',
                'transactions.service_fee_value',
                'transactions.invoice_no',
                'transactions.discount_amount',
                'transactions.rp_earned',
                'transaction_payments.paid_on',
                'stores.name as store_name',
                'users.name as created_by_name',
                'customers.name as customer_name',
                'customers.mobile_number',
                'received_currency.symbol as received_currency_symbol',
                'received_currency_id'
            )->with([
                'return_parent',
                'customer',
                'transaction_payments',
                'deliveryman',
                'canceled_by_user',
                'sell_products',
                'sell_variations'
            ])
                ->groupBy('transactions.id');

            return DataTables::of($sales)
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('invoice_no', function ($row) {
                    $string = $row->invoice_no . ' ';
                    if (!empty($row->return_parent)) {
                        $string .= '<a
                        data-href="' . action('SellReturnController@show', $row->id) . '" data-container=".view_modal"
                        class="btn btn-modal" style="color: #007bff;">R</a>';
                    }
                    if ($row->payment_status == 'pending') {
                        $string .= '<a
                            data-href="' . action('SellController@show', $row->id) . '" data-container=".view_modal"
                            class="btn btn-modal" style="color: #007bff;">P</a>';
                    }

                    return $string;
                })
                ->editColumn('final_total', function ($row) use ($default_currency_id) {
                    if (!empty($row->return_parent)) {
                        $final_total = $this->commonUtil->num_f($row->final_total - $row->return_parent->final_total);
                    } else {
                        $final_total = $this->commonUtil->num_f($row->final_total);
                    }

                    $received_currency_id = $row->received_currency_id ?? $default_currency_id;
                    return '<span data-currency_id="' . $received_currency_id . '">' . $final_total . '</span>';
                })
                ->addColumn('paid', function ($row) use ($request, $default_currency_id) {
                    $amount_paid = 0;
                    if (!empty($request->method)) {
                        $payments = $row->transaction_payments->where('method', $request->method);
                    } else {
                        $payments = $row->transaction_payments;
                    }
                    foreach ($payments as $payment) {
                        $amount_paid += $payment->amount;
                    }
                    $received_currency_id = $row->received_currency_id ?? $default_currency_id;

                    return '<span data-currency_id="' . $received_currency_id . '">' . $this->commonUtil->num_f($amount_paid) . '</span>';
                })
                ->addColumn('due', function ($row) use ($default_currency_id) {
                    $paid = $row->transaction_payments->sum('amount');
                    $due = $row->final_total - $paid;
                    $received_currency_id = $row->received_currency_id ?? $default_currency_id;

                    return '<span data-currency_id="' . $received_currency_id . '">' . $this->commonUtil->num_f($due) . '</span>';
                })
                ->addColumn('customer_type', function ($row) {
                    if (!empty($row->customer->customer_type)) {
                        return $row->customer->customer_type->name;
                    } else {
                        return '';
                    }
                })
                ->editColumn('discount_amount', '{{@num_format($discount_amount)}}')
                ->editColumn('received_currency_symbol', function ($row) use ($default_currency_id) {
                    $default_currency = Currency::find($default_currency_id);
                    return $row->received_currency_symbol ?? $default_currency->symbol;
                })
                ->editColumn('paid_on', '@if(!empty($paid_on)){{@format_datetime($paid_on)}}@endif')
                ->addColumn('method', function ($row) use ($payment_types, $request) {
                    $methods = '';
                    if (!empty($request->method)) {
                        $payments = $row->transaction_payments->where('method', $request->method);
                    } else {
                        $payments = $row->transaction_payments;
                    }
                    foreach ($payments as $payment) {
                        if (!empty($payment->method)) {
                            $methods .= $payment_types[$payment->method] . '<br>';
                        }
                    }
                    return $methods;
                })
                ->addColumn('deliveryman', function ($row) {
                    if (!empty($row->deliveryman)) {
                        return $row->deliveryman->employee_name;
                    } else {
                        return '';
                    }
                })
                ->addColumn('store_name', '{{$store_name}}')
                ->addColumn('ref_number', function ($row) use ($request) {
                    $ref_numbers = '';
                    if (!empty($request->method)) {
                        $payments = $row->transaction_payments->where('method', $request->method);
                    } else {
                        $payments = $row->transaction_payments;
                    }
                    foreach ($payments as $payment) {
                        if (!empty($payment->ref_number)) {
                            $ref_numbers .= $payment->ref_number . '<br>';
                        }
                    }
                    return $ref_numbers;
                })
                ->editColumn('payment_status', function ($row) {
                    if ($row->payment_status == 'pending') {
                        return '<span class="label label-success">' . __('lang.pay_later') . '</span>';
                    } else {
                        return '<span class="label label-danger">' . ucfirst($row->payment_status) . '</span>';
                    }
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'canceled') {
                        return '<span class="badge badge-danger">' . __('lang.cancel') . '</span>';
                    } elseif ($row->status == 'final' && $row->payment_status == 'pending') {
                        return '<span class="badge badge-warning">' . __('lang.pay_later') . '</span>';
                    } else {
                        return '<span class="badge badge-success">' . ucfirst($row->status) . '</span>';
                    }
                })
                ->addColumn('products', function ($row) {
                    $string = '';
                    foreach ($row->transaction_sell_lines as $line) {
                        $string .= '(' . $this->commonUtil->num_f($line->quantity) . ')';
                        if (!empty($line->product)) {
                        }
                        $string .= $line->product->name;
                        $string .= '<br>';
                    }


                    return $string;
                })
                ->editColumn('service_fee_value', '{{@num_format($service_fee_value)}}')
                ->editColumn('created_by', '{{$created_by_name}}')
                ->editColumn('canceled_by', function ($row) {
                    return !empty($row->canceled_by_user) ? $row->canceled_by_user->name : '';
                })
                ->addColumn('files', function ($row) {
                    return ' <a data-href="' . action('GeneralController@viewUploadedFiles', ['model_name' => 'Transaction', 'model_id' => $row->id, 'collection_name' => 'sell']) . '"
                    data-container=".view_modal"
                    class="btn btn-default btn-modal">' . __('lang.view') . '</a>';
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

                        if (auth()->user()->can('sale.pos.create_and_edit')) {
                            $html .=
                                '<li>
                                <a data-href="' . action('SellController@print', $row->id) . '"
                                    class="btn print-invoice"><i class="dripicons-print"></i>
                                    ' . __('lang.generate_invoice') . '</a>
                            </li>';
                        }
                        if (auth()->user()->can('sale.pos.create_and_edit')) {
                            $html .=
                                '<li>
                                <a data-href="' . action('SellController@print', $row->id) . '?print_gift_invoice=true"
                                    class="btn print-invoice"><i class="fa fa-gift"></i>
                                    ' . __('lang.print_gift_invoice') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('sale.pos.view')) {
                            $html .=
                                '<li>
                                <a data-href="' . action('SellController@show', $row->id) . '" data-container=".view_modal"
                                    class="btn btn-modal"><i class="fa fa-eye"></i> ' . __('lang.view') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
                            $html .=
                                '<li>
                                <a href="' . action('SellController@edit', $row->id) . '" class="btn"><i
                                        class="dripicons-document-edit"></i> ' . __('lang.edit') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('return.sell_return.create_and_edit')) {
                            if (empty($row->return_parent)) {
                                $html .=
                                    '<li>
                                    <a href="' . action('SellReturnController@add', $row->id) . '" class="btn"><i
                                        class="fa fa-undo"></i> ' . __('lang.sale_return') . '</a>
                                    </li>';
                            }
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('sale.pay.create_and_edit')) {
                            if ($row->status != 'draft' && $row->payment_status != 'paid' && $row->status != 'canceled') {
                                $html .=
                                    ' <li>
                                    <a data-href="' . action('TransactionPaymentController@addPayment', $row->id) . '"
                                        data-container=".view_modal" class="btn btn-modal"><i class="fa fa-plus"></i>
                                        ' . __('lang.add_payment') . '</a>
                                    </li>';
                            }
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('sale.pay.view')) {
                            $html .=
                                '<li>
                                <a data-href="' . action('TransactionPaymentController@show', $row->id) . '"
                                    data-container=".view_modal" class="btn btn-modal"><i class="fa fa-money"></i>
                                    ' . __('lang.view_payments') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
                            $html .=
                                '<li>
                                <a data-href="' . action('SellController@destroy', $row->id) . '"
                                    data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                    ' . __('lang.delete') . '</a>
                                </li>';
                        }
                        $html .= '</div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
                    'method',
                    'invoice_no',
                    'ref_number',
                    'payment_status',
                    'transaction_date',
                    'final_total',
                    'paid',
                    'due',
                    'status',
                    'store_name',
                    'products',
                    'files',
                    'created_by',
                ])
                ->make(true);
        }

        $sale_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final']);
        // ->whereNull('parent_return_id');

        if (!empty(request()->start_date)) {
            $sale_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $sale_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($customer_id)) {
            $sale_query->where('transactions.customer_id', $customer_id);
        }
        $sales = $sale_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->get();

        $sale_return_query = Transaction::whereIn('transactions.type', ['sell_return'])
            ->whereIn('transactions.status', ['final']);
        // ->whereNull('parent_return_id');

        if (!empty(request()->start_date)) {
            $sale_return_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $sale_return_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($customer_id)) {
            $sale_return_query->where('transactions.customer_id', $customer_id);
        }
        $sale_returns = $sale_return_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->get();


        $discount_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final'])
            ->where(function ($q) {
                $q->where('total_sp_discount', '>', 0);
                $q->orWhere('total_product_discount', '>', 0);
                $q->orWhere('total_coupon_discount', '>', 0);
            });

        if (!empty(request()->start_date)) {
            $discount_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $discount_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($customer_id)) {
            $discount_query->where('transactions.customer_id', $customer_id);
        }
        $discounts = $discount_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $point_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final'])
            ->where(function ($q) {
                $q->where('rp_earned', '>', 0);
            });

        if (!empty(request()->start_date)) {
            $point_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $point_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($customer_id)) {
            $point_query->where('transactions.customer_id', $customer_id);
        }
        $points = $point_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $customer_sizes = CustomerSize::where('customer_id', $customer_id)->get();

        $customers = Customer::pluck('name', 'id');
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $balance = $this->transactionUtil->getCustomerBalance($customer->id)['balance'];
        $referred_by = $customer->referred_by_users($customer->id);

        return view('customer.show')->with(compact(
            'sales',
            'sale_returns',
            'points',
            'discounts',
            'customers',
            'customer',
            'customer_sizes',
            'balance',
            'referred_by',
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
        $customer = Customer::find($id);
        $customer_types = CustomerType::pluck('name', 'id');

        return view('customer.edit')->with(compact(
            'customer',
            'customer_types',
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
            ['mobile_number' => ['required', 'max:255']],
            ['customer_type_id' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', '_method');

            DB::beginTransaction();
            $customer = Customer::find($id);
            $customer->update($data);

            if ($request->has('image')) {
                if ($customer->getFirstMedia('customer_photo')) {
                    $customer->getFirstMedia('customer_photo')->delete();
                }
                $customer->addMedia($request->image)->toMediaCollection('customer_photo');
            }

            if (!empty($request->important_dates)) {
                $this->transactionUtil->createOrUpdateCustomerImportantDate($id, $request->important_dates);
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

        return redirect()->to('customer')->with('status', $output);
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
            $customer = Customer::find($id);
            $customer_transactions = Transaction::where('customer_id', $id)->where('type', 'sell')->where('status', 'final')->get();

            foreach ($customer_transactions as $transaction) {
                $transaction_sell_lines = $transaction->transaction_sell_lines;
                foreach ($transaction_sell_lines as $transaction_sell_line) {
                    if ($transaction->status == 'final') {
                        $product = Product::find($transaction_sell_line->product_id);
                        if (!$product->is_service) {
                            $this->productUtil->updateProductQuantityStore($transaction_sell_line->product_id, $transaction_sell_line->variation_id, $transaction->store_id, $transaction_sell_line->quantity - $transaction_sell_line->quantity_returned);
                        }
                    }
                    $transaction_sell_line->delete();
                }
                Transaction::where('return_parent_id', $transaction->id)->delete();
                Transaction::where('parent_sale_id', $transaction->id)->delete();

                $transaction->delete();
            }
            $customer->delete();


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
        $customer = Customer::get();
        $html = '';
        if (!empty($append_text)) {
            $html = '<option value="">Please Select</option>';
        }
        foreach ($customer as $value) {
            $html .= '<option value="' . $value->id . '">' . $value->name  . ' ' . $value->mobile_number . '</option>';
        }

        return $html;
    }

    public function getDetailsByTransactionType($customer_id, $type)
    {
        $query = Customer::join('transactions as t', 'customers.id', 't.customer_id')
            ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
            ->where('customers.id', $customer_id);
        if ($type == 'sell') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'customers.name',
                'customers.address',
                'customers.deposit_balance',
                'customers.id as customer_id',
                'customer_types.name as customer_type'
            );
        }

        $customer_details = $query->first();

        $balance_adjustment = CustomerBalanceAdjustment::where('customer_id', $customer_id)->sum('add_new_balance');

        $customer_details->due = $this->getCustomerBalance($customer_id)['balance'];

        return $customer_details;
    }

    /**
     * get customer balance
     *
     * @param int $customer_id
     * @return void
     */
    public function getCustomerBalance($customer_id)
    {
        return $this->transactionUtil->getCustomerBalance($customer_id);
    }

    /**
     * Shows contact's payment due modal
     *
     * @param  int  $customer_id
     * @return \Illuminate\Http\Response
     */
    public function getPayContactDue($customer_id)
    {
        if (request()->ajax()) {

            $due_payment_type = request()->input('type');
            $query = Customer::where('customers.id', $customer_id)
                ->join('transactions AS t', 'customers.id', '=', 't.customer_id');
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'customers.name',
                'customers.mobile_number',
                'customers.id as customer_id'
            );


            $customer_details = $query->first();
            $payment_type_array = $this->commonUtil->getPaymentTypeArray();

            return view('customer.partial.pay_customer_due')
                ->with(compact('customer_details', 'payment_type_array',));
        }
    }

    /**
     * Adds Payments for Contact due
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPayContactDue(Request  $request)
    {
        try {
            DB::beginTransaction();

            $this->transactionUtil->payCustomer($request);

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    public function getImportantDateRow()
    {
        $index = request()->index ?? 0;

        return view('customer.partial.important_date_row')->with(compact(
            'index'
        ));
    }

    /**
     *  update customer address
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateAddress($id)
    {
        try {
            $customer = Customer::find($id);
            $customer->address = request()->address;
            $customer->save();
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

    public function getReferralRow()
    {
        $index = request()->index ?? 0;
        $customers = Customer::getCustomerArrayWithMobile();

        return view('customer.partial.referral_row')->with(compact(
            'index',
            'customers',
        ));
    }
    public function getreferredByDetailsHtml(Request $request)
    {
        $referred_by = $request->referred_by;
        $referred_type = $request->referred_type;
        $index = request()->index ?? 0;

        $data = [];
        if ($referred_type == 'customer') {
            $data = Customer::whereIn('id', $referred_by)->pluck('name', 'id');
        } else if ($referred_type == 'supplier') {
            $data = Supplier::whereIn('id', $referred_by)->pluck('name', 'id');
        } else if ($referred_type == 'employee') {
            $data = Employee::whereIn('id', $referred_by)->select('employee_name as name', 'id')->pluck('name', 'id');
        }


        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $product_classes = ProductClass::get();
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');

        return view('customer.partial.referred_by_details')->with(compact(
            'data',
            'index',
            'payment_type_array',
            'payment_status_array',
            'users',
            'stores',
            'product_classes',
            'products',
        ));
    }
}
