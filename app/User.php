<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Mail;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    protected $guard_name = 'web';
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'change_password', 'verified', 'is_disabled'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function verifyUser()
    {
        return $this->hasOne('App\VerifyUser');
    }

    public static function sendWelcomeEmail($user)
    {
        // Generate a new reset password token
        $token = app('auth.password.broker')->createToken($user);

        // Send email
        Mail::send('emails.welcome', ['user' => $user, 'token' => $token, 'pw' => $pw], function ($m) use ($user) {
            $m->from('hello@appsite.com', 'LinkZZapp');
            $m->to($user->email, $user->name)->subject('Welcome to APP');
        });
    }

    public static function generatePassword()
    {
        // Generate random string and encrypt it.
        return bcrypt(str_random(35));
    }

    public function developer_admin()
    {
        return $this->hasOne('App\DeveloperAdmin');
    }

    public function profile_pic_media()
    {
        return $this->belongsTo('App\Media', 'profile_pic_media_id');
    }

    public function clerk_of_work()
    {
        return $this->hasOne('App\ClerkOfWork');
    }

    public function contractor()
    {
        return $this->hasOne('App\Contractor');
    }

    public function projects_dev_admins()
    {
        return $this->belongsToMany(Project::class, 'project_dev_admin', 'dev_admin_user_id', 'project_id');
    }

    public function projects_dev_cows()
    {
        return $this->belongsToMany(Project::class, 'project_dev_cow', 'dev_cow_user_id', 'project_id');

    }

    public function contractor_associations()
    {
        return $this->hasMany('App\DeveloperContractorAssociation', 'contractor_user_id');
    }

    // Notification
    public function routeNotificationForOneSignal()
    {
        return ['tags' => ['key' => 'user_id', 'relation' => '=', 'value' => $this->id]];
    }
}
