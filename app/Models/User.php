<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

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
            'dashboard' => __('lang.dashboard'),
            'product_module' => __('lang.product_module'),
            'raw_material_module' => __('lang.raw_material_module'),
            'customer_module' => __('lang.customer_module'),
            'supplier_module' => __('lang.supplier_module'),
            'service_provider' => __('lang.service_provider'),
            'hr_management' => __('lang.hr_management'),
            'purchase_order' => __('lang.purchase_order'),
            'sale' => __('lang.sale'),
            'return' => __('lang.return'),
            'expense' => __('lang.expense'),
            'stock' => __('lang.stock'),
            'cash' => __('lang.cash'),
            'adjustment' => __('lang.adjustment'),
            'reports' => __('lang.reports'),
            'quotation_for_customers' => __('lang.quotation_for_customers'),
            'coupons_and_gift_cards' => __('lang.coupons_and_gift_cards'),
            'loyalty_points' => __('lang.loyalty_points'),
            'sp_module' => __('lang.sp_module'),
            'safe_module' => __('lang.safe_module'),
            'sms_module' => __('lang.sms_module'),
            'email_module' => __('lang.email_module'),
            'settings' => __('lang.settings'),
        ];
    }
    public static function subModulePermissionArray()
    {
        return [
            'dashboard' => [
                'profit' => __('lang.sales_and_returns'),
                // 'details' => __('lang.details'),
            ],
            'product_module' => [
                'product' => __('lang.product'),
                'product_class' => session('system_mode') == 'restaurant' ? __('lang.category') : __('lang.product_class'),
                'category' => __('lang.category'),
                'sub_category' => __('lang.sub_category'),
                'brand' => __('lang.brand'),
                'unit' => __('lang.unit'),
                'color' => __('lang.unit'),
                'size' => __('lang.size'),
                'grade' => __('lang.grade'),
                'tax' => __('lang.tax'),
                'product_classification_tree' => __('lang.product_classification_tree'),
                'barcode' => __('lang.barcode'),
                'purchase_price' => __('lang.purchase_price'),
                'sell_price' => __('lang.sell_price'),
            ],
            'raw_material_module' => [
                'raw_material' => __('lang.raw_material'),
                'consumption' => __('lang.consumption'),
                'add_consumption_for_others' => __('lang.add_consumption_for_others'),
                'add_stock_for_raw_material' => __('lang.add_stock_for_raw_material'),
                'remove_stock' => __('lang.remove_stock'),
                'transfer' => __('lang.transfer'),
                'internal_stock_request' => __('lang.internal_stock_request'),
                'internal_stock_return' => __('lang.internal_stock_return'),
            ],
            'customer_module' => [
                'customer' => __('lang.customer'),
                'customer_type' => __('lang.customer_type'),
                'add_payment' => __('lang.add_payment'),
                'customer_sizes' => __('lang.customer_sizes')
            ],
            'supplier_module' => [
                'supplier' => __('lang.supplier'),
                'category' => __('lang.category'),
            ],
            'service_provider' => [
                'supplier_services' => __('lang.supplier_services'),
                'cancel_service' => __('lang.cancel_service'),
            ],
            'sale' => [
                'pos' => __('lang.pos'),
                'pay' => __('lang.payment'),
                'sale' => __('lang.sale'),
                'delivery_list' => __('lang.delivery_list'),
                'import' => __('lang.import'),
            ],
            'return' => [
                'sell_return' => __('lang.sell_return'),
                'sell_return_pay' => __('lang.sell_return_pay'),
                'purchase_return' => __('lang.purchase_return'),
                'purchase_return_pay' => __('lang.purchase_return_pay'),
            ],
            'hr_management' => [
                'employee' => __('lang.employee'),
                'employee_commission' => __('lang.employee_commission'),
                'suspend' => __('lang.suspend'),
                'send_credentials' => __('lang.send_credentials'),
                'jobs' => __('lang.jobs'),
                'leave_types' => __('lang.leave_types'),
                'leaves' => __('lang.leaves'),
                'attendance' => __('lang.attendance'),
                'wages_and_compensation' => __('lang.wages_and_compensation'),
                'official_leaves' => __('lang.official_leaves'),
                'forfeit_leaves' => __('lang.forfeit_leaves'),
            ],
            'purchase_order' => [
                'draft_purchase_order' => __('lang.draft_purchase_order'),
                'purchase_order' => __('lang.purchase_order'),
                'send_to_admin' => __('lang.send_to_admin'),
                'send_to_supplier' => __('lang.send_to_supplier'),
                'import' => __('lang.import')
            ],
            'expense' => [
                'expense_categories' => __('lang.expense_categories'),
                'expense_beneficiaries' => __('lang.expense_beneficiaries'),
                'expenses' => __('lang.expenses'),
            ],
            'stock' => [
                'add_stock' => __('lang.add_stock'),
                'pay' => __('lang.pay'),
                'remove_stock' => __('lang.remove_stock'),
                'internal_stock_request' => __('lang.internal_stock_request'),
                'internal_stock_return' => __('lang.internal_stock_return'),
                'transfer' => __('lang.transfer'),
                'import' => __('lang.import'),
            ],
            'quotation_for_customers' => [
                'quotation' => __('lang.quotation'),
            ],
            'coupons_and_gift_cards' => [
                'coupon' => __('lang.coupon'),
                'gift_card' => __('lang.gift_card'),
            ],
            'loyalty_points' => [
                'earning_of_points' => __('lang.earning_of_points'),
                'redemption_of_points' => __('lang.redemption_of_points'),
            ],
            'reports' => [
                'profit_loss' => __('lang.profit_loss'),
                'daily_sales_summary' => __('lang.daily_sales_summary'),
                'receivable_report' => __('lang.receivable_report'),
                'payable_report' => __('lang.payable_report'),
                'expected_receivable_report' => __('lang.expected_receivable_report'),
                'expected_payable_report' => __('lang.expected_payable_report'),
                'summary_report' => __('lang.summary_report'),
                'dining_in_sales' => __('lang.dining_in_sales'),
                'sales_per_employee' => __('lang.sales_per_employee'),
                'best_seller_report' => __('lang.best_seller_report'),
                'product_report' => __('lang.product_report'),
                'daily_sale_report' => __('lang.daily_sale_report'),
                'monthly_sale_report' => __('lang.monthly_sale_report'),
                'daily_purchase_report' => __('lang.daily_purchase_report'),
                'monthly_purchase_report' => __('lang.monthly_purchase_report'),
                'sale_report' => __('lang.sale_report'),
                'purchase_report' => __('lang.purchase_report'),
                'store_report' => __('lang.store_report'),
                'store_stock_chart' => __('lang.store_stock_chart'),
                'product_quantity_alert_report' => __('lang.product_quantity_alert_report'),
                'user_report' => __('lang.user_report'),
                'customer_report' => __('lang.customer_report'),
                'supplier_report' => __('lang.supplier_report'),
                'due_report' => __('lang.due_report'),
            ],
            'cash' => [
                'add_cash_in' => __('lang.add_cash_in'),
                'add_closing_cash' => __('lang.add_closing_cash'),
                'add_cash_out' => __('lang.add_cash_out'),
                'view_details' => __('lang.view_details'),
            ],
            'adjustment' => [
                'cash_in_adjustment' => __('lang.cash_in_adjustment'),
                'cash_out_adjustment' => __('lang.cash_out_adjustment'),
                'customer_balance_adjustment' => __('lang.customer_balance_adjustment'),
                'customer_point_adjustment' => __('lang.customer_point_adjustment'),
            ],
            'sp_module' => [
                'sales_promotion' => __('lang.sales_promotion'),
            ],
            'safe_module' => [
                'money_safe' => __('lang.money_safe'),
                'add_money_to_safe' => __('lang.add_money_to_safe'),
                'take_money_from_safe' => __('lang.take_money_from_safe'),
                'statement' => __('lang.statement'),
            ],
            'sms_module' => [
                'sms' => __('lang.sms'),
                'setting' => __('lang.setting'),
            ],
            'email_module' => [
                'email' => __('lang.email'),
                'setting' => __('lang.setting'),
            ],
            'settings' => [
                'store' => __('lang.store'),
                'store_pos' => __('lang.store_pos'),
                'dining_room' => __('lang.dining_room'),
                'dining_table' => __('lang.dining_table'),
                'service_fee' => __('lang.service_fee'),
                'delivery_zone' => __('lang.delivery_zone'),
                'delivery_zone_cost' => __('lang.delivery_zone_cost'),
                'exchange_rate' => __('lang.exchange_rate'),
                'terms_and_conditions' => __('lang.terms_and_conditions'),
                'modules' => __('lang.modules'),
                'weighing_scale_setting' => __('lang.weighing_scale_setting'),
                'general_settings' => __('lang.general_settings'),
            ],

        ];
    }
}
