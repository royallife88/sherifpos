<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

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

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * get the array for dropdown of user by job type
     *
     * @param int $job_type
     * @return void
     */
    public static function getDropdownByJobType($job_type)
    {
        $employees = Employee::leftjoin('job_types', 'employees.job_type_id', 'job_types.id')
            ->leftjoin('users', 'employees.user_id', 'users.id')
            ->where('job_types.job_title', $job_type)
            ->pluck('users.name', 'employees.user_id');

        return $employees;
    }

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
            'cash' => 'Cash',
            'adjustment' => 'Adjustment',
            'reports' => 'Reports',
            'quotation_for_customers' => 'Quotation for Customers',
            'coupons_and_gift_cards' => 'Coupons and Gift Cards',
            'loyalty_points' => 'Loyalty Points',
            'sp_module' => 'Sales Promotion',
            'sms_module' => 'SMS Module',
            'email_module' => 'Email Module',
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
                'barcode' => 'Barcode',
                'purchase_price' => 'Purchase Price',
            ],
            'customer_module' => [
                'customer' => 'Customer',
                'customer_type' => 'Customer Type',
                'add_payment' => 'Add Payment'
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
                'suspend' => 'Suspend',
                'send_credentials' => 'Send Credentials',
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
                'send_to_supplier' => 'Send to Supplier',
                'import' => 'Import'
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
                'import' => 'Import',
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
            'reports' => [
                'profit_loss' => 'Profit / Loss Report',
                'receivable_report' => 'Receivable Report',
                'payable_report' => 'Payable Report',
                'expected_receivable_report' => 'Expected Receivable Report',
                'expected_payable_report' => 'Expected Payable Report',
                'summary_report' => 'Summary Report',
                'best_seller_report' => 'Best Seller Report',
                'product_report' => 'Product Report',
                'daily_sale_report' => 'Daily Sale Report',
                'monthly_sale_report' => 'Monthly Sale Report',
                'daily_purchase_report' => 'Monthly Purchase Report',
                'monthly_purchase_report' => 'Monthly Purchase Report',
                'sale_report' => 'Sale Report',
                'purchase_report' => 'Purchase Report',
                'store_report' => 'Store Report',
                'store_stock_chart' => 'Store Stock Chart',
                'product_quantity_alert_report' => 'Product Quantity Alert Report',
                'user_report' => 'User Report',
                'customer_report' => 'Customer Report',
                'supplier_report' => 'Supplier Report',
                'due_report' => 'Due Report',
            ],
            'cash' => [
                'add_cash_in' => 'Add Cash In',
                'add_closing_cash' => 'Add Closing Cash',
                'add_cash_out' => 'Add Cash Out',
                'view_details' => 'View Details',
            ],
            'adjustment' => [
                'cash_in_adjustment' => 'Cash In Adjustment',
                'cash_out_adjustment' => 'Cash Out Adjustment',
                'customer_balance_adjustment' => 'Customer Balance Adjustment',
                'customer_point_adjustment' => 'Customer Points Adjustment',
            ],
            'sp_module' => [
                'sales_promotion' => 'Sales Promotion',
            ],
            'sms_module' => [
                'sms' => 'SMS',
                'setting' => 'Setting',
            ],
            'email_module' => [
                'email' => 'Email',
                'setting' => 'Setting',
            ],
            'settings' => [
                'store' => 'Store',
                'store_pos' => 'Pos for the stores',
                'terms_and_conditions' => 'Terms and Conditions',
                'general_settings' => 'General Settings',
            ],

        ];
    }
}
