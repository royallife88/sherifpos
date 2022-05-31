<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndCondition extends Model
{
    use HasFactory;


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    static public function getDropdownInvoice()
    {
        $invoice_terms_and_conditions =  System::getProperty('invoice_terms_and_conditions');

        if(auth()->user()->can('superadmin') || auth()->user()->is_admin == 1){
            $tac = TermsAndCondition::where('type', 'invoice')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        }else{
            $tac = TermsAndCondition::where('type', 'invoice')->where('id', $invoice_terms_and_conditions)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        }

        return $tac;
    }
}
