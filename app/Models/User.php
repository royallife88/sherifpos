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
            'hr_management' => 'HR Management',
            'purchase_order' => 'Purchase Order',
            'sale' => 'Sale',
            'return' => 'Return',
            'expense' => 'Expense',
            'stock' => 'Stock',
            'quotation_for_customers' => 'Quotation for Customers',
            'coupons_and_gift_cards' => 'Coupons and Gift Cards',
            'loyalty_points' => 'Loyalty Points',
            'sp_module' => 'Sales Promotion',
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
                'product_classification_tree' => 'Product Classification Tree',
                'barcode', 'Barcode',
            ],
            'customer_module' => [
                'customer' => 'Customer',
                'customer_type' => 'Customer Type'
            ],
            'supplier_module' => [
                'supplier' => 'Supplier',
            ],
            'sale' => [
                'pos' => 'POS',
                'pay' => 'Payment',
                'sale' => 'Sale',
                'delivery_list' => 'Delivery List',
                'import' => 'Import',
            ],
            'return' => [
                'sell_return' => 'Sell Return',
                'sell_return_pay' => 'Sell Return Payment',
                'purchase_return' => 'Purchase Return',
                'purchase_return_pay' => 'Purchase Return Payment',
            ],
            'hr_management' => [
                'employee' => 'Employee',
                'jobs' => 'Jobs',
                'leave_types' => 'Leave Type',
                'leaves' => 'Leaves',
                'attendance' => 'Attendance',
                'wages_and_compensation' => 'Wages and Compensation',
                'official_leaves' => 'Offical Leaves',
                'forfeit_leaves' => 'Forfeit Leaves',
            ],
            'purchase_order' => [
                'draft_purchase_order' => 'Draft Purchase Order',
                'purchase_order' => 'Purchase Order',
                'send_to_admin' => 'Send to Admin',
                'send_to_supplier' => 'Send to Supplier'
            ],
            'expenses' => [
                'expense_categories' => 'Expense Categories',
                'expense_beneficiaries' => 'Expense Beneficiaries',
                'expenses' => 'Expenses',
            ],
            'stock' => [
                'add_stock' => 'Add Stock',
                'pay' => 'Pay',
                'remove_stock' => 'Remove Stock',
                'transfer' => 'Transfer',
            ],
            'quotation_for_customers' => [
                'quotation' => 'Quoatation',
            ],
            'coupons_and_gift_cards' => [
                'coupon' => 'Coupon',
                'gift_card' => 'Gift Card',
            ],
            'loyalty_points' => [
                'earning_of_points' => 'Earning of Points',
                'redemption_of_points' => 'Redemption of points',
            ],
            'sp_module' => [
                'sales_promotion' => 'Sales Promotion',
            ],
            'settings' => [
                'store' => 'Store',
                'store_pos' => 'Pos for the stores'
            ],

        ];
    }
}
