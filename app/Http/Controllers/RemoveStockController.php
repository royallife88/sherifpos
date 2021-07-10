<?php

namespace App\Http\Controllers;

use App\Models\RemoveStockLine;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Transaction;
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
    public function index()
    {
        $query = Transaction::where('type', 'remove_stock')->where('status', '!=', 'draft');

        if (!empty(request()->supplier_id)) {
            $query->where('supplier_id', request()->supplier_id);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->where('transaction_date', '<=', request()->end_date);
        }

        $remove_stocks = $query->get();

        $suppliers = Supplier::pluck('name', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('remove_stock.index')->with(compact(
            'remove_stocks',
            'suppliers',
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
        $stores = Store::pluck('name', 'id');

        $invoice_nos = Transaction::where('type', 'add_stock')->where('status', 'received')->pluck('invoice_no', 'id');
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
            $transaction = Transaction::create($transaction_data);

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
        $stores = Store::pluck('name', 'id');

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
    public function getInvoiceDetails($id)
    {
        $add_stock = Transaction::find($id);

        $html = view('remove_stock.partials.product_rows')->with(compact(
            'add_stock'
        ))->render();

        return ['html' => $html, 'payment_status' => ucfirst($add_stock->payment_status)];
    }
}
