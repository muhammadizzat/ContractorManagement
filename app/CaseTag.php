<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaseTag extends Model
{
    protected $fillable = ['case_id','tag'];

    public function case()
    {
        return $this->belongsTo('App\ProjectCase', 'case_id');
    }
}
