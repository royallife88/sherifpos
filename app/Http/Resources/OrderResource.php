<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'store_id' => $this->store_id,
            'customer_id' => $this->customer_id,
            'store_pos_id' => $this->store_pos_id,
            'type' => 'sell',
            'final_total' => $this->final_total,
            'grand_total' => $this->grand_total,
            'transaction_date' => $this->transaction_date,
            'invoice_no' => $this->invoice_no,
            'ticket_number' => $this->ticket_number,
            'status' => $this->status,
            'sale_note' => $this->sale_note,
            'table_no' => $this->table_no,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount_amount' => $this->discount_amount,
            'delivery_status' => $this->delivery_status,
            'delivery_cost' => $this->delivery_cost,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'transaction_sell_lines' => $this->transaction_sell_lines,
        ];
    }
}
