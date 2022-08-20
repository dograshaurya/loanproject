<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'emi_due_on',
        'status'
    ];

    public function getCurrentEmi($loan_id){
        $emiData = Emi::select('amount')->where('loan_id',$loan_id)->where('status',0)->orderBy('id','ASC');
        if($emiData->count() > 0){
            return $emiData->first()->amount;
        }
    }
}
