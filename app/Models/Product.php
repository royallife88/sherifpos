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
        'discount_customers' => 'json',
        'show_to_customer_types' => 'array',
        'translations' => 'array',

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

    public function alert_quantity_unit()
    {
        return $this->belongsTo(Unit::class, 'alert_quantity_unit_id');
    }
    public function units()
    {
        return $this->belongsToJson(Unit::class, 'multiple_units');
    }
    public function colors()
    {
        return $this->hasManyThrough(Color::class, Variation::class, 'product_id', 'id', 'id', 'color_id');
    }
    public function sizes()
    {
        return $this->hasManyThrough(Size::class, Variation::class, 'product_id', 'id', 'id', 'size_id');
    }
    public function grades()
    {
        return $this->belongsToJson(Grade::class, 'multiple_grades');
    }
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault(['name' => '']);
    }
    public function edited_by_user()
    {
        return $this->belongsTo(User::class, 'edited_by', 'id')->withDefault(['name' => '']);
    }

    public function consumption_products()
    {
        return $this->hasMany(ConsumptionProduct::class, 'raw_material_id', 'id');
    }

    public static function getProductVariationDropDown($is_raw_material = false)
    {
        $variations = Variation::join('products', 'products.id', 'variations.product_id')->select('variations.*')->groupBY('variations.id')->get();
        $variation_dropdown = [];

        foreach ($variations as $variation) {
            $name = $variation->product->name ?? '';
            if ($variation->name != 'Default') {
                $name .= ' - ' . $variation->name;
            }
            $variation_dropdown[$variation->id] = $name;
        }

        return $variation_dropdown;
    }

    public function getNameAttribute($name)
    {
        $translations = !empty($this->translations['name']) ? $this->translations['name'] : [];
        if (!empty($translations)) {
            $lang = session('language');
            if (!empty($translations[$lang])) {
                return $translations[$lang];
            }
        }
        return $name;
    }

    public function translated_name($id, $lang)
    {
        $product = Product::find($id);
        $name = $product->name;
        $translations = !empty($product->translations['name']) ? $product->translations['name'] : [];
        if (!empty($translations)) {
            if (!empty($translations[$lang])) {
                return $translations[$lang];
            }
        }
        return $name;
    }

    public function supplier()
    {
        return $this->hasOneThrough(Supplier::class, SupplierProduct::class, 'product_id', 'id', 'id', 'supplier_id');
    }
}
