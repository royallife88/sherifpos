<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
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
        $store_id = $this->transactionUtil->getFilterOptionValues(request())['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues(request())['pos_id'];

        $query = Transaction::where('type', 'sell_return');

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
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $query->where('store_pos_id', $pos_id);
        }

        $sale_returns = $query->orderBy('invoice_no', 'desc')->get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $customers = Customer::getCustomerArrayWithMobile();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');

        return view('sell_return.index')->with(compact(
            'sale_returns',
            'payment_types',
            'customers',
            'stores',
            'store_pos',
            'payment_status_array',
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
                $sell_return = Transaction::where('type', 'sell_return')
                    ->where('return_parent_id', $request->transaction_id)
                    ->first();


                $transaction_data = [
                    'store_id' => $request->store_id,
                    'customer_id' => $request->customer_id,
                    'store_pos_id' => $request->store_pos_id,
                    'type' => 'sell_return',
                    'return_parent_id' => $request->transaction_id,
                    'final_total' => $this->commonUtil->num_uf($request->final_total),
                    'grand_total' => $this->commonUtil->num_uf($request->grand_total),
                    'transaction_date' => Carbon::now(),
                    'invoice_no' => $this->productUtil->getNumberByType('sell_return'),
                    'payment_status' => 'pending',
                    'status' => 'final',
                    'is_return' => 1,
                    'created_by' => Auth::user()->id,
                ];

                DB::beginTransaction();

                if (empty($sell_return)) {
                    $sell_return = Transaction::create($transaction_data);
                } else {
                    $sell_return->final_total = $this->commonUtil->num_uf($request->final_total);
                    $sell_return->grand_total = $this->commonUtil->num_uf($request->grand_total);
                    $sell_return->invoice_no = $this->productUtil->getNumberByType('sell_return');
                    $sell_return->status = 'final';
                    $sell_return->save();
                }

                foreach ($request->transaction_sell_line as $sell_line) {
                    if (!empty($sell_line['transaction_sell_line_id'])) {

                        $line = TransactionSellLine::find($sell_line['transaction_sell_line_id']);
                        $old_quantity = $line->quantity_returned;
                        $line->quantity_returned = $sell_line['quantity'];
                        $line->save();
                        $this->productUtil->updateProductQuantityStore($line->product_id, $line->variation_id, $sell_return->store_id, $sell_line['quantity'], $old_quantity);
                    }
                }

                if ($request->payment_status != 'pending') {
                    $payment_data = [
                        'transaction_payment_id' => $sell_return->transaction_payment_id,
                        'transaction_id' => $sell_return->id,
                        'amount' => $this->commonUtil->num_uf($request->amount),
                        'method' => $request->method,
                        'paid_on' => $request->paid_on,
                        'ref_number' => $request->ref_number,
                        'bank_deposit_date' => !empty($request->bank_deposit_date) ? $request->bank_deposit_date : null,
                        'bank_name' => $request->bank_name,
                    ];
                    $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($sell_return, $payment_data);

                    if ($request->upload_documents) {
                        foreach ($request->file('upload_documents', []) as $key => $doc) {
                            $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                        }
                    }
                }

                $this->transactionUtil->updateTransactionPaymentStatus($sell_return->id);
                $this->cashRegisterUtil->addPayments($sell_return, $payment_data, 'debit');
                //TODO:: reduce rp points
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
