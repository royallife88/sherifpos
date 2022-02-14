<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedemptionOfPoint extends Model
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
        'earning_of_point_ids' => 'array',
        'product_ids' => 'array',
        'pct_data' => 'array',
    ];


    public function earning_of_points()
    {
        return $this->belongsToJson(EarningOfPoint::class, 'earning_of_point_ids');
    }

    public function stores()
    {
        return $this->belongsToJson(Store::class, 'store_ids');
    }

    public function products()
    {
        return $this->belongsToJson(Product::class, 'product_ids');
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
