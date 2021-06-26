<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function modulePermissionArray()
    {
        return [
            'product_module' => 'Product Module',
            'customer_module' => 'Customer Module',
            'supplier_module' => 'Supplier Module',
            'purchase_order' => 'Purchase Order',
            'stock' => 'Stock',
            'settings' => 'Settings',
        ];
    }
    public static function subModulePermissionArray()
    {
        return [
            'product_module' => [
                'product' => 'Product',
                'product_class' => 'Class',
                'category' => 'Category',
                'sub_category' => 'Sub Category',
                'brand' => 'Brand',
                'unit' => 'Unit',
                'color' => 'Color',
                'size' => 'Size',
                'grade' => 'Grade',
                'tax' => 'Tax',
                'product_classification_tree', 'Product Classification Tree',
                'barcode', 'Barcode',
            ],
            'customer_module' => [
                'customer' => 'Customer',
                'customer_type' => 'Customer Type'
            ],
            'supplier_module' => [
                'supplier' => 'Supplier',
            ],
            'purchase_order' => [
                'draft_purchase_order' => 'Draft Purchase Order',
                'purchase_order' => 'Purchase Order',
                'send_to_admin' => 'Send to Admin',
                'send_to_supplier' => 'Send to Supplier'
            ],
            'stock' => [
                'add_stock' => 'Add Stock',
            ],
            'settings' => [
                'store' => 'Store'
            ],

        ];
    }
}
