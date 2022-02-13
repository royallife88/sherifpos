<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturnLine;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Transaction;
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

class PurchaseReturnController extends Controller
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
        $query = Transaction::where('type', 'purchase_return');

        if (!empty(request()->supplier_id)) {
            $query->where('supplier_id', request()->supplier_id);
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

        $purchase_returns = $query->orderBy('invoice_no', 'desc')->get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('purchase_return.index')->with(compact(
            'purchase_returns',
            'payment_types',
            'suppliers',
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
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $payment_type_array = $this->commonUtil->getPaymentTypeArrayForPos();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('purchase_return.create')->with(compact(
            'suppliers',
            'stores',
            'payment_status_array',
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
            if (!empty($request->purchase_return_lines)) {
                $data = $request->except('_token');

                $transaction_data = [
                    'store_id' => $request->store_id,
                    'supplier_id' => $request->supplier_id,
                    'type' => 'purchase_return',
                    'final_total' => $this->commonUtil->num_uf($request->final_total),
                    'grand_total' => $this->commonUtil->num_uf($request->grand_total),
                    'transaction_date' => Carbon::now(),
                    'invoice_no' => $this->productUtil->getNumberByType('purchase_return'),
                    'payment_status' => 'pending',
                    'status' => 'final',
                    'is_return' => 1,
                    'due_date' => $request->due_date,
                    'notify_me' => !empty($request->notify_before_days) ? 1 : 0,
                    'notify_before_days' => !empty($request->notify_before_days) ? $request->notify_before_days : 0,
                    'created_by' => Auth::user()->id,
                ];

                DB::beginTransaction();

                $transaction = Transaction::create($transaction_data);

                $this->productUtil->createOrUpdatePurchaseReturnLine($request->purchase_return_lines, $transaction);

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
                        'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                        'bank_name' => $request->bank_name,
                    ];
                    $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);

                    if ($request->upload_documents) {
                        foreach ($request->file('upload_documents', []) as $key => $doc) {
                            $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                        }
                    }
                }

                $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

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

        return redirect()->to('/purchase-return')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase_return = Transaction::find($id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('purchase_return.show')->with(compact(
            'purchase_return',
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
        $purchase_return = Transaction::find($id);
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $payment_type_array = $this->commonUtil->getPaymentTypeArrayForPos();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('purchase_return.edit')->with(compact(
            'purchase_return',
            'suppliers',
            'stores',
            'payment_status_array',
            'payment_type_array',
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
            if (!empty($request->purchase_return_lines)) {
                $data = $request->except('_token');

                $transaction_data = [
                    'store_id' => $request->store_id,
                    'supplier_id' => $request->supplier_id,
                    'type' => 'purchase_return',
                    'final_total' => $this->commonUtil->num_uf($request->final_total),
                    'grand_total' => $this->commonUtil->num_uf($request->grand_total),
                    'transaction_date' => Carbon::now(),
                    'invoice_no' => $this->productUtil->getNumberByType('purchase_return'),
                    'payment_status' => 'pending',
                    'status' => 'final',
                    'is_return' => 1,
                    'due_date' => $request->due_date,
                    'notify_me' => !empty($request->notify_before_days) ? 1 : 0,
                    'notify_before_days' => !empty($request->notify_before_days) ? $request->notify_before_days : 0,
                    'created_by' => Auth::user()->id,
                ];

                DB::beginTransaction();

                $transaction = Transaction::find($id);
                $transaction->update($transaction_data);

                $this->productUtil->createOrUpdatePurchaseReturnLine($request->purchase_return_lines, $transaction);

                if ($request->files) {
                    foreach ($request->file('files', []) as $key => $file) {

                        $transaction->addMedia($file)->toMediaCollection('add_stock');
                    }
                }
                if ($request->payment_status != 'pending') {
                    $payment_data = [
                        'transaction_payment_id' => $request->transaction_payment_id,
                        'transaction_id' => $transaction->id,
                        'amount' => $this->commonUtil->num_uf($request->amount),
                        'method' => $request->method,
                        'paid_on' => $this->commonUtil->uf_date($data['paid_on']),
                        'ref_number' => $request->ref_number,
                        'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                        'bank_name' => $request->bank_name,
                    ];

                    $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);

                    if ($request->upload_documents) {
                        foreach ($request->file('upload_documents', []) as $key => $doc) {
                            $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                        }
                    }
                }

                $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

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

        return redirect()->to('/purchase-return')->with('status', $output);
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
            $deleted_lines = PurchaseReturnLine::where('transaction_id', $transaction->id)->get();
            foreach ($deleted_lines as $deleted_line) {
                $this->productUtil->updateProductQuantityStore($deleted_line->product_id, $deleted_line->variation_id, $transaction->store_id, $deleted_line->quantity, 0);
                $deleted_line->delete();
            }
            $transaction->delete();
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
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $store_id = $request->input('store_id');

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProduct($product_id, $variation_id);

                return view('purchase_return.partials.product_row')
                    ->with(compact('products', 'index', 'store_id'));
            }
        }
    }
}
