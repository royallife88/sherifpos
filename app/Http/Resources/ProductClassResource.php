<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductClassResource extends JsonResource
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
            'translations' => $this->translations,
            'description' => $this->description,
            'sort' => $this->sort,
            'status' => $this->status,
            'image' => $this->getFirstMediaUrl('product_class'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
