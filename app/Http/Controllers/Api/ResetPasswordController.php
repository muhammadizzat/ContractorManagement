<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;

use App\Services\UserManager;
use App\User;


class ResetPasswordController extends Controller
{
    public static function resetUserPassword($user_id) 
    {
        $pw = str_random(8);
        $user = User::where('id', $user_id)->first();
        $user->change_password = 0;
        $user->password = bcrypt($pw);
        $user->save();

        // Send email
        Mail::send('emails.resetPassword', ['user' => $user, 'pw' => $pw], function ($m) use ($user) {
            $m->from('hello@appsite.com', 'LinkZZapp');
            $m->to($user->email, $user->name)->subject('Welcome to APP');
        });
        return response()->json('User password successfully changed');
    }
}
