<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefectActivityImage extends Model
{
    protected $dates = ['created_at'];
    protected $fillable = ['media_id'];


    public function defect_activity()
    {
        return $this->belongsTo('App\Defect', 'defect_id');
    }

    public function image_media()
    {
        return $this->belongsTo('App\Media', 'media_id');
    }
}
