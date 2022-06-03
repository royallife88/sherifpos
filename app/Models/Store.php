<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function store_pos()
    {
        return $this->hasMany(StorePos::class);
    }

    public static function getDropdown()
    {
        if (session('user.is_superadmin') || session('user.is_admin')) {
            $stores = Store::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        } else {
            $employee = Employee::where('user_id', auth()->user()->id)->first();
            $stores = Store::whereIn('id', (array) $employee->store_id)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        }
        return $stores;
    }
}
