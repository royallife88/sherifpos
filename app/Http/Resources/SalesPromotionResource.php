<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesPromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'code' => $this->code,
            'store_ids' => $this->store_ids,
            'customer_type_ids' => $this->customer_type_ids,
            'product_ids' => $this->product_ids,
            'pct_data' => $this->pct_data,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'actual_sell_price' => $this->actual_sell_price,
            'purchase_condition' => $this->purchase_condition,
            'product_condition' => $this->product_condition,
            'package_promotion_qty' => $this->package_promotion_qty,
            'condition_product_ids' => $this->condition_product_ids,
            'pci_data' => $this->pci_data,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'generate_barcode' => $this->generate_barcode,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
