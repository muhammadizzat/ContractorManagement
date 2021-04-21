<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['unit_no', 'owner_name', 'owner_contact_no', 'owner_email', 'created_by', 'unit_type_id', 'project_id'];
    protected $dates = ['created_at'];

    public function unit_type()
    {
        return $this->belongsTo('App\UnitType', 'unit_type_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function cases()
    {
        return $this->hasMany('App\ProjectCase');
    }
}
