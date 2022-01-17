<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCustomerSize extends Model
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
}
