<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function received_currency()
    {
        return $this->belongsTo(Currency::class, 'received_currency_id');
    }
    public function default_currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => '']);
    }
}
