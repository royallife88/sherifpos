<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPromotion extends Model
{
    use HasFactory, \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'store_ids' => 'array',
        'customer_type_ids' => 'array',
        'product_ids' => 'array',
        'pct_data' => 'array',
        'pci_data' => 'array',
        'condition_product_ids' => 'array',
        'package_promotion_qty' => 'array',
    ];


    public function customer_types()
    {
        return $this->belongsToJson(CustomerType::class, 'customer_type_ids');
    }

    public function stores()
    {
        return $this->belongsToJson(Store::class, 'store_ids');
    }

    public function products()
    {
        return $this->belongsToJson(Product::class, 'product_ids');
    }

    public function condition_products()
    {
        return $this->belongsToJson(Product::class, 'condition_product_ids');
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
