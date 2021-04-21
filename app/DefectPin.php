<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefectPin extends Model
{
    protected $fillable = ['label','x', 'y'];

    public function defect()
    {
        return $this->belongsTo('App\Defect', 'defect_id');
    }
}
