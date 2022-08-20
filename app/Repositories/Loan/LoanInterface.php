<?php

namespace App\Repositories\Loan;


interface LoanInterface{
    public function store($request);
    public function calculateLoan($loanId,$loanAmount = 0, $term = 3);
    public function getLoanDetails(); 
    public function approveLoan($id);
    public function submitEmi($amount,$loanId);
}

?>