<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WagesAndCompensation extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function getPaymentTypes()
    {
        return [
            'salary' => 'Salary',
            'paid_leave' => 'Paid Leave',
            'paid_annual_leave' => 'Paid Annual Leave',
            'commission' => 'Commission',
            'annual_bonus' => 'Annual Bonus',
            'annual_incentive' => 'Annual Incentive',
            'recognition' => 'Recognition',
            'other_reward' => 'Other Reward'
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withDefault(['employee_name', '']);
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
