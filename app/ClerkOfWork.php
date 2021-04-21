<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClerkOfWork extends Model
{
    use SoftDeletes;

    protected $fillable = ['developer_id', 'user_id', 'created_by','primary_admin'];


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function created_by()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function developer()
    {
        return $this->belongsTo('App\Developer', 'developer_id');
    }

    public function scopeForDeveloper($query, $developer_id)
    {
        return $query->where('developer_id', null)->orWhere('developer_id', $developer_id);
    }
}
