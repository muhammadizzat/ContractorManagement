<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserManager;
use App\User;
use App\VerifyUser;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |

    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address_1' => ['required'],
            'address_2' => ['required'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'postal_code' => ['required', 'integer', 'max:5', 'min:5'],
            'contact_no' => ['required', 'integer']
        ]);
    }

    public function postAddContractor(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL|email',
            'address_1' => 'required',
            'address_2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postal_code' => 'required|numeric|digits_between:1,10',
            'contact_no' => 'required|numeric',     
            ]);

        $returnMessage;
        try {
            $success = UserManager::createContractorUser($data['name'], $data['email'], 'contractor', $data['address_1'], $data['address_2'], $data['city'], $data['state'], $data['postal_code'], $data['contact_no']);
        } catch (Exception $e) {
            $returnMessage = $e->getMessage();
        }

        return redirect('/login')->withStatus(__($returnMessage ?? 'You have successfully created a Contractor account. Please follow the instructions sent to the email you provided before login.'));
    }

    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (!empty($verifyUser)) {
            $user = $verifyUser->user;
            if (!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $user->change_password = 0;
                $user->save();

                $status = "Please update your password to a new one.";
            } else {
                $status = "Please update your password to a new one.";
            }
            return redirect('/first-time-login')->with('status', $status);
        } else {
            return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }

        // return redirect('/login')->with('status', $status);
    }

    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();
        return redirect('/login')->with('status', 'We sent you an activation code. Check your email and click on the link to verify.');
    }
}
