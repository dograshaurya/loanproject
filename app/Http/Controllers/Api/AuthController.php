<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Auth\AuthInterface;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginRequest;
use App\Traits\ApiResponser;

class AuthController extends Controller
{
    use ApiResponser;
    protected $auth;
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }
    
     /**
     * Method : register
     *
     * @return sync store user details i.e. name,email,password in database.
     */
    public function register(RegisterUserRequest $request)
    {
        $data = $request->all();
        return $this->auth->register($data);
    }
    
     /**
     * Method : login
     *
     * @return sync login user using valid email and password.
     */
    public function login(LoginRequest $request)
    {
        $data = $request->all();
        return $this->auth->login($data);
    }

     /**
     * Method : logout
     *
     * @return sync logout user.
     */
    public function logout(Request $request)
    {
        return $this->auth->logout($request); 
    }
}
