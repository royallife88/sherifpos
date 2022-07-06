<?php

namespace App\Http\Controllers;

use App\Imports\TransactionSellLineImport;
use App\Models\Brand;
use App\Models\CashRegisterTransaction;
use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerSize;
use App\Models\CustomerType;
use App\Models\DeliveryZone;
use App\Models\DiningRoom;
use App\Models\DiningTable;
use App\Models\Employee;
use App\Models\ExchangeRate;
use App\Models\Grade;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ServiceFee;
use App\Models\Size;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\System;
use App\Models\Tax;
use App\Models\TermsAndCondition;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
use App\Models\Unit;
use App\Models\User;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Tag\Em;
use Yajra\DataTables\Facades\DataTables;

class SellController extends Controller
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
    public function index(Request $request)
    {
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $default_currency_id = System::getProperty('currency');

        if (request()->ajax()) {
            // $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $store_id = request()->store_id;
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('stores', 'transactions.store_id', 'stores.id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('variations', 'transaction_sell_lines.variation_id', 'variations.id')
                ->leftjoin('users', 'transactions.created_by', 'users.id')
                ->leftjoin('currencies as received_currency', 'transactions.received_currency_id', 'received_currency.id')
                ->where('transactions.type', 'sell')->whereIn('status', ['final', 'canceled']);

            if (!empty(request()->product_class_id) &&  !empty(array_filter(request()->product_class_id))) {
                $query->whereIn('products.product_class_id', array_filter(request()->product_class_id));
            }

            if (!empty(request()->category_id) && !empty(array_filter(request()->category_id))) {
                $query->whereIn('products.category_id', array_filter(request()->category_id));
            }

            if (!empty(request()->sub_category_id) && !empty(array_filter(request()->sub_category_id))) {
                $query->whereIn('products.sub_category_id', array_filter(request()->sub_category_id));
            }

            if (!empty(request()->brand_id) && !empty(array_filter(request()->brand_id))) {
                $query->whereIn('products.brand_id', array_filter(request()->brand_id));
            }
            if (!empty(request()->tax_id) && !empty(array_filter(request()->tax_id))) {
                $query->whereIn('transactions.tax_id', array_filter(request()->tax_id));
            }
            if (!empty(request()->customer_id)) {
                $query->where('customer_id', request()->customer_id);
            }
            if (!empty(request()->customer_type_id)) {
                if (request()->customer_type_id == 'dining_in') {
                    $query->whereNotNull('dining_table_id');
                } else {
                    $query->where('customer_type_id', request()->customer_type_id);
                }
            }
            if (!empty(request()->dining_room_id)) {
                $query->where('dining_room_id', request()->dining_room_id);
            }
            if (!empty(request()->dining_table_id)) {
                $query->where('dining_table_id', request()->dining_table_id);
            }
            if (!empty(request()->status)) {
                $query->where('status', request()->status);
            }
            if (!empty($store_id)) {
                $query->where('store_id', $store_id);
            }
            if (!empty(request()->deliveryman_id)) {
                $query->where('deliveryman_id', request()->deliveryman_id);
            }
            if (!empty(request()->payment_status)) {
                $query->where('payment_status', request()->payment_status);
            }
            if (!empty(request()->created_by)) {
                $query->where('transactions.created_by', request()->created_by);
            }
            if (!empty(request()->method)) {
                $query->where('transaction_payments.method', request()->method);
            }
            if (!empty(request()->start_date)) {
                $query->whereDate('transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->whereDate('transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->start_time)) {
                $query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
            }
            if (!empty(request()->end_time)) {
                $query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
            }
            if (!empty(request()->payment_start_date)) {
                $query->whereDate('paid_on', '>=', request()->payment_start_date);
            }
            if (!empty(request()->payment_end_date)) {
                $query->whereDate('paid_on', '<=', request()->payment_end_date);
            }
            if (!empty(request()->payment_start_time)) {
                $query->where('paid_on', '>=', request()->payment_start_date . ' ' . Carbon::parse(request()->payment_start_time)->format('H:i:s'));
            }
            if (!empty(request()->payment_end_time)) {
                $query->where('paid_on', '<=', request()->payment_end_date . ' ' . Carbon::parse(request()->payment_end_time)->format('H:i:s'));
            }
            if (strtolower($request->session()->get('user.job_title')) == 'cashier') {
                $query->where('transactions.created_by', $request->session()->get('user.id'));
            }

            $sales = $query->select(
                'transactions.final_total',
                'transactions.payment_status',
                'transactions.status',
                'transactions.id',
                'transactions.transaction_date',
                'transactions.service_fee_value',
                'transactions.invoice_no',
                'transactions.deliveryman_id',
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
                // ->setTotalRecords(100)
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
                ->addColumn('commissions', function ($row) {
                    $commissions = Transaction::where('parent_sale_id', $row->id)->where('type', 'employee_commission')->get();
                    $total = 0;
                    foreach ($commissions as $commission) {
                        $total +=  $commission->final_total;
                    }
                    return $this->commonUtil->num_f($total);
                })
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
                    } else {
                        return '<span class="badge badge-success">' . ucfirst($row->status) . '</span>';
                    }
                })
                ->addColumn('products', function ($row) {
                    $string = '';
                    foreach ($row->sell_variations as $sell_variation) {
                        if (!empty($sell_variation)) {
                            if ($sell_variation->name != 'Default') {
                                $string .= $sell_variation->name . ' ' . $sell_variation->sub_sku . '<br>';
                            } else {
                                $string .= $sell_variation->product->name . '-' . $sell_variation->product->sku . '<br>';
                            }
                        }
                    }

                    return $string;
                })
                ->addColumn('sku', function ($row) {
                    $string = '';
                    foreach ($row->sell_variations as $sell_variation) {
                        if (!empty($sell_variation)) {
                            if ($sell_variation->name == 'Default') {
                                $string .= $sell_variation->product->sku . '<br>';
                            }
                        }
                    }

                    return $string;
                })
                ->addColumn('sub_sku', function ($row) {
                    $string = '';
                    foreach ($row->sell_variations as $sell_variation) {
                        if (!empty($sell_variation)) {
                            if ($sell_variation->name != 'Default') {
                                $string .= $sell_variation->sub_sku . '<br>';
                            }
                        }
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
                    'sku',
                    'sub_sku',
                    'files',
                    'created_by',
                ])
                ->make(true);
        }

        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $customers = Customer::getCustomerArrayWithMobile();
        $customer_types = CustomerType::pluck('name', 'id');
        $stores = Store::getDropdown();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $cashiers = Employee::getDropdownByJobType('Cashier', true, true);
        $delivery_men = Employee::getDropdownByJobType('Deliveryman');
        $taxes = Tax::pluck('name', 'id');
        $dining_rooms = DiningRoom::pluck('name', 'id');
        $dining_tables = DiningTable::pluck('name', 'id');

        return view('sale.index')->with(compact(
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'payment_types',
            'cashiers',
            'customers',
            'customer_types',
            'stores',
            'delivery_men',
            'payment_status_array',
            'taxes',
            'dining_rooms',
            'dining_tables'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');
        $store_poses = [];
        $weighing_scale_setting = System::getProperty('weighing_scale_setting') ?  json_decode(System::getProperty('weighing_scale_setting'), true) : [];

        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $taxes_array = Tax::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $discount_customer_types = Customer::getCustomerTreeArray();
        $service_fees = ServiceFee::pluck('name', 'id');
        $delivery_zones = DeliveryZone::pluck('name', 'id');

        $stores  = Store::getDropdown();
        $users = User::pluck('name', 'id');

        return view('sale.create')->with(compact(
            'walk_in_customer',
            'deliverymen',
            'store_pos',
            'customers',
            'tac',
            'taxes',
            'payment_types',
            'stores',
            'store_poses',
            'payment_status_array',
            'weighing_scale_setting',
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes_array',
            'customer_types',
            'discount_customer_types',
            'service_fees',
            'delivery_zones',
            'users',
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
        $sale = Transaction::find($id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArrayForPos();
        $getAttributeListArray = CustomerSize::getAttributeListArray();

        return view('sale.show')->with(compact(
            'sale',
            'payment_type_array',
            'getAttributeListArray'
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
        $sale = Transaction::findOrFail($id);
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::getDropdown();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');
        $weighing_scale_setting = System::getProperty('weighing_scale_setting') ?  json_decode(System::getProperty('weighing_scale_setting'), true) : [];
        $service_fees = ServiceFee::pluck('name', 'id');
        $delivery_zones = DeliveryZone::pluck('name', 'id');
        $exchange_rate_currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $employees = Employee::getCommissionEmployeeDropdown();

        return view('sale.edit')->with(compact(
            'sale',
            'walk_in_customer',
            'deliverymen',
            'store_pos',
            'tac',
            'customers',
            'taxes',
            'payment_types',
            'payment_status_array',
            'weighing_scale_setting',
            'service_fees',
            'delivery_zones',
            'exchange_rate_currencies',
            'employees'
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
        try {
            $this->transactionUtil->updateSellTransaction($request, $id);

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
            $transaction = Transaction::find($id);

            DB::beginTransaction();

            $transaction_sell_lines = TransactionSellLine::where('transaction_id', $id)->get();
            foreach ($transaction_sell_lines as $transaction_sell_line) {
                if ($transaction->status == 'final') {
                    $product = Product::find($transaction_sell_line->product_id);
                    if (!$product->is_service) {
                        $this->productUtil->updateProductQuantityStore($transaction_sell_line->product_id, $transaction_sell_line->variation_id, $transaction->store_id, $transaction_sell_line->quantity - $transaction_sell_line->quantity_returned);
                    }
                }
                $transaction_sell_line->delete();
            }
            Transaction::where('return_parent_id', $id)->delete();
            Transaction::where('parent_sale_id', $id)->delete();

            $this->transactionUtil->updateCustomerRewardPoints($transaction->customer_id, 0, $transaction->rp_earned, 0, $transaction->rp_redeemed);

            $transaction->delete();
            CashRegisterTransaction::where('transaction_id', $id)->delete();

            DiningTable::where('current_transaction_id', $id)->update(['current_transaction_id' => null, 'status' => 'available']);

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

        return $output;
    }

    /**
     * print the transaction
     *
     * @param int $id
     * @return html
     */
    public function print($id)
    {
        try {
            $transaction = Transaction::find($id);

            $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

            $invoice_lang = System::getProperty('invoice_lang');
            if (empty($invoice_lang)) {
                $invoice_lang = request()->session()->get('language');
            }

            if (empty($transaction->received_currency_id)) {
            }

            $html_content = $this->transactionUtil->getInvoicePrint($transaction, $payment_types);

            $output = [
                'success' => true,
                'html_content' => $html_content,
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
     * Display a listing of the resource for delivery.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDeliveryList()
    {
        $query = Transaction::where('type', 'sell')->whereNotNull('deliveryman_id');

        if (!empty(request()->customer_id)) {
            $query->where('customer_id', request()->customer_id);
        }
        if (!empty(request()->status)) {
            $query->where('status', request()->status);
        }
        if (!empty(request()->payment_status)) {
            $query->where('payment_status', request()->payment_status);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('transaction_date', '<=', request()->end_date);
        }

        $sales = $query->orderBy('invoice_no', 'desc')->get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $customers = Customer::getCustomerArrayWithMobile();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('sale.delivery_list')->with(compact(
            'sales',
            'payment_types',
            'customers',
            'payment_status_array',
        ));
    }

    public function getImport()
    {
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');

        return view('sale.import')->with(compact(
            'walk_in_customer',
            'deliverymen',
            'store_pos',
            'customers',
            'tac',
            'taxes',
            'payment_types',
            'payment_status_array'
        ));
    }

    public function saveImport(Request $request)
    {
        try {
            $transaction_data = [
                'store_id' => $request->store_id,
                'customer_id' => $request->customer_id,
                'store_pos_id' => $request->store_pos_id,
                'type' => 'sell',
                'final_total' => $this->commonUtil->num_uf($request->final_total),
                'grand_total' => $this->commonUtil->num_uf($request->grand_total),
                'gift_card_id' => $request->gift_card_id,
                'coupon_id' => $request->coupon_id,
                'transaction_date' => Carbon::now(),
                'invoice_no' => $this->productUtil->getNumberByType('sell'),
                'is_direct_sale' => !empty($request->is_direct_sale) ? 1 : 0,
                'status' => $request->status,
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'discount_type' => $request->discount_type,
                'discount_value' => $this->commonUtil->num_uf($request->discount_value),
                'discount_amount' => $this->commonUtil->num_uf($request->discount_amount),
                'tax_id' => !empty($request->tax_id_hidden) ? $request->tax_id_hidden : null,
                'total_tax' => $this->commonUtil->num_uf($request->total_tax),
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'terms_and_condition_id' => !empty($request->terms_and_condition_id) ? $request->terms_and_condition_id : null,
                'deliveryman_id' => !empty($request->deliveryman_id_hidden) ? $request->deliveryman_id_hidden : null,
                'payment_status' => 'pending',
                'delivery_status' => 'pending',
                'delivery_cost' => $this->commonUtil->num_uf($request->delivery_cost),
                'delivery_address' => $request->delivery_address,
                'delivery_cost_paid_by_customer' => !empty($request->delivery_cost_paid_by_customer) ? 1 : 0,
                'created_by' => Auth::user()->id,
            ];

            DB::beginTransaction();

            if (!empty($request->is_quotation)) {
                $transaction_data['is_quotation'] = 1;
                $transaction_data['status'] = 'draft';
                $transaction_data['invoice_no'] = $this->productUtil->getNumberByType('quotation');
                $transaction_data['block_qty'] = !empty($request->block_qty) ? 1 : 0;
                $transaction_data['block_for_days'] = !empty($request->block_for_days) ? $request->block_for_days : 0;
                $transaction_data['validity_days'] = !empty($request->validity_days) ? $request->validity_days : 0;
            }
            $transaction = Transaction::create($transaction_data);

            Excel::import(new TransactionSellLineImport($transaction->id), $request->file);

            foreach ($transaction->transaction_sell_lines as $sell_line) {
                if (empty($sell_line['transaction_sell_line_id'])) {
                    if ($transaction->status == 'final') {
                        $this->productUtil->decreaseProductQuantity($sell_line['product_id'], $sell_line['variation_id'], $transaction->store_id, $sell_line['quantity']);
                    }
                }
            }

            $final_total = TransactionSellLine::where('transaction_id', $transaction->id)->sum('sub_total');
            $transaction->discount_amount = $this->transactionUtil->calculateDiscountAmount($final_total, $request->discount_type, $request->discount_value);
            $transaction->total_tax = $this->transactionUtil->calculateTaxAmount($final_total, $request->tax_id);
            $transaction->grand_total = $final_total;
            $transaction->final_total = $final_total - $transaction->discount_amount + $transaction->total_tax;
            $transaction->save();


            if ($transaction->status == 'final') {
                //if transaction is final then calculate the reward points
                $points_earned =  $this->transactionUtil->calculateRewardPoints($transaction);
                $transaction->rp_earned = $points_earned;
                if ($request->is_redeem_points) {
                    $transaction->rp_redeemed_value = $request->rp_redeemed_value;
                    $rp_redeemed = $this->transactionUtil->calcuateRedeemPoints($transaction); //back end
                    $transaction->rp_redeemed = $rp_redeemed;
                }

                $transaction->save();

                $this->transactionUtil->updateCustomerRewardPoints($transaction->customer_id, $points_earned, 0, $request->rp_redeemed, 0);
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
     * get total sales details
     *
     * @return array
     */
    public function getTotalDetails(Request $request)
    {
        if (request()->ajax()) {
            // $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $store_id = request()->store_id;
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('stores', 'transactions.store_id', 'stores.id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('variations', 'transaction_sell_lines.variation_id', 'variations.id')
                ->leftjoin('users', 'transactions.created_by', 'users.id')
                ->leftjoin('currencies as received_currency', 'transactions.received_currency_id', 'received_currency.id')
                ->where('transactions.type', 'sell')->whereIn('status', ['final', 'canceled']);

            if (!empty(request()->product_class_id) &&  !empty(array_filter(request()->product_class_id))) {
                $query->whereIn('products.product_class_id', array_filter(request()->product_class_id));
            }

            if (!empty(request()->category_id) && !empty(array_filter(request()->category_id))) {
                $query->whereIn('products.category_id', array_filter(request()->category_id));
            }

            if (!empty(request()->sub_category_id) && !empty(array_filter(request()->sub_category_id))) {
                $query->whereIn('products.sub_category_id', array_filter(request()->sub_category_id));
            }

            if (!empty(request()->brand_id) && !empty(array_filter(request()->brand_id))) {
                $query->whereIn('products.brand_id', array_filter(request()->brand_id));
            }
            if (!empty(request()->tax_id) && !empty(array_filter(request()->tax_id))) {
                $query->whereIn('transactions.tax_id', array_filter(request()->tax_id));
            }
            if (!empty(request()->customer_id)) {
                $query->where('customer_id', request()->customer_id);
            }
            if (!empty(request()->customer_type_id)) {
                if (request()->customer_type_id == 'dining_in') {
                    $query->whereNotNull('dining_table_id');
                } else {
                    $query->where('customer_type_id', request()->customer_type_id);
                }
            }
            if (!empty(request()->dining_room_id)) {
                $query->where('dining_room_id', request()->dining_room_id);
            }
            if (!empty(request()->dining_table_id)) {
                $query->where('dining_table_id', request()->dining_table_id);
            }
            if (!empty(request()->status)) {
                $query->where('status', request()->status);
            }
            if (!empty($store_id)) {
                $query->where('store_id', $store_id);
            }
            if (!empty(request()->deliveryman_id)) {
                $query->where('deliveryman_id', request()->deliveryman_id);
            }
            if (!empty(request()->payment_status)) {
                $query->where('payment_status', request()->payment_status);
            }
            if (!empty(request()->created_by)) {
                $query->where('transactions.created_by', request()->created_by);
            }
            if (!empty(request()->method)) {
                $query->where('transaction_payments.method', request()->method);
            }
            if (!empty(request()->start_date)) {
                $query->whereDate('transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->whereDate('transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->start_time)) {
                $query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
            }
            if (!empty(request()->end_time)) {
                $query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
            }
            if (!empty(request()->payment_start_date)) {
                $query->whereDate('paid_on', '>=', request()->payment_start_date);
            }
            if (!empty(request()->payment_end_date)) {
                $query->whereDate('paid_on', '<=', request()->payment_end_date);
            }
            if (!empty(request()->payment_start_time)) {
                $query->where('paid_on', '>=', request()->payment_start_date . ' ' . Carbon::parse(request()->payment_start_time)->format('H:i:s'));
            }
            if (!empty(request()->payment_end_time)) {
                $query->where('paid_on', '<=', request()->payment_end_date . ' ' . Carbon::parse(request()->payment_end_time)->format('H:i:s'));
            }
            if (strtolower($request->session()->get('user.job_title')) == 'cashier') {
                $query->where('transactions.created_by', $request->session()->get('user.id'));
            }

            $query->select(
                DB::raw('COUNT(DISTINCT(transactions.customer_id)) as customer_count'),
                DB::raw('COUNT(DISTINCT(transactions.id)) as sales_count'),
            );
            $sales = $query->first();

            $sales_count = $sales->sales_count ?? 0;
            $customer_count = $sales->customer_count ?? 0;

            return ['customer_count' => $customer_count, 'sales_count' => $sales_count];
        }
    }
}
