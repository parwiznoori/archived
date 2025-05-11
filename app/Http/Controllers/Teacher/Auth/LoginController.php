<?php

namespace App\Http\Controllers\Teacher\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $guard = 'teacher';
    protected $redirectTo = '/teacher/noticeboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        
        return view('auth.login', ['guard' => $this->guard]);
    }

    protected function credentials($request)
    {
        return array_merge($request->only($this->username(), 'password'), ['active' => 1]);
    }

    protected function guard()
    {
        return auth()->guard($this->guard);
    }


    // public function login(){
    //     dd('djhfjdhf');
    // }
}
