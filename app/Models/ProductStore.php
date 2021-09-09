<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStore extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function variation(){
        return $this->belongsTo(Variation::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
