<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

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
        'multiple_units' => 'json',
        'multiple_colors' => 'array',
        'multiple_sizes' => 'array',
        'multiple_grades' => 'array',
        'discount_customer_types' => 'array',
        'discount_customers' => 'array',
        'show_to_customer_types' => 'array',
        // 'manufacturing_date' => 'datetime:m/d/Y',

    ];

    public function scopeActive($query)
    {
        $query->where('active', 1);
    }
    public function scopeNotActive($query)
    {
        $query->where('active', 0);
    }
    public function product_class()
    {
        return $this->belongsTo(ProductClass::class)->withDefault(['name' => '']);
    }
    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault(['name' => '']);
    }
    public function sub_category()
    {
        return $this->belongsTo(Category::class, 'sub_category_id', 'id')->withDefault(['name' => '']);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class)->withDefault(['name' => '']);
    }
    public function tax()
    {
        return $this->belongsTo(Tax::class)->withDefault(['name' => '']);
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }
    public function product_stores()
    {
        return $this->hasMany(ProductStore::class);
    }

    public function units()
    {
        return $this->belongsToJson(Unit::class, 'multiple_units');
    }
    public function colors()
    {
        return $this->belongsToJson(Color::class, 'multiple_colors');
    }
    public function sizes()
    {
        return $this->belongsToJson(Size::class, 'multiple_sizes');
    }
    public function grades()
    {
        return $this->belongsToJson(Grade::class, 'multiple_grades');
    }
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault(['name' => '']);
    }
}
