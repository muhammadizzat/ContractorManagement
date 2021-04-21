<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    protected $fillable = ['name','created_by', 'is_disabled'];


    public function developer_projects()
    {
        return $this->belongsTo('App\Project','id', 'developer_id');
    }

    public function logo_media()
    {
        return $this->belongsTo('App\Media','logo_media_id');
    }
}
