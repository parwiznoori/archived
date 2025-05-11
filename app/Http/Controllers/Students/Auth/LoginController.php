<?php

namespace App\Http\Controllers\Students\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $guard = 'student';
    protected $redirectTo = '/student/profile';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login', ['guard' => $this->guard]);
    }

    protected function guard()
    {
        return auth()->guard($this->guard);
    }
}
