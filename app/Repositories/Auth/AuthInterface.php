<?php

namespace App\Repositories\Auth;


interface AuthInterface{
    public function register($data);
    public function login($data);
    public function logout($request);
}

?>