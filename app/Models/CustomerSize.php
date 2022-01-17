<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSize extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $casts = [
        'yoke' => 'array',
        'neck_round' => 'array',
        'neck_width' => 'array',
        'neck_deep' =>  'array',
        'front_neck' =>  'array',
        'back_neck' =>  'array',
        'upper_bust' =>  'array',
        'bust' =>  'array',
        'low_bust' =>  'array',
        'shoulder_er' =>  'array',
        'arm_hole' =>  'array',
        'arm_round' =>  'array',
        'wrist_round' =>  'array',
        'lenght_of_sleeve' =>  'array',
        'waist' =>  'array',
        'low_waist' =>  'array',
        'hips' =>  'array',
        'thigh' =>  'array',
        'knee_round' =>  'array',
        'calf_round' =>  'array',
        'ankle' =>  'array',

    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault(['name' => '']);
    }

    public static function getAttributeListArray()
    {
        return [
            'yoke' => __('lang.yoke'),
            'neck_round' => __('lang.neck_round'),
            'neck_width' => __('lang.neck_width'),
            'neck_deep' =>  __('lang.neck_deep'),
            'front_neck' =>  __('lang.front_neck'),
            'back_neck' =>  __('lang.back_neck'),
            'upper_bust' =>  __('lang.upper_bust'),
            'bust' =>  __('lang.bust'),
            'low_bust' =>  __('lang.low_bust'),
            'shoulder_er' =>  __('lang.shoulder_er'),
            'arm_hole' =>  __('lang.arm_hole'),
            'arm_round' =>  __('lang.arm_round'),
            'wrist_round' =>  __('lang.wrist_round'),
            'lenght_of_sleeve' =>  __('lang.lenght_of_sleeve'),
            'waist' =>  __('lang.waist'),
            'low_waist' =>  __('lang.low_waist'),
            'hips' =>  __('lang.hips'),
            'thigh' =>  __('lang.thigh'),
            'knee_round' =>  __('lang.knee_round'),
            'calf_round' =>  __('lang.calf_round'),
            'ankle' =>  __('lang.ankle'),
        ];
    }
}
