<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoanController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/register', [AuthController::class,'register'])->name('register.api');
Route::post('/login', [AuthController::class,'login'])->name('login');
 

Route::group(['middleware'=>['auth:api']],function(){
     Route::post('/request-loan',[LoanController::class,'store']);
     Route::get('/get-loan-details',[LoanController::class,'getLoanDetails']);
     Route::post('/approve-loan/{id}',[LoanController::class,'approveLoan']);
     Route::post('/submit-emi',[LoanController::class,'submitEmi']);
     Route::post('/logout', [AuthController::class,'logout'])->name('logout.api'); 
});