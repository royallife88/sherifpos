<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Supplier extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function supplier_category()
    {
        return $this->belongsTo(SupplierCategory::class);
    }
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
