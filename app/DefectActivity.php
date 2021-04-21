<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefectActivity extends Model
{
    use SoftDeletes;
    protected $fillable = ['type','content', 'user_id', 'request_type', 'request_response', 'request_response_user_id'];
    protected $dates = ['created_at'];

    public function defect()
    {
        return $this->belongsTo('App\Defect', 'defect_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function images() {
        return $this->hasMany('App\DefectActivityImage')->orderBy('created_at');
    }

    public function request_response_user()
    {
        return $this->belongsTo('App\User', 'request_response_user_id');
    }
}
