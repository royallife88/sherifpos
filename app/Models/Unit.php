<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    public function sub_units()
    {
        return $this->hasMany(\App\Unit::class, 'base_unit_id');
    }

    public function base_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'base_unit_id');
    }

    public static function getUnitDropdown($raw_material_only = false, $base_only = true)
    {
        $query = Unit::where('id', '>', 0);
        if ($raw_material_only) {
            $query->where('is_raw_material_unit', 1);
        }
        if ($base_only) {
            $query->whereNull('base_unit_id');
        }
        $units = $query->orderBy('name', 'asc')->pluck('name', 'id');
        return  $units;
    }
}
