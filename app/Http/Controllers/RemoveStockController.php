<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RemoveStockLine;
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

class RemoveStockController extends Controller
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
    public function getCompensated()
    {
        $query = Transaction::where('type', 'remove_stock')->where('status', '!=', 'draft');

        if (!empty(request()->supplier_id)) {
            $query->where('supplier_id', request()->supplier_id);
        }
        if (!empty(request()->store_id)) {
            $query->where('store_id', request()->store_id);
        }
        if (!empty(request()->status)) {
            $query->where('status', request()->status);
        }
        if (!empty(request()->created_by)) {
            $query->where('created_by', request()->created_by);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->where('transaction_date', '<=', request()->end_date);
        }

        $remove_stocks = $query->where('status', 'compensated')->orderBy('compensated_at', 'desc')->get();

        $suppliers = Supplier::pluck('name', 'id');
        $users = User::pluck('name', 'id');
        $stores = Store::pluck('name', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('remove_stock.compensated_list')->with(compact(
            'remove_stocks',
            'suppliers',
            'users',
            'stores',
            'status_array'
        ));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Transaction::where('type', 'remove_stock')->where('status', '!=', 'draft');

        if (!empty(request()->supplier_id)) {
            $query->where('supplier_id', request()->supplier_id);
        }
        if (!empty(request()->store_id)) {
            $query->where('store_id', request()->store_id);
        }
        if (!empty(request()->status)) {
            $query->where('status', request()->status);
        }
        if (!empty(request()->created_by)) {
            $query->where('created_by', request()->created_by);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->where('transaction_date', '<=', request()->end_date);
        }

        $remove_stocks = $query->get();

        $suppliers = Supplier::pluck('name', 'id');
        $users = User::pluck('name', 'id');
        $stores = Store::pluck('name', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('remove_stock.index')->with(compact(
            'remove_stocks',
            'suppliers',
            'stores',
            'users',
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
        $suppliers = Supplier::pluck('name', 'id');
        $stores = Store::getDropdown();

        $invoice_nos = Transaction::where('type', 'add_stock')->where('status', 'received')->whereNotNull('invoice_no')->pluck('invoice_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('remove_stock.create')->with(compact(
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'invoice_nos'
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

            $product_data = json_decode($data['product_data'], true);
            $transaction_array = json_decode($data['transaction_array'], true);

            DB::beginTransaction();
            foreach ($transaction_array as $transaction_id) {
                $product_array = $this->getTransactionRealtedProductData($product_data, $transaction_id);
                $prent_transaction = Transaction::find($transaction_id);

                $transaction_data = [
                    'store_id' => $prent_transaction->store_id,
                    'supplier_id' => $prent_transaction->supplier_id,
                    'add_stock_id' => !empty($prent_transaction->id) ? $prent_transaction->id : null,
                    'type' => 'remove_stock',
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'transaction_date' => Carbon::now(),
                    'final_total' => 0,
                    'grand_total' => 0,
                    'notes' => !empty($product_array['notes']) ? $product_array['notes'] : null,
                    'details' => !empty($data['details']) ? $data['details'] : null,
                    'reason' => !empty($data['reason']) ? $data['reason'] : null,
                    'invoice_no' => $this->productUtil->getNumberByType('remove_stock'),
                    'created_by' => Auth::user()->id
                ];


                $transaction = Transaction::create($transaction_data);

                $line_data = [];
                $final_total = 0;
                foreach ($product_array as $value) {

                    $product = Product::find($value['product_id']);

                    $line_data[] = [
                        'product_id' => $value['product_id'],
                        'variation_id' => $value['variation_id'],
                        'quantity' => $value['qty'],
                        'purchase_price' => $product->purchase_price,
                        'sub_total' => $product->purchase_price * $value['qty'],
                    ];
                    $final_total += $product->purchase_price * $value['qty'];
                }

                $transaction->final_total = $final_total;
                $transaction->grand_total = $final_total;
                $transaction->save();

                $this->productUtil->createOrUpdateRemoveStockLines($line_data, $transaction);

                if ($request->files) {
                    foreach ($request->file('files', []) as $key => $file) {
                        $transaction->addMedia($file)->toMediaCollection('remove_stock');
                    }
                }

                if ($data['submit'] == 'send_to_supplier') {
                    $this->notificationUtil->sendRemoveStockToSupplier($transaction->id, $product_array[0]['email']);
                }
            }

            DB::commit();


            if ($data['submit'] == 'print') {
                $print = 'true';
                $url = action('RemoveStockController@show', $transaction->id) . '?print=' . $print;

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

    public function getTransactionRealtedProductData($product_data, $transaction_id)
    {
        $product_array = [];

        foreach ($product_data as $value) {
            if (!empty($value)) {
                if ($value['transaction_id'] == $transaction_id) {
                    $product_array[] = $value;
                }
            }
        }

        return $product_array;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $remove_stock = Transaction::find($id);

        $supplier = Supplier::find($remove_stock->supplier_id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        return view('remove_stock.show')->with(compact(
            'remove_stock',
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
        $remove_stock = Transaction::find($id);
        $suppliers = Supplier::pluck('name', 'id');
        $stores = Store::getDropdown();

        $invoice_nos = Transaction::where('type', 'add_stock')->where('status', 'received')->pluck('invoice_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('remove_stock.edit')->with(compact(
            'remove_stock',
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'invoice_nos'
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


            $transaction_data = [
                'store_id' => $data['store_id'],
                'supplier_id' => $data['supplier_id'],
                'add_stock_id' => !empty($data['invoice_id']) ? $data['invoice_id'] : null,
                'type' => 'remove_stock',
                'status' => 'final',
                'transaction_date' => Carbon::now(),
                'final_total' => $this->commonUtil->num_uf($data['final_total']),
                'grand_total' => $this->commonUtil->num_uf($data['final_total']),
                'notes' => !empty($data['notes']) ? $data['notes'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'reason' => !empty($data['reason']) ? $data['reason'] : null,
                'invoice_no' => $this->productUtil->getNumberByType('remove_stock'),
                'created_by' => Auth::user()->id
            ];

            DB::beginTransaction();
            $transaction = Transaction::find($id);
            $transaction->update($transaction_data);

            $this->productUtil->createOrUpdateRemoveStockLines($request->remove_stock_lines, $transaction);

            if ($request->files) {
                foreach ($request->file('files', []) as $key => $file) {
                    $transaction->addMedia($file)->toMediaCollection('remove_stock');
                }
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
            $deleted_lines = RemoveStockLine::where('transaction_id', $id)->get();
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

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProduct($product_id, $variation_id);

                return view('remove_stock.partials.product_row')
                    ->with(compact('products', 'index'));
            }
        }
    }

    /**
     * return product rows of add stock
     *
     * @param int $id
     * @return void
     */
    public function getInvoiceDetails()
    {
        $id = request()->input('id');
        $query = Transaction::where('id', '>', 0);
        if (!empty($id)) {
            $query->where('id', $id);
        }
        if (!empty($supplier_id)) {
            $query->where('supplier_id', $supplier_id);
        }
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }
        $add_stocks = $query->get();

        $store_id = request()->get('store_id');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        $html = view('remove_stock.partials.product_rows')->with(compact(
            'add_stocks',
            'payment_status_array',
            'store_id'
        ))->render();

        return ['html' => $html];
    }

    /**
     * get supplier dropdown
     *
     * @param int $id
     * @return html
     */
    public function getSupplierInvoicesDropdown($id)
    {
        $invoice_nos = Transaction::where('type', 'add_stock')->where('status', 'received')->where('supplier_id', $id)->whereNotNull('invoice_no')->pluck('invoice_no', 'id');

        $html = $this->commonUtil->createDropdownHtml($invoice_nos, __('lang.please_select'));

        return $html;
    }

    /**
     * update status as compensated
     *
     * @param [type] $id
     * @return void
     */
    public function getUpdateStatusAsCompensated($id)
    {
        $transaction = Transaction::find($id);

        return view('remove_stock.partials.update_status')->with(compact(
            'transaction'
        ));
    }
    /**
     * update status as compensated
     *
     * @param [type] $id
     * @return void
     */
    public function postUpdateStatusAsCompensated($id)
    {
        try {
            $transaction = Transaction::find($id);
            $transaction->status = 'compensated';
            $transaction->compensated_at = request()->input('compensated_at');
            $transaction->compensated_invoice_no = request()->input('compensated_invoice_no');
            $transaction->compensated_value = request()->input('compensated_value');
            $transaction->save();

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
