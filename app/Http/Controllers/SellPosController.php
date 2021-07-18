<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\GiftCard;
use App\Models\Product;
use App\Models\Store;
use App\Models\StorePos;
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
            return redirect()->action('CashRegisterController@create') . '?is_pos=1';
        }

        $categories = Category::whereNull('parent_id')->get();
        $sub_categories = Category::whereNotNull('parent_id')->get();
        $brands = Brand::all();
        $store_pos = StorePos::where('user_id', Auth::user()->id)->first();
        $customers = Customer::getCustomerArrayWithMobile();
        $taxes = Tax::get();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');
        $tac = TermsAndCondition::where('type', 'invoice')->pluck('name', 'id');
        $walk_in_customer = Customer::where('name', 'Walk-in-customer')->first();

        return view('sale_pos.pos')->with(compact(
            'categories',
            'walk_in_customer',
            'deliverymen',
            'sub_categories',
            'tac',
            'brands',
            'store_pos',
            'customers',
            'taxes',
            'payment_types',
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
                $transaction_data['block_for_days'] = !empty($request->block_for_days) ? $request->block_for_days : 0; //TODO:: create commond to change these column
                $transaction_data['validity_days'] = !empty($request->validity_days) ? $request->validity_days : 0;
            }
            $transaction = Transaction::create($transaction_data);

            $this->transactionUtil->createOrUpdateTransactionSellLine($transaction, $request->transaction_sell_line);

            foreach ($request->transaction_sell_line as $sell_line) {
                if (empty($sell_line['transaction_sell_line_id'])) {
                    if ($transaction->status == 'final') {
                        $this->productUtil->decreaseProductQuantity($sell_line['product_id'], $sell_line['variation_id'], $transaction->store_id, $sell_line['quantity']);
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
                    // $transaction->rp_redeemed = $request->rp_redeemed; //front end
                    $transaction->rp_redeemed_value = $request->rp_redeemed_value;
                    $rp_redeemed = $this->transactionUtil->calcuateRedeemPoints($transaction); //back end
                    $transaction->rp_redeemed = $rp_redeemed;
                }

                $transaction->save();

                $this->transactionUtil->updateCustomerRewardPoints($transaction->customer_id, $points_earned, 0, $request->rp_redeemed, 0);
            }

            $amount = $this->commonUtil->num_uf($request->amount);
            $payment_data = [
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'method' => $request->method,
                'paid_on' => $transaction->transaction_date,
                'ref_number' => $request->ref_number,
                'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                'card_number' => $request->card_number,
                'card_security' => $request->card_security,
                'card_month' => $request->card_month,
                'cheque_number' => $request->cheque_number,
                'bank_name' => $request->bank_name,
                'ref_number' => $request->ref_number,
                'gift_card_number' => $request->gift_card_number,
                'amount_to_be_used' => $request->amount_to_be_used,
                'payment_note' => $request->payment_note,
            ];
            if ($transaction->status != 'draft') {
                if ($amount > 0) {
                    $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);
                }
                $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

                $this->cashRegisterUtil->addPayments($transaction, $payment_data, 'credit');

                if (!empty($transaction->coupon_id)) {
                    Coupon::where('id', $transaction->coupon_id)->update(['used' => 1]);
                }

                if (!empty($transaction->gift_card__id)) {
                    $remaining_balance = $this->commonUtil->num_uf($request->remaining_balance);
                    GiftCard::where('id', $transaction->gift_card__id)->update(['balance' => $remaining_balance, 'customer_id' => $request->customer_id]);
                }
            }


            DB::commit();

            $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();


            if ($transaction->is_direct_sale) {
                $output = [
                    'success' => true,
                    'msg' => __('lang.success')
                ];

                return redirect()->back()->with('status', $output);
            }

            if ($request->submit_type == 'send' && $transaction->is_quotation) {
                $this->notificationUtil->sendQuotationToCustomer($transaction->id);
            }

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
        //
    }

    /**
     * filter the products by brand or category
     *
     * @param integer $id
     * @param string $type
     * @return html
     */
    public function getProductItemsByFilter($id, $type, Request $request)
    {
        $query = Product::leftjoin('variations', 'products.id', 'variations.product_id');

        if ($type == 'brand') {
            $query->where('brand_id', $id);
        }

        if ($type == 'category') {
            $query->where('category_id', $id);
        }
        if ($type == 'sub_category') {
            $query->where('sub_category_id', $id);
        }

        if (!empty($request->selling_filter)) {
            if ($request->selling_filter == 'best_selling') {
            }
            if ($request->selling_filter == 'slow_moving_items') {
            }
            if ($request->selling_filter == 'product_in_last_transactions') {
            }
        }
        if (!empty($request->price_filter)) {
            if ($request->price_filter == 'highest_price') {
                $query->orderBy('products.sell_price', 'asc');
            }
            if ($request->price_filter == 'lowest_price') {
                $query->orderBy('products.sell_price', 'desc');
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
            if ($request->expiry_filter == 'nearest_expiry') {
            }
            if ($request->expiry_filter == 'longest_expiry') {
            }
        }
        if (!empty($request->sale_promo_filter)) {
            if ($request->sale_promo_filter == 'items_in_sale_promotion') {
            }
        }

        $products = $query->select(
            'products.*',
            'variations.id as variation_id',
            'variations.name as variation_name',
            'variations.sub_sku',
        )
            ->groupBy('variations.id')->get();

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
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku',
                    DB::raw('(product_stores.qty_available - product_stores.block_qty) as qty')
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
                $products_array[$product->product_id]['qty'] = $this->productUtil->num_uf($product->qty);
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
                    if ($no_of_records > 1 && $value['type'] != 'single') {
                        $result[] = [
                            'id' => $i,
                            'text' => $value['name'] . ' - ' . $value['sku'],
                            'variation_id' => 0,
                            'product_id' => $key,
                            'qty_available' => $value['qty']
                        ];
                    }
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
                            'qty_available' => $value['qty']
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
            $store_id = $request->input('store_id');
            $customer_id = $request->input('customer_id');

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProductByStore($product_id, $variation_id);

                $sale_promotion_details = $this->productUtil->getSalesPromotionDetail($product_id, $store_id, $customer_id);

                return view('sale_pos.partials.product_row')
                    ->with(compact('products', 'index', 'sale_promotion_details'));
            }
        }
    }

    /**
     * list of recent transactions
     *
     * @return void
     */
    public function getRecentTransactions()
    {
        $query = Transaction::where('type', 'sell')->where('status', '!=', 'draft');

        if (!empty(request()->start_date)) {
            $query->whereDate('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('transaction_date', '<=', request()->end_date);
        }
        if (!empty(request()->customer_id)) {
            $query->where('customer_id', request()->customer_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('sale_pos.partials.recent_transactions')->with(compact('transactions', 'payment_types'));
    }

    /**
     * list of draft transactions
     *
     * @return void
     */
    public function getDraftTransactions()
    {
        $query = Transaction::where('type', 'sell')->where('status', 'draft');

        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->where('transaction_date', '<=', request()->end_date);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('sale_pos.partials.view_draft')->with(compact('transactions', 'payment_types'));
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

        return ['customer' => $customer, 'rp_value' => $rp_value, 'total_redeemable'  => $total_redeemable];
    }
}
