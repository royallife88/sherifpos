<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerType;
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
            return redirect()->to('/cash-rgister/create?is_pos=1');
        }

        $categories = Category::whereNull('parent_id')->groupBy('categories.id')->get();
        $sub_categories = Category::whereNotNull('parent_id')->groupBy('categories.id')->get();
        $brands = Brand::all();
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $cashiers = Employee::getDropdownByJobType('Cashier', true, true);
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $tac = TermsAndCondition::getDropdownInvoice();
        $walk_in_customer = Customer::where('is_default', 1)->first();
        $stores = Store::getDropdown();
        $product_classes = ProductClass::select('name', 'id')->get();
        $store_poses = [];
        $weighing_scale_setting = System::getProperty('weighing_scale_setting') ?  json_decode(System::getProperty('weighing_scale_setting'), true) : [];

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
                'transaction_date' => !empty($request->transaction_date) ? $request->transaction_date : Carbon::now(),
                'invoice_no' => $this->productUtil->getNumberByType('sell'),
                'ticket_number' => $this->transactionUtil->getTicketNumber(),
                'is_direct_sale' => !empty($request->is_direct_sale) ? 1 : 0,
                'status' => $request->status,
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'discount_type' => $request->discount_type,
                'discount_value' => $this->commonUtil->num_f($request->discount_value),
                'discount_amount' => $this->commonUtil->num_f($request->discount_amount),
                'current_deposit_balance' => $this->commonUtil->num_f($request->current_deposit_balance),
                'used_deposit_balance' => $this->commonUtil->num_f($request->used_deposit_balance),
                'remaining_deposit_balance' => $this->commonUtil->num_f($request->remaining_deposit_balance),
                'add_to_deposit' => $this->commonUtil->num_f($request->add_to_deposit),
                'tax_id' => !empty($request->tax_id_hidden) ? $request->tax_id_hidden : null,
                'total_tax' => $this->commonUtil->num_f($request->total_tax),
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'terms_and_condition_id' => !empty($request->terms_and_condition_id) ? $request->terms_and_condition_id : null,
                'deliveryman_id' => !empty($request->deliveryman_id_hidden) ? $request->deliveryman_id_hidden : null,
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
                    $this->productUtil->updateBlockQuantity($sell_line['product_id'], $sell_line['variation_id'], $transaction->store_id, $sell_line['quantity'], 'add');
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

                    $payment_data = [
                        'transaction_id' => $transaction->id,
                        'amount' => $amount,
                        'method' => $payment['method'],
                        'paid_on' => $transaction->transaction_date,
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
                    if ($amount > 0) {
                        $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);
                    }
                    $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
                    $this->cashRegisterUtil->addPayments($transaction, $payment_data, 'credit');
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

            if ($request->submit_type == 'send' && $transaction->is_quotation) {
                $this->notificationUtil->sendQuotationToCustomer($transaction->id);
            }


            $html_content = $this->transactionUtil->getInvoicePrint($transaction, $payment_types);


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
        if ($request->action == 'send') {
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
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id');
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();
        $product_classes = ProductClass::select('name', 'id')->get();
        $cashiers = Employee::getDropdownByJobType('Cashier', true, true);
        $weighing_scale_setting = System::getProperty('weighing_scale_setting') ?  json_decode(System::getProperty('weighing_scale_setting'), true) : [];

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
                    foreach ($request->payments as $payment) {

                        $amount = $this->commonUtil->num_uf($payment['amount']);
                        $payment_data = [
                            'transaction_payment_id' => !empty($payment['transaction_payment_id']) ? $payment['transaction_payment_id'] : null,
                            'transaction_id' => $transaction->id,
                            'amount' => $amount,
                            'method' => $payment['method'],
                            'paid_on' => $transaction->transaction_date,
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
                        ];
                        if ($amount > 0) {
                            $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);
                        }
                        $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
                    }
                    $this->cashRegisterUtil->updateSellPayments($transaction, $request->payments);
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
                $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
            }


            DB::commit();


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
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id');

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
                $sales_promotions = SalesPromotion::whereDate('start_date', '<', Carbon::now())->whereDate('end_date', '>', Carbon::now())->get();
                $sp_product_ids = [];
                foreach ($sales_promotions as $sales_promotion) {
                    $sp_product_ids = array_merge($sp_product_ids, $sales_promotion->product_ids);
                }

                $query->whereIn('products.id',  $sp_product_ids);
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

        if (session('SYSTEM_MODE') != 'restaurant') {
            $query->take(30);
        }

        $products = $query->groupBy('variations.id')->get();

        $html = '';

        return view('sale_pos.partials.filtered_products')->with(compact(
            'products'
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
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                })
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
                            'qty_available' => $variation['qty'],
                            'is_service' => $value['is_service']
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
            $weighing_scale_barcode = $request->input('weighing_scale_barcode');


            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $store_id = $request->input('store_id');
            $customer_id = $request->input('customer_id');
            $edit_quantity = !empty($request->input('edit_quantity')) ? $request->input('edit_quantity') : 1;
            $added_products = json_decode($request->input('added_products'), true);

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
                    ->with(compact('products', 'index', 'sale_promotion_details', 'product_discount_details', 'edit_quantity'))->render();

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
            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];
            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->leftjoin('users', 'transactions.created_by', 'users.id')
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
                'transactions.*',
                'users.name as created_by_name',
                'customers.name as customer_name',
            )
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
                ->editColumn('final_total', '{{@num_format($final_total)}}')
                ->addColumn('customer_type', function ($row) {
                    if (!empty($row->customer->customer_type)) {
                        return $row->customer->customer_type->name;
                    } else {
                        return '';
                    }
                })
                ->addColumn('method', function ($row) {
                    if (!empty($row->transaction_payments[0]->method)) {
                        return ucfirst($row->transaction_payments[0]->method);
                    } else {
                        return '';
                    }
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
                        return $row->deliveryman->name;
                    } else {
                        return '';
                    }
                })
                ->editColumn('status', function ($row) {
                    if ($row->payment_status == 'pending') {
                        return '<span class="label label-success">' . __('lang.pay_later') . '</span>';
                    } else {
                        return '<span class="label label-danger">' . ucfirst($row->status) . '</span>';
                    }
                })
                ->editColumn('created_by', '{{$created_by_name}}')
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
                        if (auth()->user()->can('superadmin')) {
                            $html .=
                                '<a href="' . action('SellController@edit', $row->id) . '" class="btn btn-success"><i
                            title="' . __('lang.edit') . '" data-toggle="tooltip"
                            class="dripicons-document-edit"></i></a>';
                        }
                        if (auth()->user()->can('superadmin')) {
                            $html .=
                                '<a data-href="' . action('SellController@destroy', $row->id) . '"
                            title="' . __('lang.delete') . '" data-toggle="tooltip"
                            data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                            class="btn btn-danger delete_item" style="color: white"><i class="fa fa-trash"></i></a>';
                        }
                        if (auth()->user()->can('return.sell_return.create_and_edit')) {
                            if (empty($row->return_parent)) {
                                $html .=
                                    '  <a href="' . action('SellReturnController@add', $row->id) . '"
                                title="' . __('lang.sell_return') . '" data-toggle="tooltip" class="btn btn-secondary"
                                style="color: white"><i class="fa fa-undo"></i></a>';
                            }
                        }
                        if (auth()->user()->can('return.sell_return.create_and_edit')) {
                            if ($row->status != 'draft' && $row->payment_status != 'paid') {
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
                    'invoice_no',
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
    public function getDraftTransactions(Request $request)
    {
        if (request()->ajax()) {
            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
            $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
                ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                ->where('type', 'sell')->where('status', 'draft');

            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
            }
            if (!empty(request()->start_date)) {
                $query->where('transaction_date', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $query->where('transaction_date', '<=', request()->end_date);
            }

            $transactions = $query->select(
                'transactions.*',
                'customer_types.name as customer_type_name',
                'customers.name as customer_name'
            )
                ->orderBy('transaction_date', 'desc')->get();

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
                ->addColumn('method', function ($row) {
                    if (!empty($row->transaction_payments[0]->method)) {
                        return ucfirst($row->transaction_payments[0]->method);
                    } else {
                        return '';
                    }
                })

                ->addColumn('deliveryman_name', function ($row) {
                    if (!empty($row->deliveryman)) {
                        return $row->deliveryman->name;
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
                            '<a  target="_blank" href="' . action('SellPosController@edit', $row->id) . '" class="btn btn-success"><i
                        title="' . __('lang.edit') . '" data-toggle="tooltip"
                        class="dripicons-document-edit"></i></a>';
                        $html .=
                            '<button class="btn btn-danger remove_draft" data-href=' . action(
                                'SellController@destroy',
                                $row->id
                            ) . '
                            data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                            ><i class="dripicons-trash"></i></button>';


                        $html .=
                            '<a target="_blank" href="' . action('SellPosController@edit', $row->id) . '?status=final"
                            title="' . __('lang.pay_now') . '" data-toggle="tooltip"
                            class="btn btn-success"><i class="fa fa-money"></i></a>';

                        $html .= '</div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
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

        $balance = $this->transactionUtil->getCustomerBalance($customer->id)['balance'];
        $customer_type = CustomerType::find($customer->customer_type_id);

        return ['customer' => $customer, 'rp_value' => $rp_value, 'total_redeemable'  => $total_redeemable, 'customer_type_name' => !empty($customer_type) ? $customer_type->name : '', 'balance' => $balance];
    }
}
