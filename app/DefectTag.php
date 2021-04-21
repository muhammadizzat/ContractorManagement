<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefectTag extends Model
{
    protected $fillable = ['defect_id','tag'];

    public function defect()
    {
        return $this->belongsTo('App\Defect', 'defect_id');
    }
}
