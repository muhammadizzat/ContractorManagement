<?php

namespace App\Services;

use DB;
use Auth;
use Mail;

use App\User;
use App\VerifyUser;
use App\DeveloperAdmin;
use App\ClerkOfWork;
use App\Contractor;

class UserManager
{
    private static function userExists($email)
    {
        return User::where('email', '=', $email)->where('deleted_at', null)->count() > 0;
    }

    public static function createAdminUser($name, $email)
    {
        if (self::userExists($email)) {
            throw new Exception('User with this email already exists.');
        }

        $user = null;
        // Generate random temporary password
        $pw = str_random(8);
        DB::transaction(function () use ($name, $email, $pw, $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($pw),
            ]);
            $user->assignRole('admin');

            // User verification
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => str_random(40),
            ]);

            // Send email
            $token = app('auth.password.broker')->createToken($user);
            Mail::send('emails.welcome', ['user' => $user, 'token' => $token, 'pw' => $pw], function ($m) use ($user) {
                $m->from('hello@appsite.com', 'LinkZZapp');
                $m->to($user->email)->subject('Welcome to LinkZZapp');
            });
        });
    }
    public static function createContractorUser($name, $email, $role, $address_1, $address_2, $city, $state, $postal_code, $contact_no)
    {
        if (self::userExists($email)) {
            throw new Exception('User with this email already exists.');
        }

        $user = null;
        // Generate random temporary password
        $pw = str_random(8);
        DB::transaction(function () use ($name, $email, $address_1, $address_2, $city, $state, $postal_code, $contact_no, $pw, $role, $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($pw),
            ]);

            $user->assignRole($role);

            $contractor = Contractor::create([
                'user_id' => $user->id,
                'created_by' => $user->id,
                'address_1' => $address_1,
                'address_2' => $address_2,
                'city' => $city,
                'state' => $state,
                'postal_code' => $postal_code,
                'contact_no' => $contact_no
            ]);

            // User verification
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => str_random(40),
            ]);

            // Send email
            $token = app('auth.password.broker')->createToken($user);
            Mail::send('emails.welcome', ['user' => $user, 'token' => $token, 'pw' => $pw], function ($m) use ($user) {
                $m->from('hello@appsite.com', 'LinkZZapp');
                $m->to($user->email)->subject('Welcome to LinkZZapp');
            });
        });
    }

    public static function createClerkOfWorkUser($name, $email, $dev_id, $role)
    {
        if (self::userExists($email)) {
            throw new Exception('User with this email already exists.');
        }

        $user = null;
        // Generate random temporary password
        $pw = str_random(8);
        DB::transaction(function () use ($name, $email, $pw, $dev_id, $role, $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($pw),
            ]);
            $user->assignRole($role);

            // User type specific entry
            if ($role == 'dev-admin') {
                $developerAdmin = DeveloperAdmin::create([
                    'user_id' => $user->id,
                    'developer_id' => $dev_id,
                    'created_by' => Auth::user()->id,
                ]);
            } else if ($role == 'cow') {
                $clerkOfWork = ClerkOfWork::create([
                    'user_id' => $user->id,
                    'developer_id' => $dev_id,
                    'created_by' => Auth::user()->id,
                ]);
            }
            // User verification
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => str_random(40),
            ]);

            // Send email
            $token = app('auth.password.broker')->createToken($user);
            Mail::send('emails.welcome', ['user' => $user, 'token' => $token, 'pw' => $pw], function ($m) use ($user) {
                $m->from('hello@appsite.com', 'LinkZZapp');
                $m->to($user->email)->subject('Welcome to LinkZZapp');
            });
        });
    }
    public static function createDeveloperUser($name, $email, $dev_id, $role)
    {
        if (self::userExists($email)) {
            throw new Exception('User with this email already exists.');
        }

        $user = null;
        // Generate random temporary password
        $pw = str_random(8);
        DB::transaction(function () use ($name, $email, $pw, $dev_id, $role, $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($pw),
            ]);
            $user->assignRole($role);

            // User type specific entry
            if ($role == 'dev-admin') {
                $developerAdmin = DeveloperAdmin::create([
                    'user_id' => $user->id,
                    'developer_id' => $dev_id,
                    'created_by' => Auth::user()->id,
                ]);
            } else if ($role == 'cow') {
                $clerkOfWork = ClerkOfWork::create([
                    'user_id' => $user->id,
                    'developer_id' => $dev_id,
                    'created_by' => Auth::user()->id,
                ]);
            }
            // User verification
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => str_random(40),
            ]);

            // Send email
            $token = app('auth.password.broker')->createToken($user);
            Mail::send('emails.welcome', ['user' => $user, 'token' => $token, 'pw' => $pw], function ($m) use ($user) {
                $m->from('hello@appsite.com', 'LinkZZapp');
                $m->to($user->email)->subject('Welcome to LinkZZapp');
            });
        });
    }

    public static function createDeveloperPrimaryUser($name, $email, $dev_id, $role) {
        if(self::userExists($email)) {
            throw new Exception('User with this email already exists.');
        }

        $user = null;
        // Generate random temporary password
        $pw = str_random(8);
        DB::transaction(function () use ($name, $email, $pw, $dev_id, $role, $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($pw),
            ]);
            $user->assignRole($role);

            // User type specific entry
            if($role == 'dev-admin') {
                $developerAdmin = DeveloperAdmin::create([
                    'user_id' => $user->id,
                    'developer_id' => $dev_id,
                    'created_by' => Auth::user()->id,
                    'primary_admin' => true,
                ]);
            } else if($role == 'cow') {
                $clerkOfWork = ClerkOfWork::create([
                    'user_id' => $user->id,
                    'developer_id' => $dev_id,
                    'created_by' => Auth::user()->id,
                    'primary_admin' => true,
                ]);
            }
            // User verification
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => str_random(40),
            ]);

            // Send email
            $token = app('auth.password.broker')->createToken($user);
            Mail::send('emails.welcome', ['user' => $user, 'token' => $token, 'pw' => $pw], function ($m) use ($user) {
                $m->from('hello@appsite.com', 'LinkZZapp');
                $m->to($user->email)->subject('Welcome to LinkZZapp');
            }); 
        }); 
    }

    public static function resetUserPassword($user_id) {
        if($user_id != auth()->user()->id){
            $pw = str_random(8);
            $user = User::find($user_id);
            
            $user->change_password = 0;
            $user->password = bcrypt($pw);
            $user->save();
            // $role = auth()->user()->roles[0]->name;
            // Send email
            Mail::send('emails.resetPassword', ['user' => $user, 'pw' => $pw], function ($m) use ($user) {
                $m->from('hello@appsite.com', 'LinkZZapp');
                $m->to($user->email, $user->name)->subject('Please Reset Your Password');
            });
            return 'User password successfully changed';
        } else {
            return 'You are not allowed to reset your own password';
        }
    }
}
