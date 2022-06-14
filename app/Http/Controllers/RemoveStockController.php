<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStore;
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
use Yajra\DataTables\Facades\DataTables;

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
        if (!empty(request()->start_time)) {
            $query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }

        $remove_stocks = $query->where('status', 'compensated')->orderBy('compensated_at', 'desc')->get();

        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
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
        $is_raw_material = request()->segment(1) == 'raw-materials' ? true : false;
        if (!empty($is_raw_material)) {
            $query->where('is_raw_material', 1);
        } else {
            $query->where('is_raw_material', 0);
        }

        $remove_stocks = $query->get();

        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
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
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();

        $invoice_nos = Transaction::where('type', 'add_stock')->where('status', 'received')->whereNotNull('invoice_no')->pluck('invoice_no', 'id');
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        $is_raw_material = request()->segment(1) == 'raw-materials' ? true : false;

        return view('remove_stock.create')->with(compact(
            'suppliers',
            'status_array',
            'payment_status_array',
            'payment_type_array',
            'stores',
            'invoice_nos',
            'is_raw_material'
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
                    'is_raw_material' => !empty($data['is_raw_material']) ? $data['is_raw_material'] : 0,
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
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
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
                'is_raw_material' => !empty($data['is_raw_material']) ? $data['is_raw_material'] : 0,
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
        $id = request()->input('invoice_id');
        $store_id = request()->get('store_id');
        $supplier_id = request()->get('supplier_id');

        if (request()->ajax()) {

            $query = Transaction::leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
                ->leftjoin('products', 'add_stock_lines.product_id', 'products.id')
                ->leftjoin('variations', 'add_stock_lines.variation_id', 'variations.id')
                ->leftjoin('product_classes', 'products.product_class_id', 'product_classes.id')
                ->leftjoin('categories', 'products.category_id', 'categories.id')
                ->leftjoin('categories as sub_categories', 'products.sub_category_id', 'categories.id')
                ->leftjoin('colors', 'variations.color_id', 'colors.id')
                ->leftjoin('sizes', 'variations.size_id', 'sizes.id')
                ->leftjoin('grades', 'variations.grade_id', 'grades.id')
                ->leftjoin('units', 'variations.unit_id', 'units.id')
                ->leftjoin('suppliers', 'transactions.supplier_id', 'suppliers.id')
                ->where('transactions.type',  'add_stock');
            if (!empty($id)) {
                $query->where('transactions.id', $id);
            }
            if (!empty($supplier_id)) {
                $query->where('transactions.supplier_id', $supplier_id);
            }
            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
            }
            if (!empty(request()->is_raw_material)) {
                $query->where('products.is_raw_material', 1);
            } else {
                $query->where('products.is_raw_material', 0);
            }
            $add_stocks = $query->select(
                'transactions.*',
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
                'add_stock_lines.purchase_price',
                'add_stock_lines.quantity',
                'add_stock_lines.id as index',
            )->groupBy('add_stock_lines.id');


            $payment_status_array = $this->commonUtil->getPaymentStatusArray();


            return DataTables::of($add_stocks)
                ->addColumn('selected_product', function ($row) {
                    $html = '<input type="hidden" class="row_index" name="row_index" value="' . $row->index . '">';
                    $html .= '<input type="hidden" class="product_id" name="product_ids[' . $row->index . ']" value="' . $row->product_id . '">';
                    $html .= '<input type="hidden" class="variation_id" name="variation_id[' . $row->index . ']" value="' . $row->variation_id . '">';
                    $html .= '<input type="hidden" class="purchase_price" name="purchase_price[' . $row->index . ']" value="' . $row->purchase_price . '">';
                    $html .= '<input type="hidden" class="transaction_id" name="transaction_id[' . $row->index . ']" value="' . $row->id . '">';
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
                ->editColumn('sell_price', '{{@num_format($sell_price)}}')
                ->editColumn('purchase_price', '{{@num_format($purchase_price)}}')
                ->editColumn('quantity', '{{@num_format($quantity)}}')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->addColumn('product_class', '{{$product_class}}')
                ->addColumn('category', '{{$category}}')
                ->addColumn('sub_category', '{{$sub_category}}')
                ->addColumn('remove_qauntity', function ($row) use ($store_id) {
                    $html = '<input type="text" class="form-control quantity" min=1
                    max="' . $row->quantity . '"
                    name="remove_stock_lines[' . $row->index . '][quantity]" required value="0">';
                    $html .= '<span class="error stock_error hide">' . __('lang.quantity_should_not_greater_than') . '
                    ' . $row->quantity . '</span>
                    <input type="hidden" class="form-control sub_total" min=1 name="remove_stock_lines[' . $row->index . '][sub_total]" required
                    value="0"> ';
                    $html .= '<input type="hidden" class="form-control purchase_price" min=1 name="remove_stock_lines[' . $row->index . '][purchase_price]"
                    required value="' . $row->purchase_price . '">';

                    return $html;
                })
                ->editColumn('supplier_email', function ($row) {
                    $email = $row->supplier_email;
                    return '<input type="text" class="form-control email" style="width: 100px !important"
                    name="remove_stock_lines[' . $row->index . '][email]" id="" value="' . $email . '">';
                })
                ->editColumn('notes', function ($row) {
                    return '<input type="text" class="form-control notes" name="remove_stock_lines[' . $row->index . '][notes]" id="" value="">';
                })
                ->addColumn('current_stock', function ($row) use ($store_id) {
                    $query = ProductStore::where(
                        'product_id',
                        $row->product_id
                    )->where('variation_id', $row->variation_id);
                    if (!empty($store_id)) {
                        $query->where('store_id', $store_id);
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
                    'remove_qauntity',
                    'supplier_email',
                    'purchase_price',
                    'notes',
                ])
                ->make(true);
        }
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
