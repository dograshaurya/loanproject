<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Emi;
use App\Models\Loan;

class SubmitEmiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(Request $request)
    {
        $loanobj = new Loan();
        $emi = new Emi();
        if(!$loanobj->isLoanApproved($request->loan_id)){
            throw new HttpResponseException(response()->json([
                'status'   => 0,
                'message'   => 'Loan is not Approved Yet',
            ]));
        }else{
            $nextEmi = $emi->getCurrentEmi($request->loan_id);
            return [
               'amount' => 'required|numeric|min:'.$nextEmi,
               'loan_id' => 'required',
            ];
        }

       
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'   => 0,
            'message'   => 'Validation errors',
            'data'      => $validator->errors() 
        ]));
    }
}
