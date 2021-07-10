<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model  implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

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

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function sender_store()
    {
        return $this->belongsTo(Store::class, 'sender_store_id');
    }

    public function receiver_store()
    {
        return $this->belongsTo(Store::class, 'receiver_store_id');
    }
    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
    public function expense_beneficiary()
    {
        return $this->belongsTo(ExpenseBeneficiary::class);
    }

    public function transaction_payments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function return_parent()
    {
        return $this->hasOne(Transaction::class, 'return_parent_id');
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
}
