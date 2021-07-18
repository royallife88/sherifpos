<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\PurchaseOrderLine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PurchaseOrderLineImport implements ToModel, WithHeadingRow
{
    protected $transaction_id;

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
        return new PurchaseOrderLine([
            'transaction_id' => $this->transaction_id,
            'product_id' => $product->product_id,
            'variation_id' => $product->variation_id,
            'quantity' => $row['quantity'],
            'purchase_price' => $row['product_price'],
            'sub_total' => $row['quantity'] * $row['product_price'],
        ]);
    }
}
