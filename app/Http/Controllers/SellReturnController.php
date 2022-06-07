<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\DiningRoom;
use App\Models\DiningTable;
use App\Models\Employee;
use App\Models\GiftCard;
use App\Models\MoneySafe;
use App\Models\MoneySafeTransaction;
use App\Models\Product;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\System;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
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
use Yajra\DataTables\Facades\DataTables;

class SellReturnController extends Controller
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
    protected $moneySafeUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, NotificationUtil $notificationUtil, CashRegisterUtil $cashRegisterUtil, MoneySafeUtil $moneySafeUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moneySafeUtil = $moneySafeUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues(request())['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues(request())['pos_id'];

        if ($request->ajax()) {
            $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
            $default_currency_id = System::getProperty('currency');

            $query = Transaction::leftjoin('transactions as sell_parent', 'transactions.return_parent_id', 'sell_parent.id')
                ->leftjoin('customers', 'sell_parent.customer_id', 'customers.id')
                ->leftjoin('currencies as received_currency', 'transactions.received_currency_id', 'received_currency.id')
                ->where('transactions.type', 'sell_return');

            if (!empty(request()->customer_id)) {
                $query->where('transactions.customer_id', request()->customer_id);
            }
            if (!empty(request()->status)) {
                $query->where('transactions.status', request()->status);
            }
            if (!empty(request()->payment_status)) {
                $query->where('transactions.payment_status', request()->payment_status);
            }
            if (!empty(request()->start_date)) {
                $query->where('transactions.transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->start_time)) {
                $query->where('transactions.transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
            }
            if (!empty(request()->end_time)) {
                $query->where('transactions.transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
            }
            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
            }
            if (!empty($pos_id)) {
                $query->where('transactions.store_pos_id', $pos_id);
            }
            if (!empty(request()->dining_room_id)) {
                $query->where('sell_parent.dining_room_id', request()->dining_room_id);
            }
            if (!empty(request()->dining_table_id)) {
                $query->where('sell_parent.dining_table_id', request()->dining_table_id);
            }

            $sale_returns = $query->select(
                'transactions.*',
                'received_currency.symbol as received_currency_symbol',
                'customers.name as customer_name'
            )->groupBy('transactions.id')
                ->orderBy('transactions.transaction_date', 'desc');



            return DataTables::of($sale_returns)
                // ->setTotalRecords(100)
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('invoice_no', function ($row) {
                    $string = $row->invoice_no;
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
                    $commissions = Transaction::where('parent_sale_id', $row->id)->get();
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
                ->editColumn('service_fee_value', '{{@num_format($service_fee_value)}}')
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
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('return.sell_return.view')) {
                            $html .=
                                '<li>
                                    <a data-href="' . action('SellReturnController@show', $row->return_parent_id) . '" data-container=".view_modal"
                                        class="btn btn-modal"><i class="fa fa-eye"></i> ' . __('lang.view') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('return.sell_return.create_and_edit')) {
                            $html .=
                                '<li>
                                    <a href="' . action('SellReturnController@add', $row->return_parent_id) . '" class="btn"><i
                                            class="dripicons-document-edit"></i> ' . __('lang.edit') . '</a>
                                </li>';
                        }

                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('return.sell_return_pay.create_and_edit')) {
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
                        if (auth()->user()->can('return.sell_return_pay.view')) {
                            $html .=
                                '<li>
                                <a data-href="' . action('TransactionPaymentController@show', $row->id) . '"
                                    data-container=".view_modal" class="btn btn-modal"><i class="fa fa-money"></i>
                                    ' . __('lang.view_payments') . '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('return.sell_return_pay.delete')) {
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
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $customers = Customer::getCustomerArrayWithMobile();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $stores = Store::getDropdown();
        $store_pos = StorePos::orderBy('name', 'asc')->pluck('name', 'id');
        $dining_rooms = DiningRoom::pluck('name', 'id');
        $dining_tables = DiningTable::pluck('name', 'id');

        return view('sell_return.index')->with(compact(
            'payment_types',
            'customers',
            'stores',
            'store_pos',
            'payment_status_array',
            'dining_rooms',
            'dining_tables',
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
     * Show the form for creating a new resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        $sale  = Transaction::find($id);

        $categories = Category::whereNull('parent_id')->get();
        $sub_categories = Category::whereNotNull('parent_id')->get();
        $brands = Brand::all();
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_type_array = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');

        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();

        $sell_return = Transaction::where('type', 'sell_return')
            ->where('return_parent_id', $id)
            ->first();

        $stores = Store::getDropdown();

        return view('sell_return.create')->with(compact(
            'sell_return',
            'sale',
            'categories',
            'walk_in_customer',
            'deliverymen',
            'sub_categories',
            'brands',
            'store_pos',
            'customers',
            'taxes',
            'stores',
            'payment_type_array',
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
        try {
            if (!empty($request->transaction_sell_line)) {
                $sell_transaction = Transaction::find($request->transaction_id);
                $sell_return = Transaction::where('type', 'sell_return')
                    ->where('return_parent_id', $request->transaction_id)
                    ->first();


                $transaction_data = [
                    'store_id' => $request->store_id,
                    'customer_id' => $request->customer_id,
                    'store_pos_id' => $request->store_pos_id,
                    'type' => 'sell_return',
                    'return_parent_id' => $request->transaction_id,
                    'received_currency_id' => $sell_transaction->received_currency_id,
                    'final_total' => $this->commonUtil->num_uf($request->final_total),
                    'grand_total' => $this->commonUtil->num_uf($request->grand_total),
                    'discount_type' => $request->discount_type,
                    'discount_value' => $this->commonUtil->num_uf($request->discount_value),
                    'total_tax' => $this->commonUtil->num_uf($request->total_tax),
                    'gift_card_id' => $request->gift_card_id,
                    'gift_card_amount' => $request->gift_card_amount,
                    'discount_amount' => $this->commonUtil->num_uf($request->discount_amount),
                    'transaction_date' => Carbon::now(),
                    'invoice_no' => $this->transactionUtil->createReturnTransactionInvoiceNoFromInvoice($sell_transaction->invoice_no),
                    'payment_status' => 'pending',
                    'status' => 'final',
                    'notes' => $request->notes,
                    'is_return' => 1,
                    'created_by' => Auth::user()->id,
                ];

                DB::beginTransaction();

                if (empty($sell_return)) {
                    $sell_return = Transaction::create($transaction_data);
                } else {
                    $sell_return->final_total = $this->commonUtil->num_uf($request->final_total);
                    $sell_return->grand_total = $this->commonUtil->num_uf($request->grand_total);
                    $sell_return->status = 'final';
                    $sell_return->notes = $request->notes;
                    $sell_return->save();
                }

                if (!empty($request->gift_card_id)) {
                    GiftCard::where('id', $request->gift_card_id)->increment('balance', $request->gift_card_amount);
                }

                foreach ($request->transaction_sell_line as $sell_line) {
                    if (!empty($sell_line['transaction_sell_line_id'])) {

                        $line = TransactionSellLine::find($sell_line['transaction_sell_line_id']);
                        $old_quantity = $line->quantity_returned;
                        $line->quantity_returned = $sell_line['quantity'];
                        $line->save();
                        $product = Product::find($line->product_id);
                        if (!$product->is_service) {
                            $this->productUtil->updateProductQuantityStore($line->product_id, $line->variation_id, $sell_return->store_id, $sell_line['quantity'], $old_quantity);
                        }
                    }
                }
                //deduct employee commission on returned products
                $this->transactionUtil->deductCommissionForEmployee($sell_transaction);

                if ($request->files) {
                    foreach ($request->file('files', []) as $key => $doc) {
                        $sell_return->addMedia($doc)->toMediaCollection('sell_return');
                    }
                }
                if ($request->payment_status != 'pending') {
                    $payment_data = [
                        'transaction_payment_id' => $request->transaction_payment_id,
                        'transaction_id' => $sell_return->id,
                        'amount' => $this->commonUtil->num_uf($request->amount),
                        'method' => $request->method,
                        'paid_on' => $this->commonUtil->uf_date($request->paid_on) . ' ' . date('H:i:s'),
                        'ref_number' => $request->ref_number,
                        'bank_deposit_date' => !empty($request->bank_deposit_date) ? $request->bank_deposit_date : null,
                        'bank_name' => $request->bank_name,
                        // 'is_return' => 1,
                    ];
                    $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($sell_return, $payment_data);

                    if ($request->upload_documents) {
                        foreach ($request->file('upload_documents', []) as $key => $doc) {
                            $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                        }
                    }

                    if ($payment_data['method'] == 'card' || $payment_data['method'] == 'bank_trasfer') {
                        $money_safe_transaction = MoneySafeTransaction::where('transaction_id', $sell_transaction->id)->first();
                        $money_safe = MoneySafe::find($money_safe_transaction->money_safe_id);
                        if (empty($money_safe)) {
                            $money_safe = MoneySafe::where('store_id', $sell_transaction->store_id)->where('type', 'bank')->first();
                            if (empty($money_safe)) {
                                $money_safe = MoneySafe::where('is_default', 1)->first();
                            }
                        }

                        $money_safe_data['money_safe_id'] = $money_safe->id;
                        $money_safe_data['transaction_date'] = $sell_return->transaction_date;
                        $money_safe_data['transaction_id'] = $sell_return->id;
                        $money_safe_data['transaction_payment_id'] = $transaction_payment->id;
                        $money_safe_data['currency_id'] = $sell_transaction->received_currency_id;
                        $money_safe_data['type'] = 'debit';
                        $money_safe_data['store_id'] = $sell_transaction->store_id ?? 0;
                        $money_safe_data['amount'] = $sell_return->final_total;
                        $money_safe_data['created_by'] = Auth::user()->id;
                        $money_safe_data['comments'] = __('lang.sell_return');

                        MoneySafeTransaction::updateOrCreate(['transaction_id' => $sell_return->id], $money_safe_data);
                    }
                }

                $this->transactionUtil->updateTransactionPaymentStatus($sell_return->id);
                $this->cashRegisterUtil->addPayments($sell_return, $payment_data, 'debit');

                //reduce rp points
                $this->transactionUtil->updateCustomerRewardPoints($sell_transaction->customer_id, 0, $sell_transaction->rp_earned, 0, $sell_transaction->rp_redeemed);
                DB::commit();
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

        return redirect()->to('/sale-return')->with('status', $output);
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

        return view('sell_return.show')->with(compact(
            'sale',
            'payment_type_array'
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
