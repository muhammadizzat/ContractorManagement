<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

use DB;
use Auth;
use Mail;

use App\User;
use App\Services\UserManager;
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    // protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest');
    // }

    public static function postResetUserPassword($user_id) 
    {  
        $user = User::find($user_id);
        if(auth()->user()->roles[0]->name == 'admin' || auth()->user()->roles[0]->name == 'super-admin'){
            $response = UserManager::resetUserPassword($user_id);
            return response()->json($response);
        } else if(auth()->user()->roles[0]->name == 'dev-admin'){
            if($user->roles[0]->name == 'dev-admin' || $user->roles[0]->name == 'cow'){
                $response = UserManager::resetUserPassword($user_id);
                return response()->json($response);
            } else {
                return response()->json('You do not have the permission to reset this user password');
            }
        } else if(auth()->user()->roles[0]->name == 'cow'){
            if($user->roles[0]->name == 'cow'){
                $response = UserManager::resetUserPassword($user_id);
                return response()->json($response);
            } else {
                return response()->json('You do not have the permission to reset this user password');
            }
        } else {
            return response()->json("You do not have the permission to reset user's password");
        }
    }
}
