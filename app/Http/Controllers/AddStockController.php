<?php

namespace App\Http\Controllers;

use App\Imports\AddStockLineImport;
use App\Models\AddStockLine;
use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Category;
use App\Models\Color;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Email;
use App\Models\Grade;
use App\Models\MoneySafe;
use App\Models\MoneySafeTransaction;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\Size;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\Supplier;
use App\Models\System;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Utils\CashRegisterUtil;
use App\Utils\MoneySafeUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AddStockController extends Controller
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
    protected $moneysafeUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, NotificationUtil $notificationUtil, CashRegisterUtil $cashRegisterUtil, MoneySafeUtil $moneysafeUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moneysafeUtil = $moneysafeUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $default_currency_id = System::getProperty('currency');
            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

            $query = Transaction::leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
                ->leftjoin('suppliers', 'transactions.supplier_id', '=', 'suppliers.id')
                ->leftjoin('users', 'transactions.created_by', '=', 'users.id')
                ->leftjoin('currencies as paying_currency', 'transactions.paying_currency_id', 'paying_currency.id')
                ->where('type', 'add_stock')->where('status', '!=', 'draft');

            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
            }

            if (!empty(request()->is_raw_material)) {
                $query->where('transactions.is_raw_material', 1);
            } else {
                $query->where('transactions.is_raw_material', 0);
            }
            if (!empty(request()->supplier_id)) {
                $query->where('transactions.supplier_id', request()->supplier_id);
            }
            if (!empty(request()->created_by)) {
                $query->where('transactions.created_by', request()->created_by);
            }
            if (!empty(request()->product_id)) {
                $query->where('add_stock_lines.product_id', request()->product_id);
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
            if (strtolower($request->session()->get('user.job_title')) == 'cashier') {
                $query->where('transactions.created_by', $request->session()->get('user.id'));
            }

            $add_stocks = $query->select(
                'transactions.*',
                'users.name as created_by_name',
                'suppliers.name as supplier',
                'paying_currency.symbol as paying_currency_symbol'
            )->with(['add_stock_variations'])->groupBy('transactions.id')->orderBy('transaction_date', 'desc')->get();
            return DataTables::of($add_stocks)
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('due_date', '@if(!empty($add_stock->due_date) && $add_stock->payment_status != "paid"){{@format_datetime($due_date)}}@endif')
                ->editColumn('created_by', '{{$created_by_name}}')
                ->editColumn('final_total', function ($row) use ($default_currency_id) {
                    $final_total =  $row->final_total;
                    $paying_currency_id = $row->paying_currency_id ?? $default_currency_id;
                    return '<span data-currency_id="' . $paying_currency_id . '">' . $this->commonUtil->num_f($final_total) . '</span>';
                })
                ->addColumn('paid_amount', function ($row) use ($default_currency_id) {
                    $amount_paid =  $row->transaction_payments->sum('amount');
                    $paying_currency_id = $row->paying_currency_id ?? $default_currency_id;
                    return '<span data-currency_id="' . $paying_currency_id . '">' . $this->commonUtil->num_f($amount_paid) . '</span>';
                })
                ->addColumn('due', function ($row) use ($default_currency_id) {
                    $due =  $row->final_total - $row->transaction_payments->sum('amount');
                    $paying_currency_id = $row->paying_currency_id ?? $default_currency_id;
                    return '<span data-currency_id="' . $paying_currency_id . '">' . $this->commonUtil->num_f($due) . '</span>';
                })
                ->editColumn('paying_currency_symbol', function ($row) use ($default_currency_id) {
                    $default_currency = Currency::find($default_currency_id);
                    return $row->paying_currency_symbol ?? $default_currency->symbol;
                })
                ->addColumn('products', function ($row) use ($default_currency_id) {
                    $string = '';
                    foreach ($row->add_stock_variations as $add_stock_variation) {
                        if (!empty($add_stock_variation)) {
                            if ($add_stock_variation->name != 'Default') {
                                $string .= $add_stock_variation->name . ' ' . $add_stock_variation->sub_sku . '<br>';
                            } else {
                                $string .= $add_stock_variation->product->name . '-' . $add_stock_variation->product->sku . '<br>';
                            }
                        }
                    }

                    return $string;
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = ' <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">' . __('lang.action') . '
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';

                        if (auth()->user()->can('stock.add_stock.view')) {
                            $html .=
                                '<li>
                                    <a href="' . action('AddStockController@show', $row->id) . '" class=""><i
                                    class="fa fa-eye btn"></i> ' . __('lang.view') . '</a>
                                 </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
                            $html .=
                                '<li>
                                <a href="' . action('AddStockController@edit', $row->id) . '"><i
                                        class="dripicons-document-edit btn"></i>' . __('lang.edit') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
                            $html .=
                                '<li>
                                <a data-href="' . action('AddStockController@destroy', $row->id) . '"
                                    data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                    class="btn text-red delete_item"><i class="dripicons-trash"></i>
                                    ' . __('lang.delete') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('stock.pay.create_and_edit')) {
                            if ($row->payment_status != 'paid') {
                                $html .=
                                    '<li>
                                    <a data-href="' . action('TransactionPaymentController@addPayment', ['id' => $row->id]) . '"
                                        data-container=".view_modal" class="btn btn-modal"><i class="fa fa-money"></i>
                                        ' . __('lang.pay') . '</a>
                                    </li>';
                            }
                        }

                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
                    'transaction_date',
                    'created_at',
                    'due_date',
                    'final_total',
                    'paid_amount',
                    'products',
                    'due',
                    'created_by',
                ])
                ->make(true);
        }
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('add_stock.index')->with(compact(
            'users',
            'products',
            'suppliers',
            'stores',
            'status_array'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        $po_nos = Transaction::where('type', 'purchase_order')->where('status', '!=', 'received')->pluck('po_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $payment_types = $payment_type_array;
        $taxes = Tax::pluck('name', 'id');

        $variation_id = request()->get('variation_id');
        $product_id = request()->get('product_id');

        $is_raw_material = request()->segment(1) == 'raw-material' ? true : false;

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
        $exchange_rate_currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);

        $stores  = Store::getDropdown();
        $users = User::pluck('name', 'id');

        return view('add_stock.create')->with(compact(
            'is_raw_material',
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'variation_id',
            'product_id',
            'po_nos',
            'taxes',
            'product_classes',
            'payment_types',
            'payment_status_array',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes_array',
            'customer_types',
            'exchange_rate_currencies',
            'discount_customer_types',
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

        // try {
        $data = $request->except('_token');

        if (!empty($data['po_no'])) {
            $ref_transaction_po = Transaction::find($data['po_no']);
        }
        $transaction_data = [
            'store_id' => $data['store_id'],
            'supplier_id' => $data['supplier_id'],
            'type' => 'add_stock',
            'status' => $data['status'],
            'paying_currency_id' => $data['paying_currency_id'],
            'default_currency_id' => $data['default_currency_id'],
            'exchange_rate' => $this->commonUtil->num_uf($data['exchange_rate']),
            'order_date' => !empty($ref_transaction_po) ? $ref_transaction_po->transaction_date : Carbon::now(),
            'transaction_date' => !empty($data['transaction_date']) ? Carbon::createFromTimestamp(strtotime($data['transaction_date']))->format('Y-m-d H:i:s') : Carbon::now(),
            'payment_status' => $data['payment_status'],
            'po_no' => !empty($ref_transaction_po) ? $ref_transaction_po->po_no : null,
            'purchase_order_id' => !empty($data['po_no']) ? $data['po_no'] : null,
            'grand_total' => $this->productUtil->num_uf($data['grand_total']),
            'final_total' => $this->productUtil->num_uf($data['final_total']),
            'discount_amount' => $this->productUtil->num_uf($data['discount_amount']),
            'other_payments' => $this->productUtil->num_uf($data['other_payments']),
            'other_expenses' => $this->productUtil->num_uf($data['other_expenses']),
            'notes' => !empty($data['notes']) ? $data['notes'] : null,
            'details' => !empty($data['details']) ? $data['details'] : null,
            'invoice_no' => !empty($data['invoice_no']) ? $data['invoice_no'] : null,
            'due_date' => !empty($data['due_date']) ? $this->commonUtil->uf_date($data['due_date']) : null,
            'notify_me' => !empty($data['notify_before_days']) ? 1 : 0,
            'notify_before_days' => !empty($data['notify_before_days']) ? $data['notify_before_days'] : 0,
            'created_by' => Auth::user()->id,
            'source_id' => !empty($data['source_id']) ? $data['source_id'] : null,
            'source_type' => !empty($data['source_type']) ? $data['source_type'] : null,
            'is_raw_material' => !empty($data['is_raw_material']) ? 1 : 0,
        ];

        DB::beginTransaction();
        $transaction = Transaction::create($transaction_data);

        $this->productUtil->createOrUpdateAddStockLines($request->add_stock_lines, $transaction);

        if ($request->files) {
            foreach ($request->file('files', []) as $key => $file) {

                $transaction->addMedia($file)->toMediaCollection('add_stock');
            }
        }
        if ($request->payment_status != 'pending') {
            $payment_data = [
                'transaction_id' => $transaction->id,
                'amount' => $this->commonUtil->num_uf($request->amount),
                'method' => $request->method,
                'paid_on' => $this->commonUtil->uf_date($data['paid_on']),
                'ref_number' => $request->ref_number,
                'source_type' => $request->source_type,
                'source_id' => $request->source_id,
                'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                'bank_name' => $request->bank_name,
            ];
            $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);

            if ($payment_data['method'] == 'cash') {
                $user_id = null;
                if (!empty($request->source_id)) {
                    if ($request->source_type == 'pos') {
                        $user_id = StorePos::where('id', $request->source_id)->first()->user_id;
                    }
                    if ($request->source_type == 'user') {
                        $user_id = $request->source_id;
                    }
                    if ($request->source_type == 'safe') {
                        $money_safe = MoneySafe::find($request->source_id);
                        $payment_data['currency_id'] = $transaction->paying_currency_id;

                        $this->moneysafeUtil->addPayment($transaction, $payment_data, 'debit', $transaction_payment->id, $money_safe);
                    }
                }
                if (!empty($user_id)) {
                    $this->cashRegisterUtil->addPayments($transaction, $payment_data, 'debit', $user_id);
                }
            }

            if ($request->upload_documents) {
                foreach ($request->file('upload_documents', []) as $key => $doc) {
                    $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                }
            }
        }

        $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);


        //update purchase order status if selected
        if (!empty($transaction->purchase_order_id)) {
            Transaction::find($transaction->purchase_order_id)->update(['status' => 'received']);
        }

        //update product status to active if not //added quick product from purchase order
        foreach ($transaction->add_stock_lines as $line) {
            Product::where('id', $line->product_id)->update(['active' => 1]);
        }
        DB::commit();

        if ($data['submit'] == 'print') {
            $print = 'print';
            $url = action('AddStockController@show', $transaction->id) . '?print=' . $print;

            return Redirect::to($url);
        }

        $output = [
            'success' => true,
            'msg' => __('lang.success')
        ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('lang.something_went_wrong')
        //     ];
        // }

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
        $add_stock = Transaction::find($id);

        $supplier = Supplier::find($add_stock->supplier_id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $taxes = Tax::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view('add_stock.show')->with(compact(
            'add_stock',
            'supplier',
            'payment_type_array',
            'users',
            'taxes'
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
        $add_stock = Transaction::find($id);
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');

        $po_nos = Transaction::where('type', 'purchase_order')->where('status', '!=', 'received')->pluck('po_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $payment_types = $payment_type_array;
        $taxes = Tax::pluck('name', 'id');

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
        $exchange_rate_currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);

        $stores  = Store::getDropdown();
        $users = User::pluck('name', 'id');

        return view('add_stock.edit')->with(compact(
            'add_stock',
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'taxes',
            'po_nos',
            'product_classes',
            'payment_types',
            'payment_status_array',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes_array',
            'customer_types',
            'exchange_rate_currencies',
            'discount_customer_types',
            'users',
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

        // try {
        $data = $request->except('_token');

        if (!empty($data['po_no'])) {
            $ref_transaction_po = Transaction::find($data['po_no']);
        }

        $transaction_data = [
            'store_id' => $data['store_id'],
            'supplier_id' => $data['supplier_id'],
            'type' => 'add_stock',
            'status' => $data['status'],
            'paying_currency_id' => $data['paying_currency_id'],
            'default_currency_id' => $data['default_currency_id'],
            'exchange_rate' => $this->commonUtil->num_uf($data['exchange_rate']),
            'order_date' => !empty($ref_transaction_po) ? $ref_transaction_po->transaction_date : Carbon::now(),
            'transaction_date' => $this->commonUtil->uf_date($data['transaction_date']) . ' ' . date('H:i:s'),
            'payment_status' => $data['payment_status'],
            'po_no' => !empty($ref_transaction_po) ? $ref_transaction_po->po_no : null,
            'grand_total' => $this->productUtil->num_uf($data['grand_total']),
            'final_total' => $this->productUtil->num_uf($data['final_total']),
            'discount_amount' => $this->productUtil->num_uf($data['discount_amount']),
            'other_payments' => $this->productUtil->num_uf($data['other_payments']),
            'other_expenses' => $this->productUtil->num_uf($data['other_expenses']),
            'notes' => !empty($data['notes']) ? $data['notes'] : null,
            'details' => !empty($data['details']) ? $data['details'] : null,
            'invoice_no' => !empty($data['invoice_no']) ? $data['invoice_no'] : null,
            'due_date' => !empty($data['due_date']) ? $this->commonUtil->uf_date($data['due_date']) : null,
            'notify_me' => !empty($data['notify_before_days']) ? 1 : 0,
            'notify_before_days' => !empty($data['notify_before_days']) ? $data['notify_before_days'] : 0,
            'created_by' => Auth::user()->id,
            'source_id' => !empty($data['source_id']) ? $data['source_id'] : null,
            'source_type' => !empty($data['source_type']) ? $data['source_type'] : null,
        ];

        if (!empty($data['po_no'])) {
            $transaction_date['purchase_order_id'] = $data['po_no'];
        }

        DB::beginTransaction();
        $transaction = Transaction::where('id', $id)->first();
        $transaction->update($transaction_data);

        $mismtach = $this->productUtil->checkSoldAndPurchaseQtyMismatch($request->add_stock_lines, $transaction);
        if ($mismtach) {
            return $this->productUtil->sendQunatityMismacthResponse($mismtach['product_name'], $mismtach['quantity']);
        }

        $this->productUtil->createOrUpdateAddStockLines($request->add_stock_lines, $transaction);

        if ($request->files) {
            foreach ($request->file('files', []) as $file) {
                $transaction->addMedia($file)->toMediaCollection('add_stock');
            }
        }

        if ($request->payment_status != 'pending') {
            $payment_data = [
                'transaction_payment_id' => !empty($request->transaction_payment_id) ? $request->transaction_payment_id : null,
                'transaction_id' => $transaction->id,
                'amount' => $this->commonUtil->num_uf($request->amount),
                'method' => $request->method,
                'paid_on' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['paid_on']) : null,
                'ref_number' => $request->ref_number,
                'source_type' => $request->source_type,
                'source_id' => $request->source_id,
                'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                'bank_name' => $request->bank_name,
            ];


            $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);

            if ($payment_data['method'] == 'cash') {
                $user_id = null;
                if (!empty($request->source_id)) {
                    if ($request->source_type == 'pos') {
                        $user_id = StorePos::where('id', $request->source_id)->first()->user_id;
                    }
                    if ($request->source_type == 'user') {
                        $user_id = $request->source_id;
                    }
                    if ($request->source_type == 'safe') {
                        $money_safe = MoneySafe::find($request->source_id);
                        $payment_data['currency_id'] = $transaction->paying_currency_id;
                        $this->moneysafeUtil->updatePayment($transaction, $payment_data, 'debit', $transaction_payment->id, null, $money_safe);
                    }
                }

                if (!empty($user_id)) {
                    $cr_transaction = CashRegisterTransaction::where('transaction_id', $transaction->id)->first();
                    $register = CashRegister::where('id', $cr_transaction->cash_register_id)->first();
                    $this->cashRegisterUtil->updateCashRegisterTransaction($cr_transaction->id, $register, $payment_data['amount'], $transaction->type, 'debit', $user_id, '');
                }
            }

            if ($request->upload_documents) {
                foreach ($request->file('upload_documents', []) as $doc) {
                    $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                }
            }
        }

        $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

        //update purchase order status if selected
        if (!empty($transaction->purchase_order_id)) {
            Transaction::find($transaction->purchase_order_id)->update(['status' => 'received']);
        }
        DB::commit();

        if ($data['submit'] == 'print') {
            $print = 'print';
            $url = action('AddStockController@show', $transaction->id) . '?print=' . $print;

            return Redirect::to($url);
        }

        $output = [
            'success' => true,
            'msg' => __('lang.success')
        ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('lang.something_went_wrong')
        //     ];
        // }

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
            $add_stock = Transaction::find($id);

            $add_stock_lines = $add_stock->add_stock_lines;

            DB::beginTransaction();

            if ($add_stock->status != 'received') {
                $add_stock_lines->delete();
            } else {
                $delete_add_stock_line_ids = [];
                foreach ($add_stock_lines as $line) {
                    $delete_add_stock_line_ids[] = $line->id;
                    $this->productUtil->decreaseProductQuantity($line->product_id, $line->variation_id, $add_stock->store_id, $line->quantity);
                }

                if (!empty($delete_add_stock_line_ids)) {
                    AddStockLine::where('transaction_id', $id)->whereIn('id', $delete_add_stock_line_ids)->delete();
                }
            }

            $add_stock->delete();
            CashRegisterTransaction::where('transaction_id', $id)->delete();
            MoneySafeTransaction::where('transaction_id', $id)->delete();

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
     * Returns the html for product row
     *
     * @return \Illuminate\Http\Response
     */
    public function addProductRow(Request $request)
    {
        if ($request->ajax()) {
            $currency_id = $request->currency_id;
            $currency = Currency::find($currency_id);
            $exchange_rate = $this->commonUtil->getExchangeRateByCurrency($currency_id, $request->store_id);

            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $store_id = $request->input('store_id');

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProduct($product_id, $variation_id, $store_id);

                return view('add_stock.partials.product_row')
                    ->with(compact('products', 'index', 'currency', 'exchange_rate'));
            }
        }
    }

    public function getPurchaseOrderDetails($id)
    {
        $purchase_order = Transaction::find($id);

        return view('add_stock.partials.purchase_order_details')->with(compact(
            'purchase_order'
        ));
    }

    /**
     * Show the form for importing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImport()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $stores = Store::getDropdown();

        $po_nos = Transaction::where('type', 'purchase_order')->where('status', '!=', 'received')->pluck('po_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $exchange_rate_currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $users = User::pluck('name', 'id');

        return view('add_stock.import')->with(compact(
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'exchange_rate_currencies',
            'users',
            'po_nos'
        ));
    }

    public function saveImport(Request $request)
    {
        try {
            $data = $request->except('_token');

            if (!empty($data['po_no'])) {
                $ref_transaction_po = Transaction::find($data['po_no']);
            }

            $transaction_data = [
                'store_id' => $data['store_id'],
                'supplier_id' => $data['supplier_id'],
                'type' => 'add_stock',
                'status' => $data['status'],
                'paying_currency_id' => $data['paying_currency_id'],
                'default_currency_id' => $data['default_currency_id'],
                'exchange_rate' => $this->commonUtil->num_uf($data['exchange_rate']),
                'transaction_date' => !empty($data['transaction_date']) ? Carbon::createFromTimestamp(strtotime($data['transaction_date']))->format('Y-m-d H:i:s') : Carbon::now(),
                'payment_status' => 'pending',
                'po_no' => !empty($ref_transaction_po) ? $ref_transaction_po->po_no : null,
                'purchase_order_id' => !empty($data['po_no']) ? $data['po_no'] : null,
                'grand_total' => $this->productUtil->num_uf($data['final_total']),
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'discount_amount' => $this->productUtil->num_uf($data['discount_amount']),
                'other_payments' => $this->productUtil->num_uf($data['other_payments']),
                'other_expenses' => $this->productUtil->num_uf($data['other_expenses']),
                'notes' => !empty($data['notes']) ? $data['notes'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'invoice_no' => !empty($data['invoice_no']) ? $data['invoice_no'] : null,
                'due_date' => !empty($data['due_date']) ? $this->commonUtil->uf_date($data['due_date']) : null,
                'notify_me' => !empty($data['notify_me']) ? $data['notify_me'] : 0,
                'created_by' => Auth::user()->id,
                'source_id' => !empty($data['source_id']) ? $data['source_id'] : null,
                'source_type' => !empty($data['source_type']) ? $data['source_type'] : null,
            ];

            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);

            Excel::import(new AddStockLineImport($transaction->id), $request->file);

            foreach ($transaction->add_stock_lines as $line) {
                $this->productUtil->updateProductQuantityStore($line->product_id, $line->variation_id, $transaction->store_id,  $line->quantity, 0);
            }

            $final_total = AddStockLine::where('transaction_id', $transaction->id)->sum('sub_total');
            $transaction->final_total = $final_total + $transaction->other_expenses - $transaction->discount_amount + $transaction->other_payments;
            $transaction->grand_total = $transaction->final_total;
            $transaction->save();

            if ($request->files) {
                foreach ($request->file('files', []) as $key => $file) {
                    $transaction->addMedia($file)->toMediaCollection('add_stock');
                }
            }

            if ($request->payment_status != 'pending') {
                $payment_data = [
                    'transaction_id' => $transaction->id,
                    'amount' => $this->commonUtil->num_uf($request->amount),
                    'method' => $request->method,
                    'paid_on' => $this->commonUtil->uf_date($data['paid_on']),
                    'ref_number' => $request->ref_number,
                    'source_type' => $request->source_type,
                    'source_id' => $request->source_id,
                    'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                    'bank_name' => $request->bank_name,
                ];
                $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);

                if ($payment_data['method'] == 'cash') {
                    $user_id = null;
                    if (!empty($request->source_id)) {
                        if ($request->source_type == 'pos') {
                            $user_id = StorePos::where('id', $request->source_id)->first()->user_id;
                        }
                        if ($request->source_type == 'user') {
                            $user_id = $request->source_id;
                        }
                        if ($request->source_type == 'safe') {
                            $money_safe = MoneySafe::find($request->source_id);
                            $payment_data['currency_id'] = $transaction->paying_currency_id;
                            $this->moneysafeUtil->addPayment($transaction, $payment_data, 'debit', $transaction_payment->id, $money_safe);
                        }
                    }

                    $this->cashRegisterUtil->addPayments($transaction, $payment_data, 'debit', $user_id);
                }

                if ($request->upload_documents) {
                    foreach ($request->file('upload_documents', []) as $key => $doc) {
                        $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                    }
                }
            }

            $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

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

    public function getSourceByTypeDropdown($type = null)
    {
        if ($type == 'user') {
            $array = User::pluck('name', 'id');
        }
        if ($type == 'pos') {
            $array = StorePos::pluck('name', 'id');
        }
        if ($type == 'store') {
            $array = Store::pluck('name', 'id');
        }
        if ($type == 'safe') {
            $array = MoneySafe::pluck('name', 'id');
        }

        return $this->commonUtil->createDropdownHtml($array, __('lang.please_select'));
    }
}
