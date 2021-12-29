<?php

namespace App\Http\Controllers;

use App\Imports\TransactionSellLineImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\System;
use App\Models\Tax;
use App\Models\TermsAndCondition;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SellController extends Controller
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
    public function index(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'sell')->where('status', 'final');

        if (!empty(request()->customer_id)) {
            $query->where('customer_id', request()->customer_id);
        }
        if (!empty(request()->status)) {
            $query->where('status', request()->status);
        }
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }
        if (!empty(request()->payment_status)) {
            $query->where('payment_status', request()->payment_status);
        }
        if (!empty(request()->created_by)) {
            $query->where('created_by', request()->created_by);
        }
        if (!empty(request()->method)) {
            $query->where('transaction_payments.method', request()->method);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('transaction_date', '<=', request()->end_date);
        }

        $sales = $query->select('transactions.*')
            ->groupBy('transactions.id')
            ->orderBy('created_at', 'desc')
            ->get();


        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $customers = Customer::getCustomerArrayWithMobile();
        $stores = Store::getDropdown();
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $cashiers = Employee::getDropdownByJobType('Cashier');

        return view('sale.index')->with(compact(
            'sales',
            'payment_types',
            'cashiers',
            'customers',
            'stores',
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
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::getDropdown();
        $store_poses = [];

        return view('sale.create')->with(compact(
            'walk_in_customer',
            'deliverymen',
            'store_pos',
            'customers',
            'tac',
            'taxes',
            'payment_types',
            'stores',
            'store_poses',
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
        //
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

        return view('sale.show')->with(compact(
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
        $sale = Transaction::findOrFail($id);
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');

        return view('sale.edit')->with(compact(
            'sale',
            'walk_in_customer',
            'deliverymen',
            'store_pos',
            'tac',
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
            $this->transactionUtil->updateSellTransaction($request, $id);

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

            $transaction_sell_lines = TransactionSellLine::where('transaction_id', $id)->get();
            foreach ($transaction_sell_lines as $transaction_sell_line) {
                if ($transaction->status == 'final') {
                    $this->productUtil->updateProductQuantityStore($transaction_sell_line->product_id, $transaction_sell_line->variation_id, $transaction->store_id, $transaction_sell_line->quantity);
                }
                $transaction_sell_line->delete();
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

            $invoice_lang = System::getProperty('invoice_lang');
            if (empty($invoice_lang)) {
                $invoice_lang = request()->session()->get('language');
            }

            if ($invoice_lang == 'ar_and_en') {
                $html_content = view('sale_pos.partials.invoice_ar_and_end')->with(compact(
                    'transaction',
                    'payment_types'
                ))->render();
            } else {
                $html_content = view('sale_pos.partials.invoice')->with(compact(
                    'transaction',
                    'payment_types',
                    'invoice_lang'
                ))->render();
            }

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
     * Display a listing of the resource for delivery.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDeliveryList()
    {
        $query = Transaction::where('type', 'sell')->whereNotNull('deliveryman_id');

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

        return view('sale.delivery_list')->with(compact(
            'sales',
            'payment_types',
            'customers',
            'payment_status_array',
        ));
    }

    public function getImport()
    {
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');

        return view('sale.import')->with(compact(
            'walk_in_customer',
            'deliverymen',
            'store_pos',
            'customers',
            'tac',
            'taxes',
            'payment_types',
            'payment_status_array'
        ));
    }

    public function saveImport(Request $request)
    {
        try {
            $transaction_data = [
                'store_id' => $request->store_id,
                'customer_id' => $request->customer_id,
                'store_pos_id' => $request->store_pos_id,
                'type' => 'sell',
                'final_total' => $this->commonUtil->num_uf($request->final_total),
                'grand_total' => $this->commonUtil->num_uf($request->grand_total),
                'gift_card_id' => $request->gift_card_id,
                'coupon_id' => $request->coupon_id,
                'transaction_date' => Carbon::now(),
                'invoice_no' => $this->productUtil->getNumberByType('sell'),
                'is_direct_sale' => !empty($request->is_direct_sale) ? 1 : 0,
                'status' => $request->status,
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'discount_type' => $request->discount_type,
                'discount_value' => $this->commonUtil->num_f($request->discount_value),
                'discount_amount' => $this->commonUtil->num_f($request->discount_amount),
                'tax_id' => !empty($request->tax_id_hidden) ? $request->tax_id_hidden : null,
                'total_tax' => $this->commonUtil->num_f($request->total_tax),
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'terms_and_condition_id' => !empty($request->terms_and_condition_id) ? $request->terms_and_condition_id : null,
                'deliveryman_id' => !empty($request->deliveryman_id_hidden) ? $request->deliveryman_id_hidden : null,
                'payment_status' => 'pending',
                'delivery_status' => 'pending',
                'delivery_cost' => $this->commonUtil->num_f($request->delivery_cost),
                'delivery_address' => $request->delivery_address,
                'delivery_cost_paid_by_customer' => !empty($request->delivery_cost_paid_by_customer) ? 1 : 0,
                'created_by' => Auth::user()->id,
            ];

            DB::beginTransaction();

            if (!empty($request->is_quotation)) {
                $transaction_data['is_quotation'] = 1;
                $transaction_data['status'] = 'draft';
                $transaction_data['invoice_no'] = $this->productUtil->getNumberByType('quotation');
                $transaction_data['block_qty'] = !empty($request->block_qty) ? 1 : 0;
                $transaction_data['block_for_days'] = !empty($request->block_for_days) ? $request->block_for_days : 0;
                $transaction_data['validity_days'] = !empty($request->validity_days) ? $request->validity_days : 0;
            }
            $transaction = Transaction::create($transaction_data);

            Excel::import(new TransactionSellLineImport($transaction->id), $request->file);

            foreach ($transaction->transaction_sell_lines as $sell_line) {
                if (empty($sell_line['transaction_sell_line_id'])) {
                    if ($transaction->status == 'final') {
                        $this->productUtil->decreaseProductQuantity($sell_line['product_id'], $sell_line['variation_id'], $transaction->store_id, $sell_line['quantity']);
                    }
                }
            }

            $final_total = TransactionSellLine::where('transaction_id', $transaction->id)->sum('sub_total');
            $transaction->discount_amount = $this->transactionUtil->calculateDiscountAmount($final_total, $request->discount_type, $request->discount_value);
            $transaction->total_tax = $this->transactionUtil->calculateTaxAmount($final_total, $request->tax_id);
            $transaction->grand_total = $final_total;
            $transaction->final_total = $final_total - $transaction->discount_amount + $transaction->total_tax;
            $transaction->save();


            if ($transaction->status == 'final') {
                //if transaction is final then calculate the reward points
                $points_earned =  $this->transactionUtil->calculateRewardPoints($transaction);
                $transaction->rp_earned = $points_earned;
                if ($request->is_redeem_points) {
                    $transaction->rp_redeemed_value = $request->rp_redeemed_value;
                    $rp_redeemed = $this->transactionUtil->calcuateRedeemPoints($transaction); //back end
                    $transaction->rp_redeemed = $rp_redeemed;
                }

                $transaction->save();

                $this->transactionUtil->updateCustomerRewardPoints($transaction->customer_id, $points_earned, 0, $request->rp_redeemed, 0);
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
