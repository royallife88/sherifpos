<?php

namespace App\Utils;

use App\Http\Controllers\PurchaseOrderController;
use App\Models\AddStockLine;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\EarningOfPoint;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductStore;
use App\Models\PurchaseOrderLine;
use App\Models\RedemptionOfPoint;
use App\Models\Store;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\TransactionSellLine;
use App\Models\Variation;
use App\Utils\Util;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Mockery\Matcher\Type;

class TransactionUtil extends Util
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }

    /**
     * createOrUpdateTransactionPayment
     *
     * @param object $transaction
     * @param array $payment_data
     * @return object
     */
    public function createOrUpdateTransactionPayment($transaction, $payment_data)
    {
        if (!empty($payment_data['transaction_payment_id'])) {
            $transaction_payment = TransactionPayment::find($payment_data['transaction_payment_id']);
            $transaction_payment->amount = $payment_data['amount'];
            $transaction_payment->method = $payment_data['method'];
            $transaction_payment->paid_on = $payment_data['paid_on'];
            $transaction_payment->ref_number = !empty($payment_data['ref_number']) ? $payment_data['ref_number'] : null;
            $transaction_payment->bank_deposit_date = !empty($payment_data['bank_deposit_date']) ? $payment_data['bank_deposit_date'] : null;
            $transaction_payment->bank_name = !empty($payment_data['bank_name']) ?  $payment_data['bank_name'] : null;
            $transaction_payment->card_number = !empty($payment_data['card_number']) ?  $payment_data['card_number'] : null;
            $transaction_payment->card_security = !empty($payment_data['card_security']) ?  $payment_data['card_security'] : null;
            $transaction_payment->card_month = !empty($payment_data['card_month']) ?  $payment_data['card_month'] : null;
            $transaction_payment->cheque_number = !empty($payment_data['cheque_number']) ?  $payment_data['cheque_number'] : null;
            $transaction_payment->gift_card_number = !empty($payment_data['gift_card_number']) ?  $payment_data['gift_card_number'] : null;
            $transaction_payment->amount_to_be_used = !empty($payment_data['amount_to_be_used']) ?  $this->num_uf($payment_data['amount_to_be_used']) : 0;
            $transaction_payment->payment_note = !empty($payment_data['payment_note']) ?  $payment_data['payment_note'] : null;
            $transaction_payment->save();
        } else {
            $transaction_payment = null;
            if (!empty($payment_data['amount'])) {
                $transaction_payment = TransactionPayment::create($payment_data);
                //     if($transaction->type == 'sell'){
                //         if($payment_data['method'] != 'deposit'){
                //             if($payment_data['amount'] > $transaction->final_total)
                //             $payment_data['amount'] = $transaction->final_total;
                //         }else{

                //         }
                //     }
            }
        }

        return $transaction_payment;
    }

    /**
     * updateTransactionPaymentStatus function
     *
     * @param integer $transaction_id
     * @return void
     */
    public function updateTransactionPaymentStatus($transaction_id)
    {
        $transaction_payments = TransactionPayment::where('transaction_id', $transaction_id)->get();

        $total_paid = $transaction_payments->sum('amount');

        $transaction = Transaction::find($transaction_id);
        $final_amount = $transaction->final_total - $transaction->used_deposit_balance;

        $payment_status = 'pending';
        if ($final_amount <= $total_paid) {
            $payment_status = 'paid';
        } elseif ($total_paid > 0 && $final_amount > $total_paid) {
            $payment_status = 'partial';
        }
        $transaction->payment_status = $payment_status;
        $transaction->save();
    }


    /**
     * create sell line for product in sale
     *
     * @param object $transaction
     * @param array $transaction_sell_lines
     * @return boolean
     */
    public function createOrUpdateTransactionSellLine($transaction, $transaction_sell_lines)
    {
        $keep_sell_lines = [];

        foreach ($transaction_sell_lines as $line) {
            $old_quantity = 0;
            if (!empty($transaction_sell_line['transaction_sell_line_id'])) {
                $transaction_sell_line = TransactionSellLine::find($line['transaction_sell_line_id']);
                $transaction_sell_line->product_id = $line['product_id'];
                $transaction_sell_line->variation_id = $line['variation_id'];
                $transaction_sell_line->coupon_discount = !empty($line['coupon_discount']) ? $this->num_uf($line['coupon_discount']) : 0;
                $transaction_sell_line->coupon_discount_type = !empty($line['coupon_discount_type']) ? $line['coupon_discount_type'] : null;
                $transaction_sell_line->coupon_discount_amount = !empty($line['coupon_discount_amount']) ? $this->num_uf($line['coupon_discount_amount']) : 0;
                $transaction_sell_line->promotion_discount = !empty($line['promotion_discount']) ? $this->num_uf($line['promotion_discount']) : 0;
                $transaction_sell_line->promotion_discount_type = !empty($line['promotion_discount_type']) ? $line['promotion_discount_type'] : null;
                $transaction_sell_line->promotion_discount_amount = !empty($line['promotion_discount_amount']) ? $this->num_uf($line['promotion_discount_amount']) : 0;
                $transaction_sell_line->product_discount_value = !empty($line['product_discount_value']) ? $this->num_uf($line['product_discount_value']) : 0;
                $transaction_sell_line->product_discount_type = !empty($line['product_discount_type']) ? $line['product_discount_type'] : null;
                $transaction_sell_line->product_discount_amount = !empty($line['product_discount_amount']) ? $this->num_uf($line['product_discount_amount']) : 0;
                $old_quantity = $transaction_sell_line->quantity;
                $transaction_sell_line->quantity = $this->num_uf($line['quantity']);
                $transaction_sell_line->sell_price = $this->num_uf($line['sell_price']);
                $transaction_sell_line->sub_total = $this->num_uf($line['sub_total']);
                $transaction_sell_line->save();
                $keep_sell_lines[] = $line['transaction_sell_line_id'];
            } else {
                $transaction_sell_line = new TransactionSellLine();
                $transaction_sell_line->transaction_id = $transaction->id;
                $transaction_sell_line->product_id = $line['product_id'];
                $transaction_sell_line->variation_id = $line['variation_id'];
                $transaction_sell_line->coupon_discount = !empty($line['coupon_discount']) ? $this->num_uf($line['coupon_discount']) : 0;
                $transaction_sell_line->coupon_discount_type = !empty($line['coupon_discount_type']) ? $line['coupon_discount_type'] : null;
                $transaction_sell_line->coupon_discount_amount = !empty($line['coupon_discount_amount']) ? $this->num_uf($line['coupon_discount_amount']) : 0;
                $transaction_sell_line->promotion_discount = !empty($line['promotion_discount']) ? $this->num_uf($line['promotion_discount']) : 0;
                $transaction_sell_line->promotion_discount_type = !empty($line['promotion_discount_type']) ? $line['promotion_discount_type'] : null;
                $transaction_sell_line->promotion_discount_amount = !empty($line['promotion_discount_amount']) ? $this->num_uf($line['promotion_discount_amount']) : 0;
                $transaction_sell_line->product_discount_value = !empty($line['product_discount_value']) ? $this->num_uf($line['product_discount_value']) : 0;
                $transaction_sell_line->product_discount_type = !empty($line['product_discount_type']) ? $line['product_discount_type'] : null;
                $transaction_sell_line->product_discount_amount = !empty($line['product_discount_amount']) ? $this->num_uf($line['product_discount_amount']) : 0;
                $transaction_sell_line->quantity = $this->num_uf($line['quantity']);
                $transaction_sell_line->sell_price = $this->num_uf($line['sell_price']);
                $transaction_sell_line->sub_total = $this->num_uf($line['sub_total']);
                $transaction_sell_line->save();
                $keep_sell_lines[] = $transaction_sell_line->id;
            }
            $this->updateSoldQuantityInAddStockLine($transaction_sell_line->product_id, $transaction_sell_line->variation_id, $transaction->store_id, $line['quantity'], $old_quantity);
        }

        //delete sell lines remove by user
        TransactionSellLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_sell_lines)->delete();

        return true;
    }

    /**
     * update the sold quanitty in purchase lines
     *
     * @param int $product_id
     * @param int $variation_id
     * @param int $store_id
     * @param float $new_quantity
     * @param float $old_quantity
     * @return void
     */
    public function updateSoldQuantityInAddStockLine($product_id, $variation_id, $store_id, $new_quantity, $old_quantity)
    {
        $qty_difference = $this->num_uf($new_quantity) - $this->num_uf($old_quantity);
        if ($qty_difference != 0) {
            $add_stock_lines = AddStockLine::leftjoin('transactions', 'add_stock_lines.transaction_id', 'transactions.id')
            ->where('transactions.store_id', $store_id)
            ->where('product_id', $product_id)
            ->where('variation_id', $variation_id)
            ->select('add_stock_lines.id', DB::raw('SUM(quantity - quantity_sold) as remaining_qty'))
            ->having('remaining_qty', '>', 0)
            ->groupBy('add_stock_lines.id')
            ->get();
            foreach ($add_stock_lines as $line) {
                if ($qty_difference == 0) {
                    return true;
                }

                if ($line->remaining_qty >= $qty_difference) {
                    $line->increment('quantity_sold', $qty_difference);
                    $qty_difference = 0;
                }
                if ($line->remaining_qty < $qty_difference) {
                    $line->increment('quantity_sold', $line->remaining_qty);
                    $qty_difference = $qty_difference - $line->remaining_qty;
                }
            }
        }

        return true;
    }



    /**
     * Updates reward point of a customer
     *
     * @return void
     */
    public function updateCustomerRewardPoints(
        $customer_id,
        $earned,
        $earned_before = 0,
        $redeemed = 0,
        $redeemed_before = 0
    ) {
        $customer = Customer::find($customer_id);

        //Return if walk in customer
        if ($customer->is_default == 1) {
            return false;
        }

        $total_earned = $earned - $earned_before;
        $total_redeemed = $redeemed - $redeemed_before;

        $diff = $total_earned - $total_redeemed;

        $customer_points = empty($customer->total_rp) ? 0 : $customer->total_rp;
        $total_points = $customer_points + $diff;

        $customer->total_rp = $total_points;
        $customer->total_rp_used += $total_redeemed;
        $customer->save();
    }

    /**
     * Calculates reward points to be earned by a customer
     *
     * @return integer
     */
    public function calculateTotalRewardPointsValue($customer_id, $store_id)
    {
        $total_points_value = 0;

        $customer = Customer::find($customer_id);
        if ($customer->is_default != 1) {

            $customer_type_id = (string) $customer->customer_type_id;
            if (!empty($customer_type_id)) {
                $redemption_of_point = RedemptionOfPoint::whereJsonContains('customer_type_ids', $customer_type_id)
                    ->whereJsonContains('store_ids', $store_id)
                    ->first();
                if (!empty($redemption_of_point)) {
                    if (!empty($redemption_of_point->end_date)) {
                        //if end date set then check for expiry
                        if ($redemption_of_point->end_date >= date('Y-m-d')) {
                            $total_points_value = $this->calculatePointsValue($customer->total_rp,  $redemption_of_point);
                        }
                    } else {
                        //if no end date then its is for unlimited time
                        $total_points_value = $this->calculatePointsValue($customer->total_rp,  $redemption_of_point);
                    }
                }
            }
        }

        return $total_points_value;
    }
    public function calculatePointsValue($total_points, $redemption_of_point)
    {
        return floor(($total_points / 1000) * $redemption_of_point->value_of_1000_points);
    }

    public function calcuateRedeemPoints($transaction)
    {
        $total_points = 0;

        $customer = Customer::find($transaction->customer_id);
        $store_id = (string) $transaction->store_id;

        if ($customer->is_default != 1) {

            $customer_type_id = (string) $customer->customer_type_id;
            if (!empty($customer_type_id)) {
                $redemption_of_points = RedemptionOfPoint::whereJsonContains('customer_type_ids', $customer_type_id)
                    ->whereJsonContains('store_ids', $store_id)
                    ->first();
                if (!empty($redemption_of_points)) {
                    if (!empty($redemption_of_points->end_date)) {
                        //if end date set then check for expiry
                        if ($redemption_of_points->end_date >= date('Y-m-d')) {
                            $total_points = $this->calculateRedeemPointsByProdct($transaction->transaction_sell_lines,  $redemption_of_points);
                        }
                    } else {
                        //if no end date then its is for unlimited time
                        $total_points = $this->calculateRedeemPointsByProdct($transaction->transaction_sell_lines,  $redemption_of_points);
                    }
                }
            }
        }
        return $total_points;
    }

    public function calculateRedeemPointsByProdct($sell_lines,  $redemption_of_points)
    {
        $points = 0;

        foreach ($sell_lines as $line) {
            //if product in this order is valid for reward
            $product_id = (string) $line->product_id;
            $product_contain = RedemptionOfPoint::where('id', $redemption_of_points->id)->whereJsonContains('product_ids', $product_id)->first();
            if (!empty($product_contain)) {
                $line->update(['point_redeemed' => 1]);
                $points += (1000 / $redemption_of_points->value_of_1000_points) * $line->sub_total;
            }
        }

        return floor($points);
    }

    public function calculateRedeemablePointValue($customer_id, $product_array, $store_id)
    {
        $customer = Customer::find($customer_id);
        $store_id = (string) $store_id;

        $total_redeemable = 0;
        if ($customer->is_default != 1) {

            $customer_type_id = (string) $customer->customer_type_id;
            if (!empty($customer_type_id)) {
                $redemption_of_points = RedemptionOfPoint::whereJsonContains('customer_type_ids', $customer_type_id)
                    ->whereJsonContains('store_ids', $store_id)
                    ->first();
                if (!empty($redemption_of_points)) {
                    if (!empty($redemption_of_points->end_date)) {
                        //if end date set then check for expiry
                        if ($redemption_of_points->end_date >= date('Y-m-d')) {
                            $total_redeemable = $this->calculateRedeemablePointsByProdct($product_array,  $redemption_of_points, $customer_id, $store_id);
                        }
                    } else {
                        //if no end date then its is for unlimited time
                        $total_redeemable = $this->calculateRedeemablePointsByProdct($product_array,  $redemption_of_points, $customer_id, $store_id);
                    }
                }
            }
        }
        return $total_redeemable;
    }

    public function calculateRedeemablePointsByProdct($product_array,  $redemption_of_points, $customer_id, $store_id)
    {
        $redeemable = 0;
        $total_redeemable_value = $this->calculateTotalRewardPointsValue($customer_id, $store_id);

        foreach ($product_array as $line) {
            if ($total_redeemable_value > 0) {
                //if product in this order is valid for redeem
                $product_id = (string) $line['product_id'];
                $sub_total = (string) $line['sub_total'];
                $product_contain = RedemptionOfPoint::where('id', $redemption_of_points->id)->whereJsonContains('product_ids', $product_id)->first();
                if (!empty($product_contain)) {
                    if ($total_redeemable_value >= $sub_total) {
                        $redeemable += $sub_total;
                        $total_redeemable_value -= $sub_total;
                    } else {
                        $redeemable += $total_redeemable_value;
                        $total_redeemable_value = 0;
                    }
                }
            }
        }

        return floor($redeemable);
    }
    /**
     * Calculates reward points to be earned from an order
     *
     * @return integer
     */
    public function calculateRewardPoints($transaction)
    {
        $total_points = 0;

        $customer = Customer::find($transaction->customer_id);
        $store_id = (string) $transaction->store_id;
        if ($customer->is_default != 1) {

            $customer_type_id = (string) $customer->customer_type_id;
            if (!empty($customer_type_id)) {
                $earning_point_system = EarningOfPoint::whereJsonContains('customer_type_ids', $customer_type_id)
                    ->whereJsonContains('store_ids', $store_id)
                    ->first();
                if (!empty($earning_point_system)) {
                    if (!empty($earning_point_system->end_date)) {
                        //if end date set then check for expiry
                        if ($earning_point_system->end_date >= date('Y-m-d')) {
                            $total_points = $this->calculatePointsByProducts($transaction->transaction_sell_lines,  $earning_point_system);
                        }
                    } else {
                        //if no end date then its is for unlimited time
                        $total_points = $this->calculatePointsByProducts($transaction->transaction_sell_lines,  $earning_point_system);
                    }
                }
            }
        }
        return $total_points;
    }

    /**
     * calculate point for each valid product
     *
     * @param object $sell_lines
     * @param object $earning_point_system
     * @return integer
     */
    public function calculatePointsByProducts($sell_lines, $earning_point_system)
    {
        $points = 0;

        foreach ($sell_lines as $line) {
            //if product in this order is valid for reward
            $product_id = (string) $line->product_id;
            $product_contain = EarningOfPoint::where('id', $earning_point_system->id)->whereJsonContains('product_ids', $product_id)->first();
            if (!empty($product_contain)) {
                $line->update(['point_earned' => 1]);
                $points += $earning_point_system->points_on_per_amount * $line->sub_total;
            }
        }

        return floor($points);
    }

    public function calculateDiscountAmount($amount, $type, $value)
    {
        if ($type == 'fixed') {
            return $value;
        }
        if ($type == 'percentage') {
            return ($amount * $value) / 100;
        }
    }

    public function calculateTaxAmount($amount, $tax_id)
    {
        $tax = Tax::find($tax_id);

        return ($amount * $tax->rate) / 100;
    }

    /**
     * change filter values by user access
     *
     * @param Request $request
     * @return array
     */
    public function getFilterOptionValues($request)
    {

        $data['store_id'] = null;
        $data['pos_id'] = null;
        if (!empty($request->store_id)) {
            $data['store_id'] = $request->store_id;
        }
        if (!empty($request->pos_id)) {
            $data['pos_id'] = $request->pos_id;
        }
        if (!session('user.is_superadmin')) {
            $data['store_id'] = session('user.store_id');
            $data['pos_id'] = session('user.pos_id');
        }

        return $data;
    }

    /**
     * update sell transaction
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function updateSellTransaction($request, $id)
    {
        $transaction_data = [
            'customer_id' => $request->customer_id,
            'final_total' => $this->num_uf($request->final_total),
            'grand_total' => $this->num_uf($request->grand_total),
            'gift_card_id' => $request->gift_card_id,
            'coupon_id' => $request->coupon_id,
            'invoice_no' => $this->productUtil->getNumberByType('sell'),
            'is_direct_sale' => !empty($request->is_direct_sale) ? 1 : 0,
            'status' => $request->status,
            'sale_note' => $request->sale_note,
            'staff_note' => $request->staff_note,
            'discount_type' => $request->discount_type,
            'discount_value' => $this->num_f($request->discount_value),
            'discount_amount' => $this->num_f($request->discount_amount),
            'tax_id' => $request->tax_id,
            'block_qty' => 0,
            'block_for_days' => 0,
            'total_tax' => $this->num_f($request->total_tax),
            'sale_note' => $request->sale_note,
            'staff_note' => $request->staff_note,
            'terms_and_condition_id' => !empty($request->terms_and_condition_id) ? $request->terms_and_condition_id : null,
            'deliveryman_id' => $request->deliveryman_id,
            'delivery_cost' => $this->num_f($request->delivery_cost),
            'delivery_address' => $request->delivery_address,
            'delivery_cost_paid_by_customer' => !empty($request->delivery_cost_paid_by_customer) ? 1 : 0,
        ];

        DB::beginTransaction();

        $transaction = Transaction::find($id);
        if ($transaction->is_quotation && $transaction->status == 'draft') {
            $transaction_data['ref_no'] = $transaction->invoice_no;
        }
        $transaction_status = $transaction->status;
        $is_block_qty = $transaction->block_qty;
        $transaction->update($transaction_data);

        $keep_sell_lines = [];
        foreach ($request->transaction_sell_line as $line) {
            $old_qty = 0;
            if (!empty($line['transaction_sell_line_id'])) {
                $transaction_sell_line = TransactionSellLine::find($line['transaction_sell_line_id']);
                $transaction_sell_line->product_id = $line['product_id'];
                $transaction_sell_line->variation_id = $line['variation_id'];
                $transaction_sell_line->coupon_discount = !empty($line['coupon_discount']) ? $this->num_uf($line['coupon_discount']) : 0;
                $transaction_sell_line->coupon_discount_type = !empty($line['coupon_discount_type']) ? $line['coupon_discount_type'] : null;
                $transaction_sell_line->coupon_discount_amount = !empty($line['coupon_discount_amount']) ? $this->num_uf($line['coupon_discount_amount']) : 0;
                if ($transaction_status == 'draft') {
                    $old_qty = 0;
                } else {
                    $old_qty = $transaction_sell_line->quantity;
                }
                $transaction_sell_line->promotion_discount = !empty($line['promotion_discount']) ? $this->num_uf($line['promotion_discount']) : 0;
                $transaction_sell_line->promotion_discount_type = !empty($line['promotion_discount_type']) ? $line['promotion_discount_type'] : null;
                $transaction_sell_line->promotion_discount_amount = !empty($line['promotion_discount_amount']) ? $this->num_uf($line['promotion_discount_amount']) : 0;
                $transaction_sell_line->product_discount_value = !empty($line['product_discount_value']) ? $this->num_uf($line['product_discount_value']) : 0;
                $transaction_sell_line->product_discount_type = !empty($line['product_discount_type']) ? $line['product_discount_type'] : null;
                $transaction_sell_line->product_discount_amount = !empty($line['product_discount_amount']) ? $this->num_uf($line['product_discount_amount']) : 0;
                $transaction_sell_line->quantity = $this->num_uf($line['quantity']);
                $transaction_sell_line->sell_price = $this->num_uf($line['sell_price']);
                $transaction_sell_line->sub_total = $this->num_uf($line['sub_total']);

                $transaction_sell_line->save();
                $keep_sell_lines[] = $line['transaction_sell_line_id'];
                $this->productUtil->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $line['quantity'], $old_qty);
                if ($is_block_qty) {
                    $block_qty = $transaction_sell_line->quantity;
                    $this->productUtil->updateBlockQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $block_qty, 'subtract');
                }
            } else {
                $transaction_sell_line = new TransactionSellLine();
                $transaction_sell_line->transaction_id = $transaction->id;
                $transaction_sell_line->product_id = $line['product_id'];
                $transaction_sell_line->variation_id = $line['variation_id'];
                $transaction_sell_line->coupon_discount = !empty($line['coupon_discount']) ? $this->num_uf($line['coupon_discount']) : 0;
                $transaction_sell_line->coupon_discount_type = !empty($line['coupon_discount_type']) ? $line['coupon_discount_type'] : null;
                $transaction_sell_line->coupon_discount_amount = !empty($line['coupon_discount_amount']) ? $this->num_uf($line['coupon_discount_amount']) : 0;
                $transaction_sell_line->promotion_discount = !empty($line['promotion_discount']) ? $this->num_uf($line['promotion_discount']) : 0;
                $transaction_sell_line->promotion_discount_type = !empty($line['promotion_discount_type']) ? $line['promotion_discount_type'] : null;
                $transaction_sell_line->promotion_discount_amount = !empty($line['promotion_discount_amount']) ? $this->num_uf($line['promotion_discount_amount']) : 0;
                $transaction_sell_line->product_discount_value = !empty($line['product_discount_value']) ? $this->num_uf($line['product_discount_value']) : 0;
                $transaction_sell_line->product_discount_type = !empty($line['product_discount_type']) ? $line['product_discount_type'] : null;
                $transaction_sell_line->product_discount_amount = !empty($line['product_discount_amount']) ? $this->num_uf($line['product_discount_amount']) : 0;
                $transaction_sell_line->quantity = $this->num_uf($line['quantity']);
                $transaction_sell_line->sell_price = $this->num_uf($line['sell_price']);
                $transaction_sell_line->sub_total = $this->num_uf($line['sub_total']);
                $transaction_sell_line->save();
                $keep_sell_lines[] = $transaction_sell_line->id;
                $this->productUtil->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $line['quantity']);
                if ($transaction->block_qty) {
                    $block_qty = $transaction_sell_line->quantity;
                    $this->productUtil->updateBlockQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $block_qty, 'subtract');
                }
            }

            $this->updateSoldQuantityInAddStockLine($transaction_sell_line->product_id, $transaction_sell_line->variation_id, $transaction->store_id, $line['quantity'], $old_qty);
        }

        //update stock for deleted lines
        $deleted_lines = TransactionSellLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_sell_lines)->get();
        foreach ($deleted_lines as $deleted_line) {
            $this->productUtil->updateProductQuantityStore($deleted_line->product_id, $deleted_line->variation_id, $transaction->store_id, $deleted_line->quantity);
            $deleted_line->delete();
        }

        $this->updateTransactionPaymentStatus($transaction->id);

        DB::commit();

        return $transaction;
    }
}
