<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Models\SupplierProduct;
use App\Models\Transaction;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Supplier::leftjoin('transactions', 'suppliers.id', 'transactions.supplier_id');

        if (!empty(request()->supplier_category_id)) {
            $query->where('supplier_category_id', request()->supplier_category_id);
        }

        $suppliers =   $query->select(
            'suppliers.*',
            DB::raw("SUM(IF(transactions.type = 'add_stock' AND transactions.status = 'received', final_total, 0)) as total_invoice"),
            DB::raw("SUM(IF(transactions.type = 'add_stock' AND transactions.status = 'received', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=transactions.id), 0)) as total_paid"),
            DB::raw('COUNT(CASE WHEN transactions.type="purchase_order" AND transactions.status="sent_supplier" THEN 1 END) as pending_orders'),
            DB::raw('SUM(IF(transactions.type="add_stock" AND transactions.status="received", final_total, 0)) as total_purchase')
        )->groupBy('suppliers.id')->get();

        $supplier_categories = SupplierCategory::pluck('name', 'id');

        return view('supplier.index')->with(compact(
            'suppliers',
            'supplier_categories',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quick_add = request()->quick_add ?? null;

        $supplier_categories = SupplierCategory::pluck('name', 'id');
        $products = Product::pluck('name', 'id');

        if ($quick_add) {
            return view('supplier.quick_add')->with(compact(
                'supplier_categories',
                'quick_add',
                'products',
            ));
        }

        return view('supplier.create')->with(compact(
            'supplier_categories',
            'quick_add',
            'products',
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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'max:30'],
                'company_name' => ['nullable', 'max:30'],
                'vat_number' => ['nullable', 'max:30'],
                'email' => ['nullable', 'email'],
                'mobile_number' => ['nullable', 'max:30'],
                'address' => ['nullable', 'max:60'],
                'city' => ['nullable', 'max:30'],
                'state' => ['nullable', 'max:30'],
                'country' => ['nullable', 'max:30'],
                'postal_code' => ['nullable', 'max:30']
            ]
        );
        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            if ($request->ajax()) {
                return $output;
            }

            return redirect()->back()->with('status', $output);
        }
        try {
            $data = $request->except('_token', 'quick_add');
            $data['products'] = !empty($data['products']) ? $data['products'] : [];
            $data['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            $supplier = Supplier::create($data);

            if ($request->has('image')) {
                $supplier->addMedia($request->image)->toMediaCollection('supplier_photo');
            }

            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    SupplierProduct::updateOrCreate(['supplier_id' => $supplier->id, 'product_id' => $product]);
                }
            }

            $supplier_id = $supplier->id;

            DB::commit();
            $output = [
                'success' => true,
                'supplier_id' => $supplier_id,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }


        if ($request->ajax()) {
            return $output;
        }

        return redirect()->to('supplier')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier_id = $id;
        $supplier = Supplier::find($id);

        $add_stock_query = Transaction::whereIn('transactions.type', ['add_stock', 'purchase_return'])
            ->whereIn('transactions.status', ['received', 'final']);

        if (!empty(request()->start_date)) {
            $add_stock_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $add_stock_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($supplier_id)) {
            $add_stock_query->where('transactions.supplier_id', $supplier_id);
        }
        $add_stocks = $add_stock_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $purchase_order_query = Transaction::whereIn('transactions.type', ['purchase_order'])
            ->where('status', 'sent_supplier');

        if (!empty(request()->start_date)) {
            $purchase_order_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $purchase_order_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($supplier_id)) {
            $purchase_order_query->where('transactions.supplier_id', $supplier_id);
        }
        $purchase_orders = $purchase_order_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $service_provided_query = Transaction::whereIn('transactions.type', ['supplier_service'])
            ->where('status', 'final');

        if (!empty(request()->start_date)) {
            $service_provided_query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $service_provided_query->where('transaction_date', '<=', request()->end_date);
        }
        if (!empty($supplier_id)) {
            $service_provided_query->where('transactions.supplier_id', $supplier_id);
        }
        $service_provided = $service_provided_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('supplier.show')->with(compact(
            'add_stocks',
            'purchase_orders',
            'service_provided',
            'status_array',
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
        $supplier = Supplier::find($id);
        $supplier_categories = SupplierCategory::pluck('name', 'id');
        $products = Product::pluck('name', 'id');

        return view('supplier.edit')->with(compact(
            'supplier',
            'supplier_categories',
            'products',
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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'max:30'],
                'company_name' => ['nullable', 'max:30'],
                'vat_number' => ['nullable', 'max:30'],
                'email' => ['nullable', 'email'],
                'mobile_number' => ['nullable', 'max:30'],
                'address' => ['nullable', 'max:60'],
                'city' => ['nullable', 'max:30'],
                'state' => ['nullable', 'max:30'],
                'country' => ['nullable', 'max:30'],
                'postal_code' => ['nullable', 'max:30']
            ]
        );
        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            if ($request->ajax()) {
                return $output;
            }

            return redirect()->back()->with('status', $output);
        }

        try {
            $data = $request->except('_token', '_method');
            $data['products'] = !empty($data['products']) ? $data['products'] : [];

            DB::beginTransaction();
            $supplier = Supplier::find($id);
            $supplier->update($data);

            if ($request->has('image')) {
                if ($supplier->getFirstMedia('supplier_photo')) {
                    $supplier->getFirstMedia('supplier_photo')->delete();
                }
                $supplier->addMedia($request->image)->toMediaCollection('supplier_photo');
            }

            if (!empty($data['products'])) {
                foreach ($data['products'] as $product) {
                    SupplierProduct::updateOrCreate(['supplier_id' => $supplier->id, 'product_id' => $product]);
                }
            }
            SupplierProduct::whereNotIn('product_id', $data['products'])->where('supplier_id', $supplier->id)->delete();


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

        return redirect()->to('supplier')->with('status', $output);
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
            Supplier::find($id)->delete();
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
     * get detaisl of resource
     *
     * @param  int  $id
     * @return void
     */
    public function getDetails($id)
    {
        $supplier = Supplier::find($id);

        $is_purchase_order = request()->is_purchase_order;

        return view('supplier.details')->with(compact(
            'supplier',
            'is_purchase_order'
        ));
    }

    /**
     * get dropdown html
     *
     * @return void
     */
    public function getDropdown()
    {
        $supplier = Supplier::orderBy('name', 'asc')->pluck('name', 'id');
        $supplier_dp = $this->commonUtil->createDropdownHtml($supplier, 'Please Select');

        return $supplier_dp;
    }
}
