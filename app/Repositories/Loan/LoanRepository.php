<?php
namespace App\Repositories\Loan;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Models\Loan;
use Auth;
use App\Models\Emi;
use Illuminate\Support\Facades\Log;
use Exception;

class LoanRepository implements LoanInterface{
    use ApiResponser;

     /**
     * Method : store
     *
     * @param  mixed $request
     * @return store the loan request by user
     */
    public function store($request)
    {
        try {
            $user_id = auth()->user()->id;
            $amount = $request->amount;
            $request['user_id'] = $user_id;
            $request['pending_amount'] = $amount;
            $loanRequest = Loan::create($request->toArray());
            return $this->successResponse($loanRequest,__('messages.request_submited'));
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e->getMessage());
        }
    }


     /**
     * Method : calculateLoan
     *
     * @param  mixed $loanId
     * @param  mixed $loanAmount
     * @param  mixed $term
     * @return calculate the loan emi by loan amount and term
     */
    public function calculateLoan($loanId,$loanAmount = 0, $term = 3) {
        try {
            $date = now();
            $user_id = auth()->user()->id;
            $payment = round($loanAmount/$term * 100) / 100;
            for($i =0;$i<$term;$i++){
                $data[$i]['loan_id'] = $loanId;
                $data[$i]['amount'] = $payment;
                $data[$i]['user_id'] = $user_id;
                $data[$i]['emi_due_on'] = date('Y-m-d H:i:s', strtotime("+".($i+1)." weeks"));
        
            }
            return $data;
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e->getMessage());
        }
    }


     /**
     * Method : getLoanDetails
     *
     * @return the loan details associated with the user.
     */
    public function getLoanDetails()
    {
        try {
            $user_id = auth()->user()->id;
            $loanRequest = Loan::with('emis')->where('user_id',$user_id);
            $loanDetails = $loanRequest->get();
            if($loanRequest->count() == 0){
                return $this->successResponse(__('messages.no_loan_found')); 
            }
            return $this->successResponse(['loan'=>$loanRequest],__('messages.loan_fetched'));
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e->getMessage());
        }
    }

    /**
     * Method : approveLoan
     *
     * @param  mixed $id
     * @return approve the loan request, check added for admin.
     */
    public function approveLoan($id)
    {
        try {
            if(auth()->user()->role == 1){
                $user_details = Loan::where('id',$id);
                $user_details->update(['status' => 1]);
                if($user_details){
                    $getLoanDetails = $user_details->first();
                    $loanRequest = $this->calculateLoan($id,$getLoanDetails->amount,$getLoanDetails->term);
                    Emi::insert($loanRequest);
                    return $this->successResponse('',__('messages.loan_approved'));
                }else{
                    return $this->successResponse(__('messages.record_not_found')); 
                }   
            }
            else{
                return response(['message'=>__('messages.not_admin')]);
            }
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e->getMessage());
        }

    }

    /**
     * Method : submitEmi
     *
     * @param  mixed $amount
     * @param  mixed $loan_id
     * @return submit emi by using parameters amount and loan id.
     */
    public function submitEmi($amount,$loanId)
    {
        try {
            $loan = Loan::where('id',$loanId)->where('status',1);
            $loanAmount = $loan->first();
            if(isset($loanAmount) && $loanAmount->pending_amount > 0)
            {
                    if($amount > $loanAmount->pending_amount)
                    {
                        return $this->errorResponse(__('messages.correct_amount'));
                    }
                    $pendingAmount = $loanAmount->pending_amount - $amount;
                    $loan->update(['pending_amount'=> $pendingAmount]);
                    $response = [];

                    $total_amount = $loanAmount->pending_amount; // 10000 
                    $term= $loanAmount->term;
                    $payment = $amount;
                    
                    $loanRequest1 = Emi::where('loan_id',$loanId)->where('status',0)->orderBy('id','ASC')->first();
                    
                    $loanRequest1->update(['status'=>1]);

                    $pending_amount  = $total_amount - $payment; //6000

                    if($pending_amount > 0){
                        $pendingEmiCount = Emi::where('loan_id',$loanId)->where('status',0)->count();
                        $payment = round($pending_amount/$pendingEmiCount * 100) / 100;
                        $loanRequest2 = Emi::where('loan_id',$loanId)->where('status',0);
                        $loanRequest2->update(['amount'=>$payment]);
                    }

                    if($loanAmount->pending_amount == 0)
                    {
                        $loanRequest2->update(['status'=>1]);
                        return $this->errorResponse(__('messages.not_admin'));
                    }else{
                        return $this->successResponse('',__('messages.emi_submited'));
                    }
            }else{
                return $this->successResponse('',__('messages.no_pending_amount'));
            }
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e);
        }
    }
}
?>