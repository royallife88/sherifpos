<?php

namespace App\Http\Controllers;

use App\Imports\PurchaseOrderLineImport;
use App\Models\Product;
use App\Models\PurchaseOrderLine;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Variation;
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
use Mpdf\Tag\Sup;

class PurchaseOrderController extends Controller
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

        $query = Transaction::where('type', 'purchase_order')->where('status', '!=', 'draft');

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
        // TODO: condition for superadmin --sent_admin and for other user as draft
        $purchase_orders = $query->get();

        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('purchase_order.index')->with(compact(
            'purchase_orders',
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

        $po_no = $this->productUtil->getNumberByType('purchase_order');

        return view('purchase_order.create')->with(compact(
            'suppliers',
            'stores',
            'po_no'
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
                'type' => 'purchase_order',
                'status' => 'pending',
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
                'payment_status' => 'due',
                'po_no' => $data['po_no'],
                'grand_total' => $this->productUtil->num_uf($data['final_total']),
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'details' => $data['details'],
                'created_by' => Auth::user()->id
            ];

            if ($data['submit'] == 'sent_admin') {
                $transaction_data['status'] = 'sent_admin';
            }
            if ($data['submit'] == 'sent_supplier') {
                $transaction_data['status'] = 'sent_supplier';
            }

            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);

            $this->productUtil->createOrUpdatePurchaseOrderLines($request->purchase_order_lines, $transaction);

            if ($data['submit'] == 'sent_admin') {
                $superadmins = User::where('is_superadmin', 1)->get();
                $notification_data = [
                    'user_id' => null,
                    'transaction_id' => $transaction->id,
                    'type' => $transaction->type,
                    'status' => 'unread',
                    'created_by' => Auth::user()->id,
                ];
                foreach ($superadmins as $admin) {
                    $notification_data['user_id'] = $admin->id;
                    $this->notificationUtil->createNotification($notification_data);
                }
            }
            DB::commit();
            if ($data['submit'] == 'print') {
                $print = 'print';
                $url = action('PurchaseOrderController@show', $transaction->id) . '?print=' . $print;

                return Redirect::to($url);
            }
            if ($data['submit'] == 'sent_supplier') {
                $this->notificationUtil->sendPurchaseOrderToSupplier($transaction->id);
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
        $purchase_order = Transaction::find($id);

        $supplier = Supplier::find($purchase_order->supplier_id);

        return view('purchase_order.show')->with(compact(
            'purchase_order',
            'supplier'
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
        $purchase_order = Transaction::find($id);

        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('purchase_order.edit')->with(compact(
            'purchase_order',
            'status_array',
            'suppliers',
            'stores'
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
            $data = $request->except('_token', '_method');

            $transaction_data = [
                'store_id' => $data['store_id'],
                'supplier_id' => $data['supplier_id'],
                'type' => 'purchase_order',
                'status' => $data['status'],
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
                'payment_status' => 'due',
                'po_no' => $data['po_no'],
                'grand_total' => $this->productUtil->num_uf($data['final_total']),
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'details' => $data['details'],
                'created_by' => Auth::user()->id
            ];

            if ($data['submit'] == 'sent_admin') {
                $transaction_data['status'] = 'sent_admin';
            }
            if ($data['submit'] == 'sent_supplier') {
                $transaction_data['status'] = 'sent_supplier';
            }

            DB::beginTransaction();
            $transaction = Transaction::find($id);
            $transaction->update($transaction_data);

            $this->productUtil->createOrUpdatePurchaseOrderLines($request->purchase_order_lines, $transaction);

            if ($data['submit'] == 'sent_admin') {
                $superadmins = User::where('is_superadmin', 1)->get();
                $notification_data = [
                    'user_id' => null,
                    'transaction_id' => $transaction->id,
                    'type' => $transaction->type,
                    'status' => 'unread',
                    'created_by' => Auth::user()->id,
                ];
                foreach ($superadmins as $admin) {
                    $notification_data['user_id'] = $admin->id;
                    $this->notificationUtil->createNotification($notification_data);
                }
            }


            DB::commit();

            if ($data['submit'] == 'print') {
                $print = 'print';
                $url = action('PurchaseOrderController@show', $transaction->id) . '?print=' . $print;

                return Redirect::to($url);
            }
            if ($data['submit'] == 'sent_supplier') {
                $this->notificationUtil->sendPurchaseOrderToSupplier($transaction->id);
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
            $purchase_order = Transaction::find($id)->delete();
            PurchaseOrderLine::where('transaction_id', $id)->delete();

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

    public function getProducts()
    {
        if (request()->ajax()) {

            $term = request()->term;

            if (empty($term)) {
                return json_encode([]);
            }

            $q = Product::leftJoin(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id')
                ->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('variations.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                })
                ->whereNull('variations.deleted_at')
                ->where('is_service', 0)
                ->select(
                    'products.*',
                    'products.id as product_id',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                );

            if (!empty(request()->store_id)) {
                $q->where('product_stores.store_id', request()->store_id);
            }
            if (!empty(request()->is_raw_material)) {
                $q->where('products.is_raw_material', 1);
            } else {
                $q->where('products.is_raw_material', 0);
            }
            $products = $q->groupBy('variation_id')->get();

            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['image'] = !empty($product->getFirstMediaUrl('product')) ? $product->getFirstMediaUrl('product') : asset('/uploads/' . session('logo'));
                $products_array[$product->product_id]['variations'][]
                    = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku
                    ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (!empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    // if ($no_of_records > 1 && $value['type'] != 'single') {
                    //     $result[] = [
                    //         'id' => $i,
                    //         'text' => $value['name'] . ' - ' . $value['sku'],
                    //         'variation_id' => 0,
                    //         'product_id' => $key
                    //     ];
                    // }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            if ($variation['variation_name'] != 'Default') {
                                $text = $text . ' (' . $variation['variation_name'] . ')';
                            }
                        }
                        $i++;
                        $result[] = [
                            'id' => $i,
                            'text' => $text . ' - ' . $variation['sub_sku'],
                            'product_id' => $key,
                            'variation_id' => $variation['variation_id'],
                            'image' => $value['image'],
                        ];
                    }
                    $i++;
                }
            }

            return json_encode($result);
        }
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

                return view('purchase_order.partials.product_row')
                    ->with(compact('products', 'index'));
            }
        }
    }

    /**
     * getPoNumber
     *
     * @param Request $request
     * @return string
     */
    public function getPoNumber(Request $request)
    {

        $po_no = $this->productUtil->getNumberByType('purchase_order', request()->store_id);

        return $po_no;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDraftPurchaseOrder()
    {
        $query = Transaction::where('type', 'purchase_order')->where('status', 'draft');

        if (!empty(request()->supplier_id)) {
            $query->where('supplier_id', request()->supplier_id);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->where('transaction_date', '<=', request()->end_date);
        }

        $purchase_orders = $query->orderBy('created_at', 'desc')->get();

        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('purchase_order.index')->with(compact(
            'purchase_orders',
            'suppliers',
            'stores',
            'status_array'
        ));
    }
    /**
     *  quick add purchase order as draft invoked from pos page if product stock is low
     *
     * @return \Illuminate\Http\Response
     */
    public function quickAddDraft(Request $request)
    {
        $variation = Variation::find($request->variation_id);

        try {
            $transaction_data = [
                'store_id' => $request->store_id,
                'supplier_id' => null,
                'type' => 'purchase_order',
                'status' => 'draft',
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
                'payment_status' => 'due',
                'po_no' =>  $this->productUtil->getNumberByType('purchase_order'),
                'final_total' => $this->productUtil->num_uf($variation->default_purchase_price),
                'details' => 'Created from POS page',
                'created_by' => Auth::user()->id
            ];

            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);

            $purchase_order_line_data = [
                'transaction_id' => $transaction->id,
                'product_id' => $request->product_id,
                'variation_id' => $request->variation_id,
                'quantity' => 1,
                'purchase_price' => $this->productUtil->num_uf($variation->default_purchase_price),
                'sub_total' => $this->productUtil->num_uf($variation->default_purchase_price * 1),
            ];

            PurchaseOrderLine::create($purchase_order_line_data);

            $notification_data = [
                'user_id' => Auth::user()->id,
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                'status' => 'unread',
                'created_by' => Auth::user()->id,
            ];
            $this->notificationUtil->createNotification($notification_data);

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

    public function getImport()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();

        return view('purchase_order.import')->with(compact(
            'suppliers',
            'stores',
        ));
    }

    public function saveImport(Request $request)
    {
        try {
            $data = $request->except('_token');

            $transaction_data = [
                'store_id' => $data['store_id'],
                'supplier_id' => $data['supplier_id'],
                'type' => 'purchase_order',
                'status' => 'pending',
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
                'payment_status' => 'due',
                'po_no' => $data['po_no'],
                'grand_total' => 0,
                'final_total' => 0,
                'details' => $data['details'],
                'created_by' => Auth::user()->id
            ];

            if ($data['submit'] == 'sent_admin') {
                $transaction_data['status'] = 'sent_admin';
            }
            if ($data['submit'] == 'sent_supplier') {
                $transaction_data['status'] = 'sent_supplier';
            }

            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);

            Excel::import(new PurchaseOrderLineImport($transaction->id), $request->file);

            $final_total = PurchaseOrderLine::where('transaction_id', $transaction->id)->sum('sub_total');
            $transaction->grand_total = $final_total;
            $transaction->final_total = $final_total;
            $transaction->save();

            DB::commit();

            if ($data['submit'] == 'print') {
                $print = 'print';
                $url = action('PurchaseOrderController@show', $transaction->id) . '?print=' . $print;

                return Redirect::to($url);
            }
            if ($data['submit'] == 'sent_supplier') {
                $this->notificationUtil->sendPurchaseOrderToSupplier($transaction->id);
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
}
