<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    public function customer_type()
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function getCustomerTreeArray()
    {
        $customer_types = CustomerType::all();

        $array = [];
        foreach ($customer_types as $type) {
            $customers = [];
            $customers = Customer::where('customer_type_id', $type->id)->get();

            foreach ($customers as $customer) {
                $array[$type->name] = [$customer->id => $customer->name];
            }
        }

        return $array;
    }

    public static function getCustomerArrayWithMobile(){
        return Customer::select('id', DB::raw('CONCAT(name, " ", mobile_number ) as customer_name'))->pluck('customer_name', 'id');
    }
}
