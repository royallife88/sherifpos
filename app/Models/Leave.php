<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Leave extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function getLeaveTypes()
    {
        return [
            'annual_leave' => __('lang.annual_leave'),
            'sick_leave' => __('lang.sick_leave'),
            'maternity_leave' => __('lang.maternity_leave'),
            'other_leave' => __('lang.other_leave')
        ];
    }
}
