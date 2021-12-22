<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    use HasFactory, \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function customer_type_store()
    {
        return $this->hasMany(CustomerTypeStore::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function getDropdown()
    {
        return CustomerType::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }
}
