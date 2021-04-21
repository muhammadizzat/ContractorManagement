<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'created_by', 'address', 'address2', 'address3', 'zipcode', 'description', 'status', 'developer_id'];

    public function developer_projects()
    {
        return $this->belongsTo('App\Developer', 'developer_id');
    }

    public function logo_media()
    {
        return $this->belongsTo('App\Media', 'logo_media_id');
    }
    public function units()
    {
        return $this->hasMany('App\Unit');
    }

    public function unit_types()
    {
        return $this->hasMany('App\UnitType');
    }

    public function cases()
    {
        return $this->hasMany('App\ProjectCase');
    }

    public function dev_admin_users()
    {
        return $this->belongsToMany(User::class, 'project_dev_admin','project_id','dev_admin_user_id')->withTimestamps();
    }

    public function dev_cow_users()
    {
        return $this->belongsToMany(User::class, 'project_dev_cow','project_id','dev_cow_user_id')->withTimestamps();
    }

}
