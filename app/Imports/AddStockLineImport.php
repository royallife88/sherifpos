<?php

namespace App\Imports;

use App\Models\AddStockLine;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class AddStockLineImport implements ToModel, WithHeadingRow, WithValidation
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
            'variations.id as variation_id',
            'purchase_price'
            )
            ->first();
            if(empty($product)){
                print_r($row['product_code']); die();

            }

        return new AddStockLine([
            'transaction_id' => $this->transaction_id,
            'product_id' => $product->product_id,
            'variation_id' => $product->variation_id,
            'quantity' => $row['quantity'],
            'purchase_price' => $product->purchase_price,
            'sub_total' => $row['quantity'] * $product->purchase_price,
        ]);
    }

    public function rules(): array
    {
        return [
            'product_code' => 'exists:variations,sub_sku',
            'quantity' => 'required'
        ];
    }
}
