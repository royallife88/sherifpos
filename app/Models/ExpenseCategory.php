<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    public function beneficiaries()
    {
        return $this->hasMany(ExpenseBeneficiary::class);
    }
    public function expenses()
    {
        return $this->hasMany(Transaction::class);
    }
}
