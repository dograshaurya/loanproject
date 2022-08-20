<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'term',
        'user_id',
        'pending_amount',
        'status'
    ];

    public function emis(){
        return $this->hasMany(Emi::class);
    }

    public function isLoanApproved($loan_id){
        return Loan::where('id',$loan_id)->where('status',1)->count();
    }
}
