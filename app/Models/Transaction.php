<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model  implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $appends = ['source_name'];
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
        'commissioned_employees' => 'array'
    ];

    public function getSourceNameAttribute()
    {
        $source_type = $this->source_type;
        $source_id = $this->source_id;
        if (empty($source_id)) {
            return '';
        }

        if ($source_type == 'pos') {
            $source = StorePos::where('id', $source_id)->first();
        }
        if ($source_type == 'user') {
            $source = User::where('id', $source_id)->first();
        }
        if ($source_type == 'store') {
            $source = Store::where('id', $source_id)->first();
        }

        return $source->name ?? null;
    }
    public function purchase_order_lines()
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function add_stock_lines()
    {
        return $this->hasMany(AddStockLine::class);
    }

    public function transaction_sell_lines()
    {
        return $this->hasMany(TransactionSellLine::class);
    }
    public function sell_return()
    {
        return $this->belongsTo(Transaction::class, 'return_parent_id', 'id');
    }

    public function parent_sale()
    {
        return $this->belongsTo(Transaction::class, 'parent_sale_id', 'id');
    }
    public function return_parent()
    {
        return $this->hasOne(Transaction::class, 'return_parent_id');
    }
    public function transaction_customer_size()
    {
        return $this->hasOne(TransactionCustomerSize::class);
    }
    public function service_fee()
    {
        return $this->belongsTo(ServiceFee::class);
    }
    public function canceled_by_user()
    {
        return $this->belongsTo(User::class, 'canceled_by', 'id')->withDefault(['name' => '']);
    }
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault(['name' => '']);
    }
    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id')->withDefault(['name' => '']);
    }
    public function requested_by_user()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id')->withDefault(['name' => '']);
    }
    public function received_by_user()
    {
        return $this->belongsTo(User::class, 'received_by', 'id')->withDefault(['name' => '']);
    }
    public function declined_by_user()
    {
        return $this->belongsTo(User::class, 'declined_by', 'id')->withDefault(['name' => '']);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withDefault(['employee_name' => '']);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withDefault(['name' => '']);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withDefault(['name' => '']);
    }

    public function add_stock_variations()
    {
        return $this->hasManyThrough(Product::class, AddStockLine::class, 'transaction_id', 'id', 'id', 'variation_id');
    }

    public function add_stock_products()
    {
        return $this->hasManyThrough(Product::class, AddStockLine::class, 'transaction_id', 'id', 'id', 'product_id');
    }

    public function sell_products()
    {
        return $this->hasManyThrough(Product::class, TransactionSellLine::class, 'transaction_id', 'id', 'id', 'product_id');
    }

    public function sell_variations()
    {
        return $this->hasManyThrough(Variation::class, TransactionSellLine::class, 'transaction_id', 'id', 'id', 'variation_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id')->withDefault(['name' => '']);
    }

    public function sender_store()
    {
        return $this->belongsTo(Store::class, 'sender_store_id')->withDefault(['name' => '']);
    }

    public function receiver_store()
    {
        return $this->belongsTo(Store::class, 'receiver_store_id')->withDefault(['name' => '']);
    }
    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class)->withDefault(['name' => '']);
    }
    public function expense_beneficiary()
    {
        return $this->belongsTo(ExpenseBeneficiary::class)->withDefault(['name' => '']);
    }
    public function customer_size()
    {
        return $this->belongsTo(CustomerSize::class);
    }

    public function transaction_payments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function add_stock_parent()
    {
        return $this->hasOne(Transaction::class, 'add_stock_id');
    }

    public function purchase_return_lines()
    {
        return $this->hasMany(PurchaseReturnLine::class);
    }

    public function transfer_lines()
    {
        return $this->hasMany(TransferLine::class);
    }

    public function remove_stock_lines()
    {
        return $this->hasMany(RemoveStockLine::class);
    }

    public function terms_and_conditions()
    {
        return $this->belongsTo(TermsAndCondition::class, 'terms_and_condition_id');
    }

    public function source()
    {
        return $this->belongsTo(User::class, 'source_id')->withDefault(['name' => '']);
    }
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withDefault(['name' => '']);
    }
    public function deliveryman()
    {
        return $this->belongsTo(Employee::class, 'deliveryman_id', 'id')->withDefault(['employee_name' => '']);
    }
    public function wages_and_compensation()
    {
        return $this->belongsTo(WagesAndCompensation::class);
    }
    public function dining_table()
    {
        return $this->belongsTo(DiningTable::class);
    }
    public function dining_room()
    {
        return $this->belongsTo(DiningRoom::class);
    }
    public function received_currency()
    {
        $default_currency_id = System::getProperty('currency');
        $default_currency = Currency::where('id', $default_currency_id)->first();

        return $this->belongsTo(Currency::class, 'received_currency_id')->withDefault($default_currency);
    }
    public function paying_currency()
    {
        $default_currency_id = System::getProperty('currency');
        $default_currency = Currency::where('id', $default_currency_id)->first();

        return $this->belongsTo(Currency::class, 'paying_currency_id')->withDefault($default_currency);
    }
}
