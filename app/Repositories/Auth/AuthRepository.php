<?php
namespace App\Repositories\Auth;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthRepository implements AuthInterface{
    use ApiResponser;

     /**
     * Method : register
     *
     * @param  mixed $data
     * @return store the user with fiels i.e email, role, password and name
     */
    public function register($data)
    {
        try {
            $data['password'] = Hash::make($data['password']);
            $data['role'] = isset($data['role']) ? $data['role']  : 0;
            $user = User::create($data);
            $token = $user->createToken('Loan Api Token')->accessToken;
            $userData = $this->sendUserResponse($user,$token);
            return $this->successResponse($userData,__('messages.user_registered'));
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e->getMessage());
        }
    }
   
    /**
     * Method : login
     *
     * @param  mixed $data
     * @return logged in the user and generate a token to access all other api's
     */
    public function login($data)
    { 
        try {
            if (!auth()->attempt($data)) {
                return $this->errorResponse(__('messages.incorrect_credentials'),422);
            }
            $token=auth()->user()->createToken('Loan Api Token')->accessToken;
            $userData = $this->sendUserResponse(auth()->user(),$token);
            $userData['access_token'] = $token;
            return $this->successResponse($userData,__('messages.user_loggedin')); 
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e->getMessage());
        }
    }

    /**
     * Method : sendUserResponse
     *
     * @param  mixed $user
     * @param  mixed $token
     * @return common user response for login and register function.
     */
    public function sendUserResponse($user,$token){
        $userData = [];
        $userData['user_id'] = $user['id'];
        $userData['name'] = $user['name'];
        $userData['email'] = $user['email'];
        $userData['role'] = $user['role'];
        $userData['access_token'] = $token;
        return $userData;
    }

     /**
     * Method : logout
     *
     * @param  mixed $request
     * @return common user response for login and register function.
     */
    public function logout($request){

        try {
            if($request->user()){
                $token = $request->user()->token() ;
                $token->revoke();
                return $this->successResponse('',__('messages.user_loggedout')); 
            }
            else{
                return $this->errorResponse(__('messages.something_went_wrong')); 
            }
        } catch (\Exception $e) {
            Log::error('Catch Response Error: '.$e->getMessage());
        }

    }

}

?>