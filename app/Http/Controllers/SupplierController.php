<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Transaction;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $suppliers = Supplier::leftjoin('transactions', 'suppliers.id', 'transactions.supplier_id')
        ->select(
            'suppliers.*',
            DB::raw("SUM(IF(transactions.type = 'add_stock' AND transactions.status = 'received', final_total, 0)) as total_invoice"),
            DB::raw("SUM(IF(transactions.type = 'add_stock' AND transactions.status = 'received', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=transactions.id), 0)) as total_paid"),
            DB::raw('COUNT(CASE WHEN transactions.type="purchase_order" AND transactions.status="sent_supplier" THEN 1 END) as pending_orders'),
            DB::raw('SUM(IF(transactions.type="add_stock" AND transactions.status="received", final_total, 0)) as total_purchase')
        )->groupBy('suppliers.id')->get();

        return view('supplier.index')->with(compact(
            'suppliers'
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

        if ($quick_add) {
            return view('supplier.quick_add')->with(compact(
                'supplier_types',
                'quick_add'
            ));
        }

        return view('supplier.create')->with(compact(
            'quick_add'
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
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['company_name' => ['required', 'max:255']],
            ['email' => ['required', 'max:255']],
            ['mobile_number' => ['required', 'max:255']],
            ['address' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            $supplier = Supplier::create($data);

            if ($request->has('image')) {
                $supplier->addMedia($request->image)->toMediaCollection('supplier_photo');
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


        if ($request->quick_add) {
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

        $add_stock_query = Transaction::whereIn('transactions.type', ['add_stock'])
            ->whereIn('transactions.status', ['received']);

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



        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $status_array = $this->commonUtil->getPurchaseOrderStatusArray();

        return view('supplier.show')->with(compact(
            'add_stocks',
            'purchase_orders',
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

        return view('supplier.edit')->with(compact(
            'supplier',
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
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['company_name' => ['required', 'max:255']],
            ['email' => ['required', 'max:255']],
            ['mobile_number' => ['required', 'max:255']],
            ['address' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', '_method');

            DB::beginTransaction();
            $supplier = Supplier::find($id);
            $supplier->update($data);

            if ($request->has('image')) {
                if ($supplier->getFirstMedia('supplier_photo')) {
                    $supplier->getFirstMedia('supplier_photo')->delete();
                }
                $supplier->addMedia($request->image)->toMediaCollection('supplier_photo');
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

    public function getDetails($id){
        $supplier = Supplier::find($id);

        $is_purchase_order = request()->is_purchase_order;

        return view('supplier.details')->with(compact(
             'supplier',
             'is_purchase_order'
        ));
    }
}
