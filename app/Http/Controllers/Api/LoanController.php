<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Loan\LoanInterface;
use App\Http\Requests\LoanStoreRequest;
use App\Http\Requests\SubmitEmiRequest;
use App\Models\Emi;

class LoanController extends Controller
{
    protected $loan;
    public function __construct(LoanInterface $loan)
    {
        $this->loan = $loan;
    }
    

     /**
     * Method : store
     *
     * @return store loan details i.e store value of amount and terms into database.
     */
    public function store(LoanStoreRequest $request)
    { 
      return $this->loan->store($request);
    }


     /**
     * Method : getLoanDetails
     *
     * @return sync fetch loan details from database.
     */
    public function getLoanDetails(Request $request)
    {
        return $this->loan->getLoanDetails($request);
    }
    

      /**
     * Method : approveLoan
     *
     * @return sync approve loan by admin.
     */
    public function approveLoan(Request $request,$id)
    {
        return $this->loan->approveLoan($id);
    }

     /**
     * Method : submitEmi
     *
     * @return  submit emi i.e store values amount, loan_id after that update status of emi.
     */

    public function submitEmi(SubmitEmiRequest $request)
    {
        $amount = $request->amount;
        $loan_id = $request->loan_id;
        return $this->loan->submitEmi($amount,$loan_id);
    }
}
