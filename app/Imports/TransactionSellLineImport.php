<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\TransactionSellLine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionSellLineImport implements ToModel, WithHeadingRow
{
    protected $transaction_id;

    /**
     * Constructor
     *
     * @param int $transaction_id
     * @return void
     */
    public function __construct($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('sub_sku', $row['product_code'])
            ->select(
                'products.id as product_id',
                'variations.id as variation_id'
            )
            ->first();

        return new TransactionSellLine([
            'transaction_id' => $this->transaction_id,
            'product_id' => $product->product_id,
            'variation_id' => $product->variation_id,
            'coupon_discount' => 0,
            'coupon_discount_type' => null,
            'coupon_discount_amount' =>  0,
            'promotion_discount' => 0,
            'promotion_discount_type' => null,
            'promotion_discount_amount' =>  0,
            'quantity' => $row['quantity'],
            'sell_price' => $row['product_price'],
            'sub_total' => $row['quantity'] * $row['product_price'],
        ]);
    }
}
