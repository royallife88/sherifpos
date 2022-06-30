<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\DeliveryZone;
use App\Models\DiningRoom;
use App\Models\Employee;
use App\Models\GiftCard;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\SalesPromotion;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\System;
use App\Models\Tax;
use App\Models\TermsAndCondition;
use App\Models\Transaction;
use App\Models\DiningTable;
use App\Models\MoneySafeTransaction;
use App\Models\ServiceFee;
use App\Models\TransactionPayment;
use App\Models\TransactionSellLine;
use App\Models\Variation;
use App\Utils\CashRegisterUtil;
use App\Utils\MoneySafeUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Str;

class SellPosController extends Controller
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
    protected $moneysafeUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, NotificationUtil $notificationUtil, CashRegisterUtil $cashRegisterUtil, MoneySafeUtil $moneysafeUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moneysafeUtil = $moneysafeUtil;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales = Transaction::where('type', 'sell')->get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('sale_pos.index')->with(compact(
            'sales',
            'payment_types'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Check if there is a open register, if no then redirect to Create Register screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->to('/cash-register/create?is_pos=1');
        }

        $categories = Category::whereNull('parent_id')->groupBy('categories.id')->get();
        $sub_categories = Category::whereNotNull('parent_id')->groupBy('categories.id')->get();
        $brands = Brand::all();
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::getDropdown();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $cashiers = Employee::getDropdownByJobType('Cashier', true, true);
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $tac = TermsAndCondition::getDropdownInvoice();
        $walk_in_customer = Customer::where('is_default', 1)->first();
        $product_classes = ProductClass::orderBy('sort', 'asc')->select('name', 'id')->get();
        $stores = Store::getDropdown();
        $store_poses = [];
        $weighing_scale_setting = System::getProperty('weighing_scale_setting') ?  json_decode(System::getProperty('weighing_scale_setting'), true) : [];
        $languages = System::getLanguageDropdown();
        $service_fees = ServiceFee::pluck('name', 'id');
        $delivery_zones = DeliveryZone::pluck('name', 'id');
        $exchange_rate_currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $employees = Employee::getCommissionEmployeeDropdown();
        $delivery_men = Employee::getDropdownByJobType('Deliveryman');

        if (empty($store_pos)) {
            $output = [
                'success' => false,
                'msg' => __('lang.kindly_assign_pos_for_that_user_to_able_to_use_it')
            ];

            return redirect()->to('/home')->with('status', $output);
        }

        return view('sale_pos.pos')->with(compact(
            'categories',
            'walk_in_customer',
            'deliverymen',
            'sub_categories',
            'tac',
            'brands',
            'store_pos',
            'customers',
            'stores',
            'store_poses',
            'cashiers',
            'taxes',
            'product_classes',
            'payment_types',
            'weighing_scale_setting',
            'languages',
            'service_fees',
            'delivery_zones',
            'employees',
            'delivery_men',
            'exchange_rate_currencies',
        ));
    }

    public function getPaymentRow()
    {
        $index = request()->index ?? 0;
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('sale_pos.partials.payment_row')->with(compact(
            'index',
            'payment_types'
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
        // try {
        $transaction_data = [
            'store_id' => $request->store_id,
            'customer_id' => $request->customer_id,
            'store_pos_id' => $request->store_pos_id,
            'exchange_rate' => !empty($request->exchange_rate) ? $request->exchange_rate : 1,
            'default_currency_id' => $request->default_currency_id,
            'received_currency_id' => $request->received_currency_id,
            'type' => 'sell',
            'final_total' => $this->commonUtil->num_uf($request->final_total),
            'grand_total' => $this->commonUtil->num_uf($request->grand_total),
            'gift_card_id' => $request->gift_card_id,
            'coupon_id' => $request->coupon_id,
            'transaction_date' => !empty($request->transaction_date) ? $request->transaction_date : Carbon::now(),
            'payment_status' => 'pending',
            'invoice_no' => $this->productUtil->getNumberByType('sell'),
            'ticket_number' => $this->transactionUtil->getTicketNumber(),
            'is_direct_sale' => !empty($request->is_direct_sale) ? 1 : 0,
            'status' => $request->status,
            'sale_note' => $request->sale_note,
            'staff_note' => $request->staff_note,
            'customer_size_id' => $request->customer_size_id_hidden ?? null,
            'fabric_name' => $request->fabric_name ?? null,
            'fabric_squatch' => $request->fabric_squatch ?? null,
            'prova_datetime' => $request->prova_datetime ?? null,
            'delivery_datetime' => $request->delivery_datetime ?? null,
            'discount_type' => $request->discount_type,
            'discount_value' => $this->commonUtil->num_uf($request->discount_value),
            'discount_amount' => $this->commonUtil->num_uf($request->discount_amount),
            'current_deposit_balance' => $this->commonUtil->num_uf($request->current_deposit_balance),
            'used_deposit_balance' => $this->commonUtil->num_uf($request->used_deposit_balance),
            'remaining_deposit_balance' => $this->commonUtil->num_uf($request->remaining_deposit_balance),
            'add_to_deposit' => $this->commonUtil->num_uf($request->add_to_deposit),
            'tax_id' => !empty($request->tax_id_hidden) ? $request->tax_id_hidden : null,
            'tax_method' => $request->tax_method ?? null,
            'tax_rate' => $request->tax_rate ?? 0,
            'total_tax' => $this->commonUtil->num_uf($request->total_tax),
            'total_item_tax' => $this->commonUtil->num_uf($request->total_item_tax),
            'sale_note' => $request->sale_note,
            'staff_note' => $request->staff_note,
            'terms_and_condition_id' => !empty($request->terms_and_condition_id) ? $request->terms_and_condition_id : null,
            'delivery_zone_id' => !empty($request->delivery_zone_id) ? $request->delivery_zone_id : null,
            'manual_delivery_zone' => !empty($request->manual_delivery_zone) ? $request->manual_delivery_zone : null,
            'deliveryman_id' => !empty($request->deliveryman_id_hidden) ? $request->deliveryman_id_hidden : null,
            'delivery_status' => 'pending',
            'delivery_cost' => $this->commonUtil->num_uf($request->delivery_cost),
            'delivery_address' => $request->delivery_address,
            'delivery_cost_paid_by_customer' => !empty($request->delivery_cost_paid_by_customer) ? 1 : 0,
            'delivery_cost_given_to_deliveryman' => !empty($request->delivery_cost_given_to_deliveryman) ? 1 : 0,
            'dining_table_id' => !empty($request->dining_table_id) ? $request->dining_table_id : null,
            'dining_room_id' => !empty($request->dining_room_id) ? $request->dining_room_id : null,
            'service_fee_id' => !empty($request->service_fee_id_hidden) ? $request->service_fee_id_hidden : null,
            'service_fee_rate' => !empty($request->service_fee_rate) ? $this->commonUtil->num_uf($request->service_fee_rate) : null,
            'service_fee_value' => !empty($request->service_fee_value) ? $this->commonUtil->num_uf($request->service_fee_value) : null,
            'commissioned_employees' => !empty($request->commissioned_employees) ? $request->commissioned_employees : [],
            'shared_commission' => !empty($request->shared_commission) ? 1 : 0,
            'created_by' => Auth::user()->id,
        ];

        $transaction_data['dining_room_id'] = null;
        if (!empty($request->dining_table_id)) {
            $dining_table = DiningTable::find($request->dining_table_id);
            $transaction_data['dining_room_id'] = $dining_table->dining_room_id;
        }
        DB::beginTransaction();

        if (!empty($request->is_quotation)) {
            $transaction_data['is_quotation'] = 1;
            $transaction_data['status'] = 'draft';
            $transaction_data['invoice_no'] = $this->productUtil->getNumberByType('quotation');
            $transaction_data['block_qty'] = !empty($request->block_qty) ? 1 : 0;
            $transaction_data['block_for_days'] = !empty($request->block_for_days) ? $request->block_for_days : 0; //reverse the block qty handle by command using cron job
            $transaction_data['validity_days'] = !empty($request->validity_days) ? $request->validity_days : 0;
        }
        $transaction = Transaction::create($transaction_data);

        $this->transactionUtil->createOrUpdateTransactionSellLine($transaction, $request->transaction_sell_line);

        foreach ($request->transaction_sell_line as $sell_line) {
            if (empty($sell_line['transaction_sell_line_id'])) {
                if ($transaction->status == 'final') {
                    $product = Product::find($sell_line['product_id']);
                    if (!$product->is_service) {
                        $this->productUtil->decreaseProductQuantity($sell_line['product_id'], $sell_line['variation_id'], $transaction->store_id, $sell_line['quantity']);
                    }
                }
            }
        }

        // if quotation and qty is blocked(reserved) for sale
        if ($transaction->is_quotation && $transaction->block_qty) {
            foreach ($request->transaction_sell_line as $sell_line) {
                $product = Product::find($sell_line['product_id']);
                if (!$product->is_service) {
                    $this->productUtil->updateBlockQuantity($sell_line['product_id'], $sell_line['variation_id'], $transaction->store_id, $sell_line['quantity'], 'add');
                }
            }
        }

        if ($transaction->status == 'final') {
            //if transaction is final then calculate the reward points
            $points_earned =  $this->transactionUtil->calculateRewardPoints($transaction);

            $transaction->rp_earned = $points_earned;
            if ($request->is_redeem_points) {
                // $transaction->rp_redeemed = $request->rp_redeemed; //logic in front end
                $transaction->rp_redeemed_value = $request->rp_redeemed_value;
                $rp_redeemed = $this->transactionUtil->calcuateRedeemPoints($transaction); //back end
                $transaction->rp_redeemed = $rp_redeemed;
            }
            $transaction->total_sp_discount = $request->total_sp_discount;
            $transaction->total_product_discount = $transaction->transaction_sell_lines->whereIn('product_discount_type', ['fixed', 'percentage'])->sum('product_discount_amount');
            $transaction->total_product_surplus = $transaction->transaction_sell_lines->whereIn('product_discount_type', ['surplus'])->sum('product_discount_amount');
            $transaction->total_coupon_discount = $transaction->transaction_sell_lines->sum('coupon_discount_amount');

            $transaction->save();

            $this->transactionUtil->updateCustomerRewardPoints($transaction->customer_id, $points_earned, 0, $request->rp_redeemed, 0);

            //update customer deposit balance if any
            $customer = Customer::find($transaction->customer_id);
            if ($request->used_deposit_balance > 0) {
                $customer->deposit_balance = $customer->deposit_balance - $request->used_deposit_balance;
            }
            if ($request->add_to_deposit > 0) {
                $customer->deposit_balance = $customer->deposit_balance + $request->add_to_deposit;
            }
            $customer->save();
        }

        if ($transaction->status != 'draft') {
            foreach ($request->payments as $payment) {

                $amount = $this->commonUtil->num_uf($payment['amount']) - $this->commonUtil->num_uf($payment['change_amount']);
                if ($amount > 0) {
                    $payment_data = [
                        'transaction_id' => $transaction->id,
                        'amount' => $amount,
                        'method' => $payment['method'],
                        'paid_on' => Carbon::now(),
                        'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                        'card_number' => !empty($payment['card_number']) ? $payment['card_number'] : null,
                        'card_security' => !empty($payment['card_security']) ? $payment['card_security'] : null,
                        'card_month' => !empty($payment['card_month']) ? $payment['card_month'] : null,
                        'card_year' => !empty($payment['card_year']) ? $payment['card_year'] : null,
                        'cheque_number' => !empty($payment['cheque_number']) ? $payment['cheque_number'] : null,
                        'bank_name' => !empty($payment['bank_name']) ? $payment['bank_name'] : null,
                        'ref_number' => !empty($payment['ref_number']) ? $payment['ref_number'] : null,
                        'gift_card_number' => $request->gift_card_number,
                        'amount_to_be_used' => $request->amount_to_be_used,
                        'payment_note' => $request->payment_note,
                        'change_amount' => $payment['change_amount'] ?? 0,
                    ];

                    $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);
                    $transaction = $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
                    $this->cashRegisterUtil->addPayments($transaction, $payment_data, 'credit', null, $transaction_payment->id);
                    if ($payment_data['method'] == 'bank_transfer' || $payment_data['method'] == 'card') {
                        $this->moneysafeUtil->addPayment($transaction, $payment_data, 'credit', $transaction_payment->id);
                    }
                }
            }


            if (!empty($transaction->coupon_id)) {
                Coupon::where('id', $transaction->coupon_id)->update(['used' => 1]);
            }

            if (!empty($transaction->gift_card_id)) {
                $remaining_balance = $this->commonUtil->num_uf($request->remaining_balance);
                $used = 0;
                if ($remaining_balance == 0) {
                    $used = 1;
                }
                GiftCard::where('id', $transaction->gift_card_id)->update(['balance' => $remaining_balance, 'used' => $used]);
            }
        }

        if (!empty($request->transaction_customer_size)) {
            $this->transactionUtil->createOrUpdateTransactionCustomerSize($transaction, $request->transaction_customer_size);
        }

        $this->transactionUtil->createOrUpdateTransactionSupplierService($transaction, $request);

        if (!empty($request->commissioned_employees)) {
            $this->transactionUtil->createOrUpdateTransactionCommissionedEmployee($transaction, $request);
        }

        if (!empty($request->uploaded_file_names)) {
            $files = explode(',', $request->uploaded_file_names);
            foreach ($files as $key => $doc) {
                $transaction->addMediaFromDisk($doc, 'temp')->toMediaCollection('sell');
            }
        }


        $this->transactionUtil->createOrUpdateRawMaterialConsumption($transaction);

        if (session('system_mode') == 'restaurant') {
            if (!empty($transaction->dining_table_id)) {
                $dining_table->current_transaction_id = $transaction->id;
                $old_status = $dining_table->status;
                if ($old_status == 'available') {
                    $dining_table->status = 'order';
                }
                $dining_table->save();
                if ($old_status == 'reserve') {
                    if (Carbon::now()->gt(Carbon::parse($dining_table->date_and_time))) {
                        $dining_table->status = 'available';
                        $dining_table->customer_name = null;
                        $dining_table->customer_mobile_number = null;
                        $dining_table->date_and_time = null;
                    }
                }


                if ($old_status != 'reserve') {
                    if ($transaction->status == 'final' && $transaction->payment_status != 'pending') {
                        $dining_table->status = 'available';
                        $dining_table->customer_name = null;
                        $dining_table->customer_mobile_number = null;
                        $dining_table->date_and_time = null;
                    }
                }
                $dining_table->save();
            }
        }


        DB::commit();

        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();


        if ($transaction->is_direct_sale) {
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];

            if ($request->action == 'send') {
                $this->notificationUtil->sendSellInvoiceToCustomer($transaction->id, $request->emails);
            }
            if ($request->action == 'print') {
                $html_content = $this->transactionUtil->getInvoicePrint($transaction, $payment_types);

                $output = [
                    'success' => true,
                    'html_content' => $html_content,
                    'msg' => __('lang.success')
                ];

                return $output;
            }

            return redirect()->back()->with('status', $output);
        }

        if (!empty($transaction->dining_table_id)) {
            $html_content = $this->transactionUtil->getInvoicePrint($transaction, $payment_types, $request->invoice_lang);

            $output = [
                'success' => true,
                'html_content' => $html_content,
                'msg' => __('lang.success')
            ];

            if ($request->dining_action_type == 'save') {
                $output = [
                    'success' => true,
                    'msg' => __('lang.success')
                ];
            }
            return $output;
        }

        if ($request->submit_type == 'send' && $transaction->is_quotation) {
            $this->notificationUtil->sendQuotationToCustomer($transaction->id, $request->emails);
        }


        $html_content = $this->transactionUtil->getInvoicePrint($transaction, $payment_types, $request->invoice_lang);


        $output = [
            'success' => true,
            'html_content' => $html_content,
            'msg' => __('lang.success')
        ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('lang.something_went_wrong')
        //     ];
        // }
        if ($request->action == 'send' && $transaction->is_direct_sale == 1) {
            return redirect()->back()->with('status', $output);
        }
        return $output;
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
        $transaction = Transaction::findOrFail($id);

        $categories = Category::whereNull('parent_id')->get();
        $sub_categories = Category::whereNotNull('parent_id')->get();
        $brands = Brand::all();
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::getDropdown();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $product_classes = ProductClass::select('name', 'id')->get();
        $cashiers = Employee::getDropdownByJobType('Cashier', true, true);
        $weighing_scale_setting = System::getProperty('weighing_scale_setting') ?  json_decode(System::getProperty('weighing_scale_setting'), true) : [];
        $stores = Store::getDropdown();
        $store_poses = [];
        $languages = System::getLanguageDropdown();
        $service_fees = ServiceFee::pluck('name', 'id');
        $delivery_zones = DeliveryZone::pluck('name', 'id');
        $exchange_rate_currencies = $this->commonUtil->getCurrenciesExchangeRateArray(true);
        $employees = Employee::getCommissionEmployeeDropdown();
        $delivery_men = Employee::getDropdownByJobType('Deliveryman');

        return view('sale_pos.edit')->with(compact(
            'transaction',
            'categories',
            'walk_in_customer',
            'deliverymen',
            'product_classes',
            'sub_categories',
            'tac',
            'brands',
            'store_pos',
            'customers',
            'cashiers',
            'taxes',
            'payment_types',
            'weighing_scale_setting',
            'stores',
            'store_poses',
            'languages',
            'service_fees',
            'employees',
            'delivery_zones',
            'delivery_men',
            'exchange_rate_currencies',
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
        // try {
        DB::beginTransaction();
        $transaction = $this->transactionUtil->updateSellTransaction($request, $id);

        if ($transaction->status == 'final') {
            //if transaction is final then calculate the reward points
            $points_earned =  $this->transactionUtil->calculateRewardPoints($transaction);
            $transaction->rp_earned = $points_earned;
            if ($request->is_redeem_points) {
                // $transaction->rp_redeemed = $request->rp_redeemed; //logic in front end
                $transaction->rp_redeemed_value = $request->rp_redeemed_value;
                $rp_redeemed = $this->transactionUtil->calcuateRedeemPoints($transaction); //back end
                $transaction->rp_redeemed = $rp_redeemed;
            }
            $transaction->total_sp_discount = $request->total_sp_discount;
            $transaction->total_product_discount = $transaction->transaction_sell_lines->whereIn('product_discount_type', ['fixed', 'percentage'])->sum('product_discount_amount');
            $transaction->total_product_surplus = $transaction->transaction_sell_lines->whereIn('product_discount_type', ['surplus'])->sum('product_discount_amount');
            $transaction->total_coupon_discount = $transaction->transaction_sell_lines->sum('coupon_discount_amount');

            $transaction->save();

            $this->transactionUtil->updateCustomerRewardPoints($transaction->customer_id, $points_earned, 0, $request->rp_redeemed, 0);

            //update customer deposit balance if any
            $customer = Customer::find($transaction->customer_id);
            if ($request->used_deposit_balance > 0) {
                $customer->deposit_balance = $customer->deposit_balance - $request->used_deposit_balance;
            }
            if ($request->add_to_deposit > 0) {
                $customer->deposit_balance = $customer->deposit_balance + $request->add_to_deposit;
            }
            $customer->save();
        }

        if ($transaction->status != 'draft') {
            if (!empty($request->payments)) {
                $payment_formated = [];
                foreach ($request->payments as $payment) {
                    $amount = $this->commonUtil->num_uf($payment['amount']) - $this->commonUtil->num_uf($payment['change_amount']);
                    $old_tp = null;
                    if (!empty($payment['transaction_payment_id'])) {
                        $old_tp = TransactionPayment::find($payment['transaction_payment_id']);
                    }
                    $payment_data = [
                        'transaction_payment_id' => !empty($payment['transaction_payment_id']) ? $payment['transaction_payment_id'] : null,
                        'transaction_id' => $transaction->id,
                        'amount' => $amount,
                        'method' => $payment['method'],
                        'paid_on' => !empty($payment['paid_on']) ? Carbon::createFromTimestamp(strtotime($payment['paid_on']))->format('Y-m-d H:i:s') : Carbon::now(),
                        'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                        'card_number' => !empty($payment['card_number']) ? $payment['card_number'] : null,
                        'card_security' => !empty($payment['card_security']) ? $payment['card_security'] : null,
                        'card_month' => !empty($payment['card_month']) ? $payment['card_month'] : null,
                        'card_year' => !empty($payment['card_year']) ? $payment['card_year'] : null,
                        'cheque_number' => !empty($payment['cheque_number']) ? $payment['cheque_number'] : null,
                        'bank_name' => !empty($payment['bank_name']) ? $payment['bank_name'] : null,
                        'ref_number' => !empty($payment['ref_number']) ? $payment['ref_number'] : null,
                        'gift_card_number' => $request->gift_card_number,
                        'amount_to_be_used' => $request->amount_to_be_used,
                        'payment_note' => $request->payment_note,
                        'change_amount' => $payment['change_amount'] ?? 0,
                        'cash_register_id' => $payment['cash_register_id'] ?? null,
                    ];
                    if ($amount > 0) {
                        $transaction_payment =  $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);
                    }
                    $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

                    if (!empty($transaction_payment)) {
                        $this->moneysafeUtil->updatePayment($transaction, $payment_data, 'credit', $transaction_payment->id, $old_tp);
                        $payment_data['transaction_payment_id'] =  $transaction_payment->id;
                        $payment_formated[] = $payment_data;
                    }
                }
                $this->cashRegisterUtil->updateSellPaymentsBasedOnPaymentDate($transaction, $payment_formated);
            }

            if ($request->payment_status == 'pending') {
                TransactionPayment::where('transaction_id', $transaction->id)->delete();
                CashRegisterTransaction::where('transaction_id', $transaction->id)->delete();
                MoneySafeTransaction::where('transaction_id', $transaction->id)->delete();
                $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
            }


            if (!empty($transaction->coupon_id)) {
                Coupon::where('id', $transaction->coupon_id)->update(['used' => 1]);
            }

            if (!empty($transaction->gift_card_id)) {
                $remaining_balance = $this->commonUtil->num_uf($request->remaining_balance);
                $used = 0;
                if ($remaining_balance == 0) {
                    $used = 1;
                }
                GiftCard::where('id', $transaction->gift_card_id)->update(['balance' => $remaining_balance, 'used' => $used]);
            }
            $transaction = $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
        }

        if (!empty($request->transaction_customer_size)) {
            $this->transactionUtil->createOrUpdateTransactionCustomerSize($transaction, $request->transaction_customer_size);
        }

        $this->transactionUtil->createOrUpdateTransactionSupplierService($transaction, $request);

        if (!empty($request->commissioned_employees)) {
            $this->transactionUtil->createOrUpdateTransactionCommissionedEmployee($transaction, $request->commissioned_employees);
        }

        if ($request->upload_documents) {
            foreach ($request->file('upload_documents', []) as $key => $doc) {
                $transaction->addMedia($doc)->toMediaCollection('transaction');
            }
        }


        if (!empty($request->dining_table_id)) {
            $dining_table = DiningTable::find($request->dining_table_id);
            $transaction_data['dining_room_id'] = $dining_table->dining_room_id;
        }

        $this->transactionUtil->createOrUpdateRawMaterialConsumption($transaction);
        if (session('system_mode') == 'restaurant') {
            if (!empty($transaction->dining_table_id)) {
                $dining_table->current_transaction_id = $transaction->id;
                $old_status = $dining_table->status;
                if ($old_status == 'available') {
                    $dining_table->status = 'order';
                }
                $dining_table->save();
                if ($old_status == 'reserve') {
                    if (Carbon::now()->gt(Carbon::parse($dining_table->date_and_time))) {
                        $dining_table->status = 'available';
                        $dining_table->current_transaction_id = null;
                        $dining_table->customer_name = null;
                        $dining_table->customer_mobile_number = null;
                        $dining_table->date_and_time = null;
                    }
                }


                if ($old_status != 'reserve') {
                    if ($transaction->status == 'final' && $transaction->payment_status != 'pending') {
                        $dining_table->status = 'available';
                        $dining_table->current_transaction_id = null;
                        $dining_table->customer_name = null;
                        $dining_table->customer_mobile_number = null;
                        $dining_table->date_and_time = null;
                    }
                }
                $dining_table->save();
            }
        }

        DB::commit();


        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $html_content = $this->transactionUtil->getInvoicePrint($transaction, $payment_types, $request->invoice_lang);

        $output = [
            'success' => true,
            'html_content' => $html_content,
            'msg' => __('lang.success')
        ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('lang.something_went_wrong')
        //     ];
        // }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * filter the products by brand or category
     *
     * @param integer $id
     * @param string $type
     * @return html
     */
    public function getProductItemsByFilter(Request $request)
    {
        $query = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id')
            ->where('products.active', 1)
            ->where('is_raw_material', 0);

        if (!empty($request->product_class_id)) {
            $query->where('product_class_id', $request->product_class_id);
        }
        if (!empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        if (!empty($request->sub_category_id)) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if (!empty($request->brand_id)) {
            $query->where('brand_id', $request->brand_id);
        }
        if (!empty($request->selling_filter)) {
            $query->leftjoin('transaction_sell_lines', 'products.id', 'transaction_sell_lines.product_id');
            if ($request->selling_filter == 'best_selling') {
                $query->select(DB::raw('SUM(transaction_sell_lines.quantity) as sold_qty'))->orderBy('sold_qty', 'desc');
            }
            if ($request->selling_filter == 'slow_moving_items') {
                $query->select(DB::raw('SUM(transaction_sell_lines.quantity) as sold_qty'))->orderBy('sold_qty', 'asc');
            }
            if ($request->selling_filter == 'product_in_last_transactions') {
                $query->orderBy('transaction_sell_lines.created_at', 'desc');
            }
        }
        if (!empty($request->price_filter)) {
            if ($request->price_filter == 'highest_price') {
                $query->orderBy('products.sell_price', 'desc');
            }
            if ($request->price_filter == 'lowest_price') {
                $query->orderBy('products.sell_price', 'asc');
            }
        }
        if (!empty($request->sorting_filter)) {
            if ($request->sorting_filter == 'a_to_z') {
                $query->orderBy('products.name', 'asc');
            }
            if ($request->sorting_filter == 'z_to_a') {
                $query->orderBy('products.name', 'desc');
            }
        }
        if (!empty($request->expiry_filter)) {
            $query->leftjoin('add_stock_lines', 'variations.id', 'add_stock_lines.variation_id');
            if ($request->expiry_filter == 'nearest_expiry') {
                $query->where(function ($q) {
                    $q->whereDate('add_stock_lines.expiry_date', '>', Carbon::now());
                })->orderBy('add_stock_lines.expiry_date', 'asc');
            }
            if ($request->expiry_filter == 'longest_expiry') {
                $query->where(function ($q) {
                    $q->whereDate('add_stock_lines.expiry_date', '>', Carbon::now());
                })->orderBy('add_stock_lines.expiry_date', 'desc');
            }
        }
        if (!empty($request->sale_promo_filter)) {
            if ($request->sale_promo_filter == 'items_in_sale_promotion') {
                $sales_promotions = SalesPromotion::whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=', date('Y-m-d'))->get();
                $sp_product_ids = [];
                foreach ($sales_promotions as $sales_promotion) {
                    $sp_product_ids = array_merge($sp_product_ids, $sales_promotion->product_ids);
                }
                $query->whereIn('products.id',  $sp_product_ids);

                if (session('system_mode') == 'restaurant') {
                    return view('sale_pos.partials.promotions')->with(compact('sales_promotions'));
                }
            }
        }
        if (!empty($request->store_id)) {
            $query->where('product_stores.store_id', $request->store_id);
        }

        $query->addSelect(
            'products.*',
            'variations.id as variation_id',
            'variations.name as variation_name',
            'variations.sub_sku',
            'product_stores.qty_available',
            'product_stores.block_qty',
        );

        if (session('system_mode') != 'restaurant') {
            $query->take(30);
        }

        $products = $query->groupBy('variations.id')->get();

        $currency_id = $request->currency_id;
        $currency = Currency::find($currency_id);
        $exchange_rate = $this->commonUtil->getExchangeRateByCurrency($currency_id, $request->store_id);

        return view('sale_pos.partials.filtered_products')->with(compact(
            'products',
            'currency',
            'exchange_rate'
        ));
    }

    /**
     * get the product items list for pos on user search term
     *
     * @return json
     */
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
                ->where('is_raw_material', 0)
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    'products.is_service',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku',
                    'product_stores.qty_available',
                    'product_stores.block_qty',
                );

            if (!empty(request()->store_id)) {
                $q->where('product_stores.store_id', request()->store_id);
            }

            $products = $q->groupBy('variation_id')->get();
            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['is_service'] = $product->is_service;
                $products_array[$product->product_id]['qty'] = $this->productUtil->num_uf($product->qty_available - $product->block_qty);
                $products_array[$product->product_id]['variations'][]
                    = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku,
                        'qty' => $product->qty_available
                    ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (!empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $v = Variation::find($variation['variation_id']);
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            if ($variation['variation_name'] != 'Default') {
                                $text = $variation['variation_name'];
                            }
                            // $text .= $v->size->name ?? '';
                        }
                        $i++;
                        $result[] = [
                            'id' => $i,
                            'text' => $text . ' - ' . $variation['sub_sku'],
                            'product_id' => $key,
                            'variation_id' => $variation['variation_id'],
                            'qty_available' => $variation['qty'],
                            'is_service' => $value['is_service']
                        ];
                    }
                    $i++;
                }
            }

            $sp_query = SalesPromotion::whereDate('start_date', '<', Carbon::now())->whereDate('end_date', '>', Carbon::now());
            $sp_query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%');
                $query->orWhere('code', 'like', '%' . $term . '%');
            });
            $sp_query->where('generate_barcode', 1);

            $sales_promotions = $sp_query->get();

            foreach ($sales_promotions as $sales_promotion) {
                $result[] = [
                    'id' => $i,
                    'text' => $sales_promotion->name . ' - ' . $sales_promotion->code,
                    'sale_promotion_id' => $sales_promotion->id,
                    'variation_id' => null,
                    'qty_available' => null,
                    'is_sale_promotion' => 1
                ];
                $i++;
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
            $weighing_scale_barcode = $request->input('weighing_scale_barcode');


            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $store_id = $request->input('store_id');
            $customer_id = $request->input('customer_id');
            $currency_id = $request->input('currency_id');
            $dining_table_id = $request->input('dining_table_id');
            $is_direct_sale = $request->input('is_direct_sale');
            $edit_quantity = !empty($request->input('edit_quantity')) ? $request->input('edit_quantity') : 1;
            $added_products = json_decode($request->input('added_products'), true);

            $currency_id = $request->currency_id;
            $currency = Currency::find($currency_id);
            $exchange_rate = $this->commonUtil->getExchangeRateByCurrency($currency_id, $request->store_id);

            //Check for weighing scale barcode
            $weighing_barcode = request()->get('weighing_scale_barcode');
            if (empty($variation_id) && !empty($weighing_barcode)) {
                $product_details = $this->__parseWeighingBarcode($weighing_barcode);
                if ($product_details['success']) {
                    $product_id = $product_details['product_id'];
                    $variation_id = $product_details['variation_id'];
                    $quantity = $product_details['qty'];
                    $edit_quantity = $quantity;
                } else {
                    $output['success'] = false;
                    $output['msg'] = $product_details['msg'];
                    return $output;
                }
            }

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProductByStore($product_id, $variation_id, $store_id);

                $product_discount_details = $this->productUtil->getProductDiscountDetails($product_id, $customer_id);
                // $sale_promotion_details = $this->productUtil->getSalesPromotionDetail($product_id, $store_id, $customer_id, $added_products);
                $sale_promotion_details = null; //changed, now in pos.js check_for_sale_promotion method
                $html_content =  view('sale_pos.partials.product_row')
                    ->with(compact('products', 'index', 'sale_promotion_details', 'product_discount_details', 'edit_quantity', 'is_direct_sale', 'dining_table_id', 'exchange_rate'))->render();

                $output['success'] = true;
                $output['html_content'] = $html_content;
            } else {
                $output['success'] = false;
                $output['msg'] = __('lang.sku_no_match');
            }
            return  $output;
        }
    }

    /**
     * get the row for non identifiable products
     *
     * @param Request $request
     * @return void
     */
    public function getNonIdentifiableItemRow(Request $request)
    {
        $name = !empty($request->name) ? $request->name : 'Non-Identifiable Item';
        $sell_price = $request->sell_price;
        $purchase_price = $request->purchase_price;
        $quantity = $request->quantity;
        $index = $request->row_count;
        $store_id = $request->store_id;
        $customer_id = $request->customer_id;
        $dining_table_id = $request->dining_table_id;

        $currency_id = $request->currency_id;
        $currency = Currency::find($currency_id);
        $exchange_rate = $this->commonUtil->getExchangeRateByCurrency($currency_id, $request->store_id);

        $product_details = $this->productUtil->getNonIdentifiableProductDetails($name, $sell_price, $purchase_price, $request);
        if (!empty($product_details)) {
            $product_id = $product_details->product_id;
            $variation_id = $product_details->variation_id;
            $quantity = $quantity;
            $edit_quantity = $quantity;
        } else {
            $output['success'] = false;
            $output['msg'] = $product_details['msg'];
            return $output;
        }

        if (!empty($product_id)) {
            $index = $request->input('row_count');
            $products = $this->productUtil->getDetailsFromProductByStore($product_id, $variation_id, $store_id);

            $product_discount_details = $this->productUtil->getProductDiscountDetails($product_id, $customer_id);
            // $sale_promotion_details = $this->productUtil->getSalesPromotionDetail($product_id, $store_id, $customer_id, $added_products);
            $sale_promotion_details = null; //changed, now in pos.js check_for_sale_promotion method
            $html_content =  view('sale_pos.partials.product_row')
                ->with(compact('products', 'index', 'sale_promotion_details', 'product_discount_details', 'edit_quantity', 'dining_table_id', 'exchange_rate'))->render();

            $output['success'] = true;
            $output['html_content'] = $html_content;
        } else {
            $output['success'] = false;
            $output['msg'] = __('lang.sku_no_match');
        }
        return  $output;
    }

    /**
     * Parse the weighing barcode.
     *
     * @return array
     */
    private function __parseWeighingBarcode($scale_barcode)
    {
        $scale_setting = System::getProperty('weighing_scale_setting') ? json_decode(System::getProperty('weighing_scale_setting'), true) : [];

        $error_msg = trans("lang.something_went_wrong");

        //Check for prefix.
        if ((strlen($scale_setting['label_prefix']) == 0) || Str::startsWith($scale_barcode, $scale_setting['label_prefix'])) {
            $scale_barcode = substr($scale_barcode, strlen($scale_setting['label_prefix']));
            //Get product sku, trim left side 0
            $sku = substr($scale_barcode, 0, $scale_setting['product_sku_length'] + 1);

            $last_digits_type = $scale_setting['last_digits_type'];
            $qty = 0;

            //Get quantity integer
            $integer_part = substr($scale_barcode, $scale_setting['product_sku_length'] + 1, $scale_setting['qty_length'] + 1);

            //Get quantity decimal
            $decimal_part = '0.' . substr($scale_barcode, $scale_setting['product_sku_length'] + $scale_setting['qty_length'] + 2, $scale_setting['qty_length_decimal'] + 1);
            //Find the variation id
            $result = $this->productUtil->filterProduct($sku, ['sub_sku'], 'like')->first();

            if ($last_digits_type == 'quantity') {
                $qty = (float)$integer_part + (float)$decimal_part;
            }
            if ($last_digits_type == 'price') {
                $price = (float)$integer_part + (float)$decimal_part;
                $sell_price = $result->default_sell_price;
                $qty = $price / $sell_price;
            }


            if (!empty($result)) {
                return [
                    'product_id' => $result->product_id,
                    'variation_id' => $result->variation_id,
                    'qty' => $qty,
                    'success' => true
                ];
            } else {
                $error_msg = trans("lang.sku_not_match", ['sku' => $sku]);
            }
        } else {
            $error_msg = trans("lang.prefix_did_not_match");
        }

        return [
            'success' => false,
            'msg' => $error_msg
        ];
    }
    /**
     * Returns the html for product row
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalePromotionDetailsIfValid(Request $request)
    {
        $result = ['valid' => false, 'sale_promotion_details' => null];
        if ($request->ajax()) {
            $store_id = $request->input('store_id');
            $customer_id = $request->input('customer_id');
            $added_products = json_decode($request->input('added_products'), true);
            $added_qty = json_decode($request->input('added_qty'), true);
            $qty_array = [];
            foreach ($added_qty as $value) {
                $qty_array[$value['product_id']] = $value['qty'];
            }

            $sale_promotion_details = $this->productUtil->getSalePromotionDetailsIfValidForThisSale($store_id, $customer_id, $added_products, $qty_array);
            if (!empty($sale_promotion_details)) {
                $result = ['valid' => true, 'sale_promotion_details' => $sale_promotion_details];
            }
        }

        return $result;
    }

    /**
     * list of recent transactions
     *
     * @return void
     */
    public function getRecentTransactions(Request $request)
    {
        if (request()->ajax()) {
            $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
            $default_currency_id = System::getProperty('currency');

            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];
            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->leftjoin('users', 'transactions.created_by', 'users.id')
                ->leftjoin('currencies as received_currency', 'transactions.received_currency_id', 'received_currency.id')
                ->where('type', 'sell')->where('status', '!=', 'draft');

            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
            }
            if (!empty(request()->start_date)) {
                $query->whereDate('transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->whereDate('transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->customer_id)) {
                $query->where('customer_id', request()->customer_id);
            }
            if (!empty(request()->deliveryman_id)) {
                $query->where('transactions.deliveryman_id', request()->deliveryman_id);
            }
            if (!empty(request()->created_by)) {
                $query->where('transactions.created_by', request()->created_by);
            }
            if (!empty(request()->method)) {
                $query->where('transaction_payments.method', request()->method);
            }
            if (!empty($pos_id)) {
                $query->where('store_pos_id', $pos_id);
            }
            if (!session('user.is_superadmin')) {
                $stores = Store::getDropdown();
                $stores_ids = array_keys($stores);
                $query->whereIn('transactions.store_id', $stores_ids);
            }

            $transactions = $query->select(
                'transactions.final_total',
                'transactions.payment_status',
                'transactions.status',
                'transactions.id',
                'transactions.transaction_date',
                'transactions.service_fee_value',
                'transactions.invoice_no',
                'transactions.deliveryman_id',
                'transaction_payments.paid_on',
                'users.name as created_by_name',
                'customers.name as customer_name',
                'customer_types.name as customer_type_name',
                'customers.mobile_number',
                'received_currency.symbol as received_currency_symbol',
                'received_currency_id'
            )->with([
                'return_parent',
                'customer',
                'transaction_payments',
                'deliveryman',
                'canceled_by_user',
            ])
                ->groupBy('transactions.id');

            return DataTables::of($transactions)
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('invoice_no', function ($row) {
                    $string = $row->invoice_no . ' ';
                    if (!empty($row->return_parent)) {
                        $string .= '<a
                        data-href="' . action('SellReturnController@show', $row->id) . '" data-container=".view_modal"
                        class="btn btn-modal" style="color: #007bff;">R</a>';
                    }

                    return $string;
                })
                ->editColumn('final_total', function ($row) use ($default_currency_id) {
                    if (!empty($row->return_parent)) {
                        $final_total = $this->commonUtil->num_f($row->final_total - $row->return_parent->final_total);
                    } else {
                        $final_total = $this->commonUtil->num_f($row->final_total);
                    }

                    $received_currency_id = $row->received_currency_id ?? $default_currency_id;
                    return '<span data-currency_id="' . $received_currency_id . '">' . $final_total . '</span>';
                })
                ->editColumn('received_currency_symbol', function ($row) use ($default_currency_id) {
                    $default_currency = Currency::find($default_currency_id);
                    return $row->received_currency_symbol ?? $default_currency->symbol;
                })
                ->addColumn('method', function ($row) use ($payment_types, $request) {
                    $methods = '';
                    if (!empty($request->method)) {
                        $payments = $row->transaction_payments->where('method', $request->method);
                    } else {
                        $payments = $row->transaction_payments;
                    }
                    foreach ($payments as $payment) {
                        if (!empty($payment->method)) {
                            $methods .= $payment_types[$payment->method] . '<br>';
                        }
                    }
                    return $methods;
                })
                ->addColumn('ref_number', function ($row) {
                    if (!empty($row->transaction_payments[0]->ref_number)) {
                        return $row->transaction_payments[0]->ref_number;
                    } else {
                        return '';
                    }
                })
                ->addColumn('deliveryman_name', function ($row) {
                    if (!empty($row->deliveryman)) {
                        return $row->deliveryman->employee_name;
                    } else {
                        return '';
                    }
                })
                ->editColumn('payment_status', function ($row) {
                    if ($row->payment_status == 'pending') {
                        return '<span class="badge badge-warning">' . __('lang.pay_later') . '</span>';
                    } else if ($row->payment_status == 'partial') {
                        return '<span class="badge badge-danger">Partial</span>';
                    } else {
                        return '<span class="badge badge-success">' . ucfirst($row->payment_status) . '</span>';
                    }
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'canceled') {
                        return '<span class="badge badge-danger">' . __('lang.cancel') . '</span>';
                    } else {
                        return '<span class="badge badge-success">' . ucfirst($row->status) . '</span>';
                    }
                })
                ->addColumn('paid', function ($row) use ($request) {
                    $amount_paid = 0;
                    if (!empty($request->method)) {
                        $payments = $row->transaction_payments->where('method', $request->method);
                    } else {
                        $payments = $row->transaction_payments;
                    }
                    foreach ($payments as $payment) {
                        $amount_paid += $payment->amount;
                    }
                    return $this->commonUtil->num_uf($amount_paid);
                })
                ->editColumn('created_by', '{{$created_by_name}}')
                ->editColumn('canceled_by', function ($row) {
                    return !empty($row->canceled_by_user) ? $row->canceled_by_user->name : '';
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">';

                        if (auth()->user()->can('sale.pos.view')) {
                            $html .=
                                ' <a data-href="' . action('SellController@print', $row->id) . '"
                            class="btn btn-danger text-white print-invoice"><i title="' . __('lang.print') . '"
                                data-toggle="tooltip" class="dripicons-print"></i></a>';
                        }
                        if (auth()->user()->can('sale.pos.view')) {
                            $html .=
                                '<a data-href="' . action('SellController@show', $row->id) . '"
                            class="btn btn-primary text-white  btn-modal" data-container=".view_modal"><i
                                title="' . __('lang.view') . '" data-toggle="tooltip" class="fa fa-eye"></i></a>';
                        }
                        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
                            $html .=
                                '<a target="_blank" href="' . action('SellController@edit', $row->id) . '" class="btn btn-success"><i
                            title="' . __('lang.edit') . '" data-toggle="tooltip"
                            class="dripicons-document-edit"></i></a>';
                        }
                        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
                            $html .=
                                '<a data-href="' . action('SellController@destroy', $row->id) . '"
                            title="' . __('lang.delete') . '" data-toggle="tooltip"
                            data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                            class="btn btn-danger delete_item" style="color: white"><i class="fa fa-trash"></i></a>';
                        }
                        if (auth()->user()->can('return.sell_return.create_and_edit')) {
                            $html .=
                                '  <a href="' . action('SellReturnController@add', $row->id) . '"
                                title="' . __('lang.sell_return') . '" data-toggle="tooltip" class="btn btn-secondary"
                                style="color: white"><i class="fa fa-undo"></i></a>';
                        }
                        if (auth()->user()->can('sale.pay.create_and_edit')) {
                            if ($row->status != 'draft' && $row->payment_status != 'paid' && $row->status != 'canceled') {
                                $html .=
                                    '<a data-href="' . action('TransactionPaymentController@addPayment', ['id' => $row->id]) . '"
                                title="' . __('lang.pay_now') . '" data-toggle="tooltip" data-container=".view_modal"
                                class="btn btn-modal btn-success" style="color: white"><i class="fa fa-money"></i></a>';
                            }
                        }
                        $html .= '</div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
                    'transaction_date',
                    'paid',
                    'method',
                    'invoice_no',
                    'final_total',
                    'status',
                    'payment_status',
                    'created_by',
                ])
                ->make(true);
        }
    }

    /**
     * list of draft transactions
     *
     * @return void
     */
    public function getDraftTransactions(Request $request)
    {
        if (request()->ajax()) {
            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->where('type', 'sell')->whereIn('status', ['draft', 'canceled'])->whereNull('transactions.dining_table_id')->whereNull('transactions.restaurant_order_id');

            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
            }
            if (!empty(request()->start_date)) {
                $query->whereDate('transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->whereDate('transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->deliveryman_id)) {
                $query->where('transactions.deliveryman_id', request()->deliveryman_id);
            }

            $transactions = $query->select(
                'transactions.*',
                'customer_types.name as customer_type_name',
                'customers.name as customer_name',
                'customers.mobile_number',
            )->with(['deliveryman']);

            return DataTables::of($transactions)
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('final_total', '{{@num_format($final_total)}}')
                ->addColumn('customer_type', function ($row) {
                    if (!empty($row->customer->customer_type)) {
                        return $row->customer->customer_type->name;
                    } else {
                        return '';
                    }
                })
                ->editColumn('customer_name', '<span class="text-red">{{$customer_name}}</span>')
                ->addColumn('method', function ($row) {
                    if (!empty($row->transaction_payments[0]->method)) {
                        return ucfirst($row->transaction_payments[0]->method);
                    } else {
                        return '';
                    }
                })

                ->addColumn('deliveryman_name', function ($row) {
                    if (!empty($row->deliveryman)) {
                        return $row->deliveryman->employee_name;
                    } else {
                        return '';
                    }
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'canceled') {
                        return '<span class="badge badge-danger">' . __('lang.cancel') . '</span>';
                    } else {
                        return '<span class="badge badge-primary">' . ucfirst($row->status) . '</span>';
                    }
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">';

                        if (auth()->user()->can('sale.pos.view')) {
                            $html .=
                                ' <a data-href="' . action('SellController@print', $row->id) . '"
                        class="btn btn-danger text-white print-invoice"><i title="' . __('lang.print') . '"
                        data-toggle="tooltip" class="dripicons-print"></i></a>';
                        }
                        if (auth()->user()->can('sale.pos.view')) {
                            $html .=
                                '<a data-href="' . action('SellController@show', $row->id) . '"
                                class="btn btn-primary text-white  btn-modal" data-container=".view_modal"><i
                                title="' . __('lang.view') . '" data-toggle="tooltip" class="fa fa-eye"></i></a>';
                        }
                        $html .=
                            '<a  target="_blank" href="' . action('SellPosController@edit', $row->id) . '?status=final" class="btn btn-success draft_pay"><i
                        title="' . __('lang.edit') . '" data-toggle="tooltip"
                        class="dripicons-document-edit"></i></a>';
                        if ($row->status != 'canceled') {
                            $html .=
                                '<a data-href="' . action('SellPosController@updateStatusToCancel', $row->id) . '?status=final" data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '" class="btn btn-danger draft_cancel text-white"><i
                            title="' . __('lang.cancel') . '" data-toggle="tooltip"
                            class="fa fa-ban"></i></a>';
                        }
                        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
                            $html .=
                                '<button class="btn btn-danger remove_draft" data-href=' . action(
                                    'SellController@destroy',
                                    $row->id
                                ) . '
                                data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                title="' . __('lang.delete') . '" data-toggle="tooltip"
                                ><i class="dripicons-trash"></i></button>';
                        }


                        $html .=
                            '<a target="_blank" href="' . action('SellPosController@edit', $row->id) . '?status=final"
                            title="' . __('lang.pay_now') . '" data-toggle="tooltip"
                            class="btn btn-success draft_pay"><i class="fa fa-money"></i></a>';

                        $html .= '</div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
                    'customer_name',
                    'transaction_date',
                    'final_total',
                    'status',
                    'created_by',
                ])
                ->make(true);
        }
    }

    /**
     * list of draft transactions
     *
     * @return void
     */
    public function getOnlineOrderTransactions(Request $request)
    {
        if (request()->ajax()) {
            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->where('type', 'sell')->where('status', 'draft')
                ->whereNotNull('transactions.restaurant_order_id');
            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
            }
            if (!empty(request()->start_date)) {
                $query->whereDate('transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->whereDate('transaction_date', '<=', request()->end_date);
            }

            $transactions = $query->select(
                'transactions.*',
                'customer_types.name as customer_type_name',
                'customers.name as customer_name',
                'customers.mobile_number',
            );

            return DataTables::of($transactions)
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('final_total', '{{@num_format($final_total)}}')
                ->addColumn('customer_type', function ($row) {
                    if (!empty($row->customer->customer_type)) {
                        return $row->customer->customer_type->name;
                    } else {
                        return '';
                    }
                })
                ->editColumn('customer_name', '<span class="text-red">{{$customer_name}}</span>')
                ->addColumn('method', function ($row) {
                    if (!empty($row->transaction_payments[0]->method)) {
                        return ucfirst($row->transaction_payments[0]->method);
                    } else {
                        return '';
                    }
                })

                ->addColumn('deliveryman_name', function ($row) {
                    if (!empty($row->deliveryman)) {
                        return $row->deliveryman->employee_name;
                    } else {
                        return '';
                    }
                })
                ->editColumn('status', function ($row) {
                    return '<span class="label label-danger">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">';

                        if (auth()->user()->can('sale.pos.view')) {
                            $html .=
                                ' <a data-href="' . action('SellController@print', $row->id) . '"
                        class="btn btn-danger text-white print-invoice"><i title="' . __('lang.print') . '"
                        data-toggle="tooltip" class="dripicons-print"></i></a>';
                        }
                        if (auth()->user()->can('sale.pos.view')) {
                            $html .=
                                '<a data-href="' . action('SellController@show', $row->id) . '"
                                class="btn btn-primary text-white  btn-modal" data-container=".view_modal"><i
                                title="' . __('lang.view') . '" data-toggle="tooltip" class="fa fa-eye"></i></a>';
                        }
                        $html .=
                            '<a  target="_blank" href="' . action('SellPosController@edit', $row->id) . '?status=final" class="btn btn-success draft_pay"><i
                            title="' . __('lang.edit') . '" data-toggle="tooltip"
                            class="dripicons-document-edit"></i></a>';

                        $html .=
                            '<button class="btn btn-danger remove_online_order" data-href=' . action(
                                'SellController@destroy',
                                $row->id
                            ) . '
                            data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                            ><i class="dripicons-trash"></i></button>';


                        $html .=
                            '<a target="_blank" href="' . action('SellPosController@edit', $row->id) . '?status=final"
                            title="' . __('lang.pay_now') . '" data-toggle="tooltip"
                            class="btn btn-success draft_pay"><i class="fa fa-money"></i></a>';

                        $html .= '</div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
                    'customer_name',
                    'transaction_date',
                    'final_total',
                    'status',
                    'created_by',
                ])
                ->make(true);
        }
    }

    /**
     * get the customer details
     *
     * @param int $id
     * @return void
     */
    public function getCustomerDetails($customer_id)
    {
        $customer = Customer::find($customer_id);
        $store_id = request()->store_id;
        $product_array = request()->product_array;

        $rp_value = $this->transactionUtil->calculateTotalRewardPointsValue($customer_id, $store_id);

        $total_redeemable = 0;

        if (!empty($product_array)) {
            $total_redeemable = $this->transactionUtil->calculateRedeemablePointValue($customer_id, $product_array, $store_id);
        }

        $customer_type = CustomerType::find($customer->customer_type_id);

        return ['customer' => $customer, 'rp_value' => $rp_value, 'total_redeemable'  => $total_redeemable, 'customer_type_name' => !empty($customer_type) ? $customer_type->name : ''];
    }
    /**
     * get the customer balance
     *
     * @param int $id
     * @return void
     */
    public function getCustomerBalance($customer_id)
    {
        $balance = $this->transactionUtil->getCustomerBalance($customer_id)['balance'];

        return ['balance' => $balance];
    }

    /**
     * get the transction details
     *
     * @param int $id
     * @return void
     */
    public function getTransactionDetails($transaction_id)
    {
        return Transaction::find($transaction_id);
    }
    /**
     * update transaction status as cancel
     *
     * @param int $id
     * @return void
     */
    public function updateTransactionStatusCancel($transaction_id)
    {
        try {
            $transaction = Transaction::find($transaction_id);
            $transaction->status = 'canceled';
            $transaction->canceled_by = Auth::user()->id;
            $transaction->save();
            $dining_table = DiningTable::find($transaction->dining_table_id);
            $dining_table->status = 'available';
            $dining_table->customer_name = null;
            $dining_table->customer_mobile_number = null;
            $dining_table->date_and_time = null;
            $dining_table->current_transaction_id = null;
            $dining_table->save();


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

    public function updateStatusToCancel($id)
    {
        try {
            $transaction = Transaction::find($id);
            $transaction->status = 'canceled';
            $transaction->canceled_by = Auth::user()->id;
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
        return $output;
    }
}
