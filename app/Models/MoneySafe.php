<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneySafe extends Model
{
    use HasFactory;

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
        'add_money_users' => 'array',
        'take_money_users' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault(['name' => '']);
    }
    public function edited_by_user()
    {
        return $this->belongsTo(User::class, 'edited_by', 'id')->withDefault(['name' => '']);
    }
}
