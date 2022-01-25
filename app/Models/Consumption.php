<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumption extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function consumption_details()
    {
        return $this->hasMany(ConsumptionDetail::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class)->withDefault(['name' => '']);
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function raw_material()
    {
        return $this->belongsTo(Product::class, 'raw_material_id', 'id')->withDefault(['name' => '']);
    }
}
