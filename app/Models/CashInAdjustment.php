<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashInAdjustment extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function cash_register()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
