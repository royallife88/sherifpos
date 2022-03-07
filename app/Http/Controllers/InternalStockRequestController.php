<?php

namespace App\Http\Controllers;

use App\Models\AddStockLine;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Grade;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductStore;
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
use Yajra\DataTables\Facades\DataTables;

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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('stock.internal_stock_request.create_and_edit') && !auth()->user()->can('raw_material_module.internal_stock_request.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $stores = Store::getDropdown();
        $stores_keys = array_keys($stores);

        $products = $this->productUtil->getProductList($stores_keys);
        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $taxes = Tax::orderBy('name', 'asc')->pluck('name', 'id');

        $is_raw_material = request()->segment(1) == 'raw-materials' ? true : false;

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
            'taxes',
            'is_raw_material'
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
        if (!auth()->user()->can('stock.internal_stock_request.create_and_edit') && !auth()->user()->can('raw_material_module.internal_stock_request.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        try {
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
                        'created_by' => Auth::user()->id,
                        'requested_by' => Auth::user()->id,
                        'is_raw_material' => !empty($request->is_raw_material) ? 1 : 0,
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
                    if ($transaction->status == 'approved' || $transaction->status == 'received') {
                        $this->updateStockInStores($transaction);
                    }
                    $this->notificationUtil->notifyInternalStockRequest($transaction);
                }
            }

            DB::commit();

            // if ($data['submit'] == 'print') {
            //     $print = 'print';
            //     $url = action('AddStockController@show', $transaction->id) . '?print=' . $print;

            //     return Redirect::to($url);
            // }

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

    public function updateStockInStores($transaction)
    {
        $transfer_lines = TransferLine::where('transaction_id', $transaction->id)->get();
        foreach ($transfer_lines as $line) {
            if ($transaction->status == 'approved') {
                $this->productUtil->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->sender_store_id, $line['quantity'], 0);
            }
            if ($transaction->status == 'received') {
                $this->productUtil->updateProductQuantityStore($line['product_id'], $line['variation_id'], $transaction->receiver_store_id,  $line['quantity'], 0);
            }
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
        if (request()->ajax()) {

            $query = Product::leftjoin('add_stock_lines', 'products.id', 'add_stock_lines.product_id')
                ->leftjoin('transactions', 'add_stock_lines.transaction_id', 'transactions.id')
                ->leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id')
                ->leftjoin('stores', 'product_stores.store_id', 'stores.id')
                ->leftjoin('product_classes', 'products.product_class_id', 'product_classes.id')
                ->leftjoin('categories', 'products.category_id', 'categories.id')
                ->leftjoin('categories as sub_categories', 'products.sub_category_id', 'categories.id')
                ->leftjoin('colors', 'variations.color_id', 'colors.id')
                ->leftjoin('sizes', 'variations.size_id', 'sizes.id')
                ->leftjoin('grades', 'variations.grade_id', 'grades.id')
                ->leftjoin('units', 'variations.unit_id', 'units.id')
                ->leftjoin('taxes', 'products.tax_id', 'taxes.id')
                ->leftjoin('brands', 'products.brand_id', 'brands.id')
                ->leftjoin('suppliers', 'transactions.supplier_id', 'suppliers.id')
                ->where('transactions.type',  'add_stock');

            if (!empty(request()->is_raw_material)) {
                $query->where('products.is_raw_material', 1);
            } else {
                $query->where('products.is_raw_material', 0);
            }
            $query = $query->select(
                'transactions.payment_status',
                'transactions.transaction_date',
                'transactions.invoice_no',
                'products.discount',
                'products.sell_price',
                'products.id as product_id',
                'variations.id as variation_id',
                'products.name as product_name',
                'variations.name as variation_name',
                'variations.sub_sku',
                'colors.name as color',
                'sizes.name as size',
                'grades.name as grade',
                'units.name as unit',
                'suppliers.name as supplier',
                'suppliers.email as supplier_email',
                'product_classes.name as product_class',
                'categories.name as category',
                'sub_categories.name as sub_category',
                'add_stock_lines.batch_number',
                'add_stock_lines.purchase_price',
                'add_stock_lines.quantity',
                'add_stock_lines.expiry_date',
                'add_stock_lines.manufacturing_date',
                'product_stores.id as index',
                'stores.name as store_name',
                'stores.id as store_id',
                'taxes.id as tax_name',
                'brands.name as brand_name',
            );

            $products = $query->addSelect(
                DB::raw('SUM(product_stores.qty_available) as current_stock'),
            )->having('current_stock', '>', 0)
                ->groupBy('products.id', 'product_stores.id');



            $payment_status_array = $this->commonUtil->getPaymentStatusArray();

            return DataTables::of($products)
                ->addColumn('selected_product', function ($row) {
                    $html = '<input type="hidden" class="row_index" name="row_index" value="' . $row->index . '">';
                    $html .= '<input type="hidden" class="product_id" name="product_ids[' . $row->index . ']" value="' . $row->product_id . '">';
                    $html .= '<input type="hidden" class="variation_id" name="variation_id[' . $row->index . ']" value="' . $row->variation_id . '">';
                    $html .= '<input type="hidden" class="purchase_price" name="purchase_price[' . $row->index . ']" value="' . $row->purchase_price . '">';
                    $html .= '<input type="hidden" class="transaction_id" name="transaction_id[' . $row->index . ']" value="' . $row->id . '">';
                    $html .= '<input type="hidden" class="store_id" name="store_id[' . $row->index . ']" value="' . $row->store_id . '">';
                    $html .= '<input type="checkbox" class="product_checkbox" name="product_selected[]" value="' . $row->index . '">';

                    return $html;
                })
                ->addColumn('image', function ($row) {
                    $image = $row->getFirstMediaUrl('product');
                    if (!empty($image)) {
                        return '<img src="' . $image . '" height="50px" width="50px">';
                    } else {
                        return '<img src="' . asset('/uploads/' . session('logo')) . '" height="50px" width="50px">';
                    }
                })
                ->editColumn('variation_name', '{{$product_name}} @if($variation_name != "Default"){{$variation_name}} @endif')
                ->editColumn('sub_sku', '{{$sub_sku}}')
                ->editColumn('payment_status', function ($row) use ($payment_status_array) {
                    return $payment_status_array[$row->payment_status];
                })
                ->editColumn('expiry_date', '@if(!empty($expiry_date)){{@format_date($expiry_date)}}@endif')
                ->editColumn('manufacturing_date', '@if(!empty($manufacturing_date)){{@format_date($manufacturing_date)}}@endif')
                ->editColumn('sell_price', '{{@num_format($sell_price)}}')
                ->editColumn('purchase_price', '{{@num_format($purchase_price)}}')
                ->editColumn('quantity', '{{@num_format($quantity)}}')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->addColumn('product_class', '{{$product_class}}')
                ->addColumn('category', '{{$category}}')
                ->addColumn('sub_category', '{{$sub_category}}')
                ->addColumn('qty', function ($row) {
                    $query = ProductStore::where(
                        'product_id',
                        $row->product_id
                    )->where('variation_id', $row->variation_id);
                    if (!empty($row->store_id)) {
                        $query->where('store_id', $row->store_id);
                    }
                    $current_stock_query = $query->first();
                    $current_stock = 0;

                    if (!empty($current_stock_query)) {
                        $current_stock = $current_stock_query->qty_available;
                    }

                    $html = '<input type="text" class="form-control qty" min=1
                    max="' . $current_stock . '"
                    name="qty[' . $row->index . ']" required value="0" style="width: 100px !important; border: 1px solid #999">';
                    $html .= '<span class="error stock_error hide">' . __('lang.quantity_should_not_greater_than') . '
                    ' . $this->productUtil->num_uf($current_stock) . '</span>
                    <input type="hidden" class="current_stock" name="current_stock" value="' . $current_stock . '">';

                    return $html;
                })
                ->addColumn('purchase_history', function ($row) {
                    $html = '<a data-href="' . action('ProductController@getPurchaseHistory', $row->product_id) . '"
                    data-container=".view_modal" class="btn btn-modal">' . __('lang.view') . '</a>';
                    return $html;
                })
                ->editColumn('notes', function ($row) {
                    return '<input type="text" class="form-control notes" name="remove_stock_lines[' . $row->index . '][notes]" id="" value="">';
                })
                ->addColumn('current_stock', function ($row) {
                    $query = ProductStore::where(
                        'product_id',
                        $row->product_id
                    )->where('variation_id', $row->variation_id);
                    if (!empty($row->store_id)) {
                        $query->where('store_id', $row->store_id);
                    }
                    $current_stock_query = $query->first();
                    $current_stock = 0;

                    if (!empty($current_stock_query)) {
                        $current_stock = $current_stock_query->qty_available;
                    }

                    return $this->productUtil->num_uf($current_stock);
                })

                ->rawColumns([
                    'selected_product',
                    'image',
                    'variation_name',
                    'sku',
                    'product_class',
                    'category',
                    'sub_category',
                    'purchase_history',
                    'batch_number',
                    'sell_price',
                    'tax',
                    'brand',
                    'unit',
                    'color',
                    'size',
                    'grade',
                    'qty',
                    'supplier_email',
                    'purchase_price',
                    'notes',
                ])
                ->make(true);
        }
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

        return view('internal_stock_request.partials.update_status')->with(compact(
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
    public function postUpdateStatus(Request $request, $id)
    {
        try {
            $transaction = Transaction::find($id);

            DB::beginTransaction();
            $final_total = $this->productUtil->createOrUpdateInternalStockRequestLines($request->transfer_lines, $transaction);
            $transaction->final_total = $final_total;

            $transaction->status = $request->status;
            if ($transaction->status == 'approved') {
                $transaction->approved_at = Carbon::now();
                $transaction->approved_by = Auth::user()->id;
                $transaction->save();
            }
            if ($transaction->status == 'received') {
                $transaction->received_at = Carbon::now();
                $transaction->received_by = Auth::user()->id;
                $transaction->save();
            }
            if ($transaction->status == 'declined') {
                $transaction->declined_at = Carbon::now();
                $transaction->declined_by = Auth::user()->id;
                $transaction->save();
            }
            $transaction->save();
            $this->notificationUtil->notifyInternalStockRequest($transaction);

            if ($transaction->status == 'approved' || $transaction->status == 'received') {
                $this->updateStockInStores($transaction);
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
}
