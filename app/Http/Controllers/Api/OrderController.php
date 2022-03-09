<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
use App\Models\Variation;
use App\Utils\CashRegisterUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $cashRegisterUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, CashRegisterUtil $cashRegisterUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return json_encode(['asdfsdf']);
        $input = $request->all();
        $validator = Validator::make($input, [
            'customer_name' => 'required',
            'phone_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        try {
            $customer = Customer::firstOrCreate(['mobile_number' => $input['phone_number'], 'name' => $input['customer_name'] ]);
            $store = Store::first();
            $store_pos = StorePos::where('store_id', $store->id)->first();

            $transaction_data = [
                'store_id' => $store->id,
                'customer_id' => $customer->id,
                'store_pos_id' => $store_pos->id,
                'type' => 'sell',
                'final_total' => $input['final_total'],
                'grand_total' => $input['final_total'],
                'transaction_date' => $input['created_at'],
                'invoice_no' => $this->productUtil->getNumberByType('sell'),
                'ticket_number' => $this->transactionUtil->getTicketNumber(),
                'is_direct_sale' =>  0,
                'status' => 'draft',
                'sale_note' => $input['sales_note'],
                'table_no' => $input['table_no'],
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'discount_amount' => $input['discount_amount'],
                'delivery_status' => 'pending',
                'delivery_cost' => 0,
                'delivery_address' => null,
                'delivery_cost_paid_by_customer' => 0,
                'restaurant_order_id' => $input['id'],
                'created_by' => 1,
            ];
            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);


            foreach ($input['order_details'] as $line) {
                $variation = Variation::find($line['variation_pos_model_id']);
                $product = Product::find($variation->product_id);
                $transaction_sell_line = new TransactionSellLine();
                $transaction_sell_line->transaction_id = $transaction->id;
                $transaction_sell_line->product_id = $product->id;
                $transaction_sell_line->variation_id = $variation->id;
                $transaction_sell_line->coupon_discount = !empty($line['coupon_discount']) ? $line['coupon_discount'] : 0;
                $transaction_sell_line->coupon_discount_type = !empty($line['coupon_discount_type']) ? $line['coupon_discount_type'] : null;
                $transaction_sell_line->coupon_discount_amount = !empty($line['coupon_discount_amount']) ? $line['coupon_discount_amount'] : 0;
                $transaction_sell_line->promotion_discount = !empty($line['promotion_discount']) ? $line['promotion_discount'] : 0;
                $transaction_sell_line->promotion_discount_type = !empty($line['promotion_discount_type']) ? $line['promotion_discount_type'] : null;
                $transaction_sell_line->promotion_discount_amount = !empty($line['promotion_discount_amount']) ? $line['promotion_discount_amount'] : 0;
                $transaction_sell_line->product_discount_value = !empty($line['product_discount_value']) ? $line['product_discount_value'] : 0;
                $transaction_sell_line->product_discount_type = !empty($line['product_discount_type']) ? $line['product_discount_type'] : null;
                $transaction_sell_line->product_discount_amount = !empty($line['product_discount_amount']) ? $line['product_discount_amount'] : 0;
                $transaction_sell_line->quantity = $line['quantity'];
                $transaction_sell_line->sell_price = $line['price'];
                $transaction_sell_line->purchase_price = $variation->default_purchase_price;
                $transaction_sell_line->sub_total = $line['sub_total'];
                $transaction_sell_line->tax_id = !empty($line['tax_id']) ? $line['tax_id'] : null;
                $transaction_sell_line->tax_rate = !empty($line['tax_rate']) ? $line['tax_rate'] : 0;
                $transaction_sell_line->item_tax = !empty($line['item_tax']) ? $line['item_tax'] : 0;
                $transaction_sell_line->restaurant_order_detail_id = !empty($line['id']) ? $line['id'] : 0;
                $transaction_sell_line->save();
            }
            $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

            DB::commit();
            return $this->handleResponse(new OrderResource($transaction), 'Order created!');
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            return $this->handleError($e->getMessage(), [__('lang.something_went_wrong')], 503);
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
        //
    }
}
