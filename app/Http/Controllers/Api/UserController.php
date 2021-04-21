<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\User;

use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    public $successStatus = 200;

    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /** 
     * contractor login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function contractorLogin()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->hasRole('contractor')) {
                $success['token'] = $user->createToken('MyApp')->accessToken;
                return response()->json(['success' => $success], $this->successStatus);
            }
        } 

        return response()->json(['error' => 'Unauthorised'], 401);
    }

    /** 
     * clerk of work login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function clerkOfWorkLogin()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->hasRole('cow')) {
                $success['token'] = $user->createToken('MyApp')->accessToken;
                return response()->json(['success' => $success], $this->successStatus);
            }
        } 
        
        return response()->json(['error' => 'Unauthorised'], 401);
    }

    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function info()
    {
        $user = Auth::user();

        return response()->json($user, $this->successStatus);
    }
}
