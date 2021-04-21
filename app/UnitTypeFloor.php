<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitTypeFloor extends Model
{
    protected $fillable = ['name', 'unit_type_id'];
    protected $dates = ['created_at'];

    public function floor_plan_media()
    {
        return $this->belongsTo('App\Media', 'floor_plan_media_id');
    }
}
