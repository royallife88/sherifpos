<?php

namespace App\Http\Controllers;

use App\Models\AddStockLine;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Grade;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\Size;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransferLine;
use App\Models\Unit;
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

class InternalStockRequestController extends Controller
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
        if (!auth()->user()->can('stock.internal_stock_request.view')) {
            abort(403, 'Unauthorized action.');
        }

        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $query = Transaction::where('type', 'add_stock')->where('status', '!=', 'draft');

        if (!empty($store_id)) {
            $query->where('transactions.store_id', $store_id);
        }

        if (!empty(request()->supplier_id)) {
            $query->where('supplier_id', request()->supplier_id);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->where('transaction_date', '<=', request()->end_date);
        }

        $add_stocks = $query->orderBy('transaction_date', 'desc')->get();

        $suppliers = Supplier::pluck('name', 'id');
        $stores = Store::getDropdown();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('internal_stock_request.index')->with(compact(
            'add_stocks',
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
        if (!auth()->user()->can('stock.internal_stock_request.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $stores = Store::getDropdown();
        $stores_keys = array_keys($stores);

        $products = $this->productUtil->getProductList($stores_keys);
        $product_classes = ProductClass::pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = Unit::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');

        return view('internal_stock_request.create')->with(compact(
            'products',
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'stores',
            'taxes'
        ));
    }

    public function getStoreRealtedProductData($product_data, $store)
    {
        $product_array = [];

        foreach ($product_data as $key => $value) {
            if (!empty($value)) {
                if ($value['store_id'] == $store) {
                    $product_array[] = $value;
                }
            }
        }

        return $product_array;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('stock.internal_stock_request.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        // try {
        $data = $request->except('_token');
        $invoice_no = $this->productUtil->getNumberByType('internal_stock_request');

        $product_data = json_decode($data['product_data'], true);
        $store_array = json_decode($data['store_array'], true);
        DB::beginTransaction();

        foreach ($store_array as  $store) {
            $product_array = $this->getStoreRealtedProductData($product_data, $store);

            if (!empty($product_array)) {
                $transaction_data = [
                    'sender_store_id' => $store,
                    'receiver_store_id' => $data['receiver_store_id'],
                    'type' => 'transfer',
                    'status' => $data['status'],
                    'transaction_date' => Carbon::now(),
                    'is_internal_stock_transfer' => 1,
                    'final_total' => 0,
                    'notes' => !empty($data['notes']) ? $data['notes'] : null,
                    'details' => !empty($data['details']) ? $data['details'] : null,
                    'invoice_no' => $invoice_no,
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
                $transaction->save();

                $this->productUtil->createOrUpdateInternalStockRequestLines($line_data, $transaction);
                if ($transaction->status == 'approved') {
                    $this->updateStockInStores($transaction);
                }
            }
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

    public function updateStockInStores($transaction)
    {
        $transfer_lines = TransferLine::where('transaction_id', $transaction->id)->get();
        foreach ($transfer_lines as $line) {
            $this->productUtil->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->sender_store_id, $line['quantity'], 0);
            $this->productUtil->updateProductQuantityStore($line['product_id'], $line['variation_id'], $transaction->receiver_store_id,  $line['quantity'], 0);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    }

    /**
     * get prdudct table
     *
     * @return void
     */
    public function getProductTable()
    {
        $stores = Store::getDropdown();
        $stores_keys = array_keys($stores);
        $products = $this->productUtil->getProductList($stores_keys);

        return view('internal_stock_request.partials.product_table')->with(compact(
            'products'
        ));
    }


    public function getUpdateStatus($id)
    {
        $transaction = Transaction::find($id);

        return view('internal_stock_request.partials.update_status')->with(compact(
            'transaction'
        ));
    }

    public function postUpdateStatus(Request $request, $id)
    {
        // try {
        $transaction = Transaction::find($id);

        DB::beginTransaction();
        $transaction->status = $request->status;
        $transaction->save();

        if ($transaction->status == 'approved') {
            $this->updateStockInStores($transaction);
        }
        DB::commit();

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
}
