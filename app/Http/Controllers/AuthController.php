<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;

use Illuminate\Support\Facades\Auth;

use App\User;

class AuthController extends Controller
{


    public function userChangePassword()
    {
        return view('profile.password');

    }

    public function postUserChangePassword(Request $request) {  
        $data = request()->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_new_password' => 'required|min:8',
        ]);

        if (!(Hash::check($data['current_password'],  Auth::user()->password))) {
                return back()->with('error', 'Your current password is incorrect.');
        }else if($data['new_password'] != $data['confirm_new_password']){
            return back()->with('error', 'Passwords do not match');
        }else{
            User::where('id', Auth::user()->id)->update([
                'password' => bcrypt($data['new_password']),
                'change_password' => 1
            ]);
            return back()->with('status', 'Password was successfully updated.');
        }
    }
}
