<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Developer;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected function redirectTo()
    {
        if (\Auth::user()->hasRole('super-admin')) {
            // return route("admin.developers.index");
            return route('admin.dashboard');
        } elseif (\Auth::user()->hasRole('admin')) {
            return route("admin.dashboard");
        } elseif (\Auth::user()->hasRole('cow')) {
            return route("dev-cow.dashboard");
        } elseif (\Auth::user()->hasRole('dev-admin')) {
            return route("dev-admin.dashboard");
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated(Request $request, $user)
    {
        // Check if Developer is disabled
        if ($user->hasRole('cow')) {
            $developer = Developer::where("id",$user->clerk_of_work->developer_id)->first();
            if ($developer->is_disabled == 1) {
                auth()->logout();
                return back()->with('error', 'The developer has been disabled. Please contact an administrator for more information.');
            }
        } else if ($user->hasRole('dev-admin')) {
            $developer = Developer::where("id",$user->developer_admin->developer_id)->first();
            if ($developer->is_disabled == 1) {
                auth()->logout();
                return back()->with('error', 'The developer has been disabled. Please contact an administrator for more information.');
            }
        }

        // Check if User is disabled
        if ($user->is_disabled == 1) {
            auth()->logout();
            return back()->with('error', 'Your account has been disabled. Please contact an administrator for more information.');
        } else if ($user->verified==0 && $user->change_password ==0) {
            auth()->logout();
            return back()->with('error', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        } else if ($user->verified==1 && $user->change_password ==0) {
            return redirect()->route('profile.password');
        }
        return redirect()->intended($this->redirectPath());
    }

    public function firstTimeLogin()
    {
        return view('first-time-login');

    }

    public function postChangePassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required',
            'current_password' => 'required',
            'password' => 'required|min:8|required_with:confirm_password|same:confirm_password',
        ]);
        
        $existingEmail = User::where('email', $data['email'])->count();
        
        if($existingEmail != 0){
            $user = User::where('email', $data['email'])->first();
            $status = "Your e-mail is already verified. You can now login.";
            
            if (!(Hash::check($data['current_password'], $user->password))) {
                $status = "The current password is incorrect. Password was not successfully updated.";
                return redirect('/first-time-login')->with('warning', $status);
            } else {
                User::where('id', $user->id)->update([
                    'password' => bcrypt($data['password']),
                    'change_password' => true
                ]);
                return redirect('/login')->with('status', $status);
            }
        } else {
            return redirect('/first-time-login')->with('error','Invalid Email or Password' );
        }
    }
}
