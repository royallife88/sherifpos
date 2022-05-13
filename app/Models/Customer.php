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


    public function customer_important_dates()
    {
        return $this->hasMany(CustomerImportantDate::class);
    }
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

            $array[$type->name] = [];
            foreach ($customers as $customer) {
                $array[$type->name][$customer->id] = $customer->name;
            }
        }

        return $array;
    }

    public static function getCustomerArrayWithMobile()
    {
        $customers = Customer::select('id', 'name', 'mobile_number')->get();
        $customer_array = [];
        foreach ($customers as $customer) {
            $customer_array[$customer->id] = $customer->name . ' ' . $customer->mobile_number;
        }

        return $customer_array;
    }

    public static function referred_by_users($id)
    {
        $referred_by_users = [];
        $referreds = Referred::where('customer_id', $id)->get();
        foreach ($referreds as $referred) {
            foreach ($referred->referred_by as $referred_by) {
                if ($referred->referred_type == 'customer') {
                    $customer = Customer::where('id', $referred_by)->first();
                    $referred_by_users[] = $customer->name ?? '';
                }
                if ($referred->referred_type == 'employee') {
                    $employee = Employee::where('id', $referred_by)->first();
                    $referred_by_users[] = $employee->employee_name ?? '';
                }
                if ($referred->referred_type == 'supplier') {
                    $supplier = Supplier::where('id', $referred_by)->first();
                    $referred_by_users[] = $supplier->name ?? '';
                }
            }
        }

        return implode(', ', $referred_by_users);
    }
}
