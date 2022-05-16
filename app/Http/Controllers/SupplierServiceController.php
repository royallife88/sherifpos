<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Category;
use App\Models\Color;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Grade;
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
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class SupplierServiceController extends Controller
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
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
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
                ->where('type', 'supplier_service')->where('status', '!=', 'draft');

            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
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
            )->groupBy('transactions.id')->orderBy('transaction_date', 'desc')->get();
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
                ->addColumn('files', function ($row) {
                    $transaction = Transaction::where('id', $row->parent_sale_id)->first();
                    if (!empty($transaction)) {
                        return ' <a data-href="' . action('GeneralController@viewUploadedFiles', ['model_name' => 'Transaction', 'model_id' => $transaction->id, 'collection_name' => 'sell']) . '"
                        data-container=".view_modal"
                        class="btn btn-default btn-modal">' . __('lang.view') . '</a>';
                    }
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

                        if (auth()->user()->can('service_provider.supplier_service.view')) {
                            $html .=
                                '<li>
                                    <a href="' . action('SupplierServiceController@show', $row->id) . '?print=true" class=""><i
                                    class="dripicons-print btn"></i> ' . __('lang.print') . '</a>
                                 </li>';
                        }
                        if (auth()->user()->can('service_provider.supplier_service.view')) {
                            $html .=
                                '<li>
                                    <a href="' . action('SupplierServiceController@show', $row->id) . '" class=""><i
                                    class="fa fa-eye btn"></i> ' . __('lang.view') . '</a>
                                 </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('service_provider.supplier_service.create_and_edit')) {
                            $html .=
                                '<li>
                                <a href="' . action('SupplierServiceController@edit', $row->id) . '"><i
                                        class="dripicons-document-edit btn"></i>' . __('lang.edit') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('service_provider.supplier_service.delete')) {
                            $html .=
                                '<li>
                                <a data-href="' . action('SupplierServiceController@destroy', $row->id) . '"
                                    data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                    class="btn text-red delete_item"><i class="dripicons-trash"></i>
                                    ' . __('lang.delete') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('service_provider.supplier_service.create_and_edit')) {
                            if ($row->payment_status != 'paid') {
                                $html .=
                                    '<li>
                                    <a data-href="' . action('TransactionPaymentController@addPayment', ['id' => $row->id]) . '"
                                        data-container=".view_modal" class="btn btn-modal"><i class="fa fa-money"></i>
                                        ' . __('lang.pay') . '</a>
                                    </li>';
                            }
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('service_provider.cancel_service.create_and_edit')) {
                            if ($row->status != 'canceled') {
                                $html .=
                                    '<li>
                                    <a data-href="' . action('SupplierServiceController@getUpdateStatus', ['id' => $row->id]) . '"
                                        data-container=".view_modal" class="btn btn-modal text-red"><i class="fa fa-ban"></i>
                                        ' . __('lang.cancel') . '</a>
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
                    'due',
                    'files',
                    'created_by',
                ])
                ->make(true);
        }
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('supplier_service.index')->with(compact(
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
        $supplier_service = Transaction::find($id);

        $supplier = Supplier::find($supplier_service->supplier_id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $taxes = Tax::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view('supplier_service.show')->with(compact(
            'supplier_service',
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

        return view('supplier_service.edit')->with(compact(
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
            'type' => 'supplier_service',
            'status' => $data['status'],
            'paying_currency_id' => $data['paying_currency_id'],
            'default_currency_id' => $data['default_currency_id'],
            'exchange_rate' => $this->commonUtil->num_uf($data['exchange_rate']),
            'order_date' => !empty($ref_transaction_po) ? $ref_transaction_po->transaction_date : Carbon::now(),
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
            'source_id' => !empty($data['source_id']) ? $data['source_id'] : null,
            'source_type' => !empty($data['source_type']) ? $data['source_type'] : null,
        ];

        DB::beginTransaction();
        $transaction = Transaction::where('id', $id)->first();
        $transaction->update($transaction_data);

        $this->productUtil->createOrUpdateAddStockLines($request->add_stock_lines, $transaction);

        if ($request->files) {
            foreach ($request->file('files', []) as $file) {
                $transaction->addMedia($file)->toMediaCollection('supplier_service');
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

            if ($request->upload_documents) {
                foreach ($request->file('upload_documents', []) as $doc) {
                    $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                }
            }
        }

        $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

        DB::commit();

        if ($data['submit'] == 'print') {
            $print = 'print';
            $url = action('SupplierServiceController@show', $transaction->id) . '?print=' . $print;

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
        //
    }

    /**
     * change the status of transaction
     *
     * @param int $id
     * @return void
     */
    public function getUpdateStatus($id)
    {
        $transaction = Transaction::find($id);

        return view('supplier_service.partial.update_status')->with(compact(
            'transaction'
        ));
    }

    /**
     * update the transaction status and send notification based on status
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function postUpdateStatus($id)
    {
        try {
            $transaction = Transaction::find($id);

            DB::beginTransaction();
            $transaction->status = 'canceled';
            $transaction->save();

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
}
