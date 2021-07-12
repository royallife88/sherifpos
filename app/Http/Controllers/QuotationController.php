<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
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
        $query = Transaction::where('type', 'sell')->where('is_quotation', 1)->where('status', '!=', 'final');

        if (!empty(request()->customer_id)) {
            $query->where('customer_id', request()->customer_id);
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

        $sales = $query->orderBy('invoice_no', 'desc')->get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $customers = Customer::getCustomerArrayWithMobile();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('quotation.index')->with(compact(
            'sales',
            'payment_types',
            'customers',
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

        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $stores = Store::pluck('name', 'id');

        return view('quotation.create')->with(compact(
            'walk_in_customer',
            'stores',
            'store_pos',
            'customers',
            'taxes',
            'payment_types',
            'payment_status_array'
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
        // quotation store logic use SellPosController@store
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sale = Transaction::find($id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('quotation.show')->with(compact(
            'sale',
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
        $sale = Transaction::find($id);
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $stores = Store::pluck('name', 'id');

        return view('quotation.edit')->with(compact(
            'stores',
            'sale',
            'store_pos',
            'customers',
            'taxes',
            'payment_types',
            'payment_status_array'
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

            $transaction_data = [
                'customer_id' => $request->customer_id,
                'final_total' => $this->commonUtil->num_uf($request->final_total),
                'grand_total' => $this->commonUtil->num_uf($request->grand_total),
                'is_direct_sale' => !empty($request->is_direct_sale) ? 1 : 0,
                'status' => $request->status,
                'discount_type' => $request->discount_type,
                'discount_value' => $this->commonUtil->num_f($request->discount_value),
                'discount_amount' => $this->commonUtil->num_f($request->discount_amount),
                'tax_id' => $request->tax_id,
                'is_quotation' => 1,
                'block_qty' => !empty($request->block_qty) ? 1 : 0,
                'block_for_days' => !empty($request->block_for_days) ? $request->block_for_days : 0,
                'validity_days' => !empty($request->validity_days) ? $request->validity_days : 0,
                'total_tax' => $this->commonUtil->num_f($request->total_tax),
            ];

            DB::beginTransaction();

            $transaction = Transaction::find($id);
            $transaction->update($transaction_data);

            $keep_sell_lines = [];
            foreach ($request->transaction_sell_line as $line) {
                if (!empty($transaction_sell_line['transaction_sell_line_id'])) {
                    $transaction_sell_line = TransactionSellLine::find($line['transaction_sell_line_id']);
                    $transaction_sell_line->product_id = $line['product_id'];
                    $transaction_sell_line->variation_id = $line['variation_id'];
                    $transaction_sell_line->quantity = $this->commonUtil->num_uf($line['quantity']);
                    $transaction_sell_line->sell_price = $this->commonUtil->num_uf($line['sell_price']);
                    $transaction_sell_line->sub_total = $this->commonUtil->num_uf($line['sub_total']);
                    $transaction_sell_line->save();
                    $keep_sell_lines[] = $line['transaction_sell_line_id'];
                } else {
                    $transaction_sell_line = new TransactionSellLine();
                    $transaction_sell_line->transaction_id = $transaction->id;
                    $transaction_sell_line->product_id = $line['product_id'];
                    $transaction_sell_line->variation_id = $line['variation_id'];
                    $transaction_sell_line->quantity = $this->commonUtil->num_uf($line['quantity']);
                    $transaction_sell_line->sell_price = $this->commonUtil->num_uf($line['sell_price']);
                    $transaction_sell_line->sub_total = $this->commonUtil->num_uf($line['sub_total']);
                    $transaction_sell_line->save();
                    $keep_sell_lines[] = $transaction_sell_line->id;
                }
            }

            //deleted lines
            TransactionSellLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_sell_lines)->delete();

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
            TransactionSellLine::where('transaction_id', $id)->delete();
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
     * print the transaction
     *
     * @param int $id
     * @return html
     */
    public function print($id)
    {
        try {
            $transaction = Transaction::find($id);

            $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

            $html_content = view('sale_pos.partials.invoice')->with(compact(
                'transaction',
                'payment_types'
            ))->render();

            $output = [
                'success' => true,
                'html_content' => $html_content,
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewAllInvoices()
    {
        $query = Transaction::where('type', 'sell')->where('is_quotation', 1)->where('status', 'final');

        if (!empty(request()->customer_id)) {
            $query->where('customer_id', request()->customer_id);
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

        $sales = $query->orderBy('invoice_no', 'desc')->get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $customers = Customer::getCustomerArrayWithMobile();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('quotation.view_all_invoices')->with(compact(
            'sales',
            'payment_types',
            'customers',
            'payment_status_array',
        ));
    }
}