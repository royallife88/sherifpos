<?php

namespace App\Http\Controllers;

use App\Imports\AddStockLineImport;
use App\Models\AddStockLine;
use App\Models\Email;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
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

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, NotificationUtil $notificationUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $query = Transaction::leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
        ->where('type', 'add_stock')->where('status', '!=', 'draft');

        if (!empty($store_id)) {
            $query->where('transactions.store_id', $store_id);
        }

        if (!empty(request()->supplier_id)) {
            $query->where('supplier_id', request()->supplier_id);
        }
        if (!empty(request()->created_by)) {
            $query->where('created_by', request()->created_by);
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

        $add_stocks = $query->select(
            'transactions.*',
        )->groupBy('transactions.id')->orderBy('transaction_date', 'desc')->get();

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('add_stock.index')->with(compact(
            'add_stocks',
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
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();

        $po_nos = Transaction::where('type', 'purchase_order')->where('status', '!=', 'received')->pluck('po_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('add_stock.create')->with(compact(
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'po_nos'
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
            $data = $request->except('_token');

            if (!empty($data['po_no'])) {
                $ref_transaction_po = Transaction::find($data['po_no']);
            }

            $transaction_data = [
                'store_id' => $data['store_id'],
                'supplier_id' => $data['supplier_id'],
                'type' => 'add_stock',
                'status' => $data['status'],
                'order_date' => !empty($ref_transaction_po) ? $ref_transaction_po->transaction_date : Carbon::now(),
                'transaction_date' => $this->commonUtil->uf_date($data['transaction_date']),
                'payment_status' => $data['payment_status'],
                'po_no' => !empty($ref_transaction_po) ? $ref_transaction_po->po_no : null,
                'purchase_order_id' => !empty($data['po_no']) ? $data['po_no'] : null,
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'notes' => !empty($data['notes']) ? $data['notes'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'invoice_no' => !empty($data['invoice_no']) ? $data['invoice_no'] : null,
                'due_date' => !empty($data['due_date']) ? $this->commonUtil->uf_date($data['due_date']) : null,
                'notify_me' => !empty($data['notify_before_days']) ? 1 : 0,
                'notify_before_days' => !empty($data['notify_before_days']) ? $data['notify_before_days'] : 0,
                'created_by' => Auth::user()->id
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
        return view('add_stock.show')->with(compact(
            'add_stock',
            'supplier',
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
        $add_stock = Transaction::find($id);
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();

        $po_nos = Transaction::where('type', 'purchase_order')->where('status', '!=', 'received')->pluck('po_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('add_stock.edit')->with(compact(
            'add_stock',
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'po_nos'
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
            $data = $request->except('_token');

            if (!empty($data['po_no'])) {
                $ref_transaction_po = Transaction::find($data['po_no']);
            }

            $transaction_data = [
                'store_id' => $data['store_id'],
                'supplier_id' => $data['supplier_id'],
                'type' => 'add_stock',
                'status' => $data['status'],
                'order_date' => !empty($ref_transaction_po) ? $ref_transaction_po->transaction_date : Carbon::now(),
                'transaction_date' => $this->commonUtil->uf_date($data['transaction_date']),
                'payment_status' => $data['payment_status'],
                'po_no' => !empty($ref_transaction_po) ? $ref_transaction_po->po_no : null,
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'notes' => !empty($data['notes']) ? $data['notes'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'invoice_no' => !empty($data['invoice_no']) ? $data['invoice_no'] : null,
                'due_date' => !empty($data['due_date']) ? $this->commonUtil->uf_date($data['due_date']) : null,
                'notify_me' => !empty($data['notify_before_days']) ? 1 : 0,
                'notify_before_days' => !empty($data['notify_before_days']) ? $data['notify_before_days'] : 0,
                'created_by' => Auth::user()->id
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
                $products = $this->productUtil->getDetailsFromProduct($product_id, $variation_id, $store_id);

                return view('add_stock.partials.product_row')
                    ->with(compact('products', 'index'));
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
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();

        $po_nos = Transaction::where('type', 'purchase_order')->where('status', '!=', 'received')->pluck('po_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('add_stock.import')->with(compact(
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
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
                'order_date' => !empty($ref_transaction_po) ? $ref_transaction_po->transaction_date : Carbon::now(),
                'transaction_date' => $this->commonUtil->uf_date($data['transaction_date']),
                'payment_status' => 'pending',
                'po_no' => !empty($ref_transaction_po) ? $ref_transaction_po->po_no : null,
                'purchase_order_id' => !empty($data['po_no']) ? $data['po_no'] : null,
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'notes' => !empty($data['notes']) ? $data['notes'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'invoice_no' => !empty($data['invoice_no']) ? $data['invoice_no'] : null,
                'due_date' => !empty($data['due_date']) ? $this->commonUtil->uf_date($data['due_date']) : null,
                'notify_me' => !empty($data['notify_me']) ? $data['notify_me'] : 0,
                'created_by' => Auth::user()->id
            ];

            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);

            Excel::import(new AddStockLineImport($transaction->id), $request->file);

            foreach ($transaction->add_stock_lines as $line) {
                $this->productUtil->updateProductQuantityStore($line->product_id, $line->variation_id, $transaction->store_id,  $line->quantity, 0);
            }

            $final_total = AddStockLine::where('transaction_id', $transaction->id)->sum('sub_total');
            $transaction->final_total = $final_total;
            $transaction->save();

            if ($request->files) {
                foreach ($request->file('files', []) as $key => $file) {

                    $transaction->addMedia($file)->toMediaCollection('add_stock');
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
}
