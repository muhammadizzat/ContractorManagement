<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'created_by', 'project_id'];
    protected $dates = ['created_at'];

    public function projects()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function floors()
    {
        return $this->hasMany('App\UnitTypeFloor', 'unit_type_id');
    }

    public function noOfUnits($unitTypeId) {
        return Unit::where('unit_type_id', $unitTypeId)->count();
    }
}
