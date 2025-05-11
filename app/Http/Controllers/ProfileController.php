<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'title' => trans('general.profile'),
            'description' => trans('general.change_password'),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
       
        $this->validate($request, [
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail( __('general.current_password_does_not_match') );
                    }
                }
            ],
        ]);

        auth()->user()->update([
            'password' => $request->password
        ]);

        if (auth('teacher')->check()) {
            return redirect('/teacher');
            $request->session()->flash('success', 'Your password has been updated successfully.');
        }
        if (auth('student')->check()) {
            session::flash('success', trans('general.password_reset_successfully'));

            return redirect(route('student.profile.password'));
        }


        return redirect('/');
    }
}
