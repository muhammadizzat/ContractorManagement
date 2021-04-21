<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $fillable = ['contact_no', 'address_1', 'status', 'created_by', 'user_id', 'address_2', 'city', 'state', 'postal_code'];
    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
