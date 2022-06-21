<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
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
        'store_ids' => 'array'
    ];

    public function stores()
    {
        return $this->belongsToJson(Store::class, 'store_ids');
    }

    public static function getDropdown($store_id = null)
    {
        $taxes = [];
        if (empty($store_id)) {
            $store_id = session('user.store_id');
        }

        $product_taxes = Tax::where('type', 'product_tax')->select('id', 'name', 'rate')->get()->toArray();
        $taxes += $product_taxes;

        $general_taxes = Tax::where('type', 'general_tax')->select('id', 'name', 'rate', 'store_ids', 'status')->get();

        foreach ($general_taxes as $g_tax) {
            $arr = ['id' => $g_tax->id, 'name' => $g_tax->name, 'rate' => $g_tax->rate];
            if ($g_tax->status == 1) {
                if (!empty($g_tax->store_ids)) {
                    if (in_array($store_id, $g_tax->store_ids)) {
                        $taxes[] = $arr;
                    }
                } else {
                    $taxes[] = $arr;
                }
            } else {
                if (!empty($g_tax->store_ids)) {
                    if (!in_array($store_id, $g_tax->store_ids)) {
                        $taxes[] = $arr;
                    }
                }
            }
        }

        return $taxes;
    }
}
