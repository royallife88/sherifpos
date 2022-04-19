<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault(['name' => '']);
    }
    public function edited_by_user()
    {
        return $this->belongsTo(User::class, 'edited_by', 'id')->withDefault(['name' => '']);
    }
    public function deliveryman()
    {
        return $this->belongsTo(Employee::class, 'deliveryman_id')->withDefault(['employee_name' => '']);
    }
}
