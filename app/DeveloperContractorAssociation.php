<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveloperContractorAssociation extends Model
{
    protected $fillable = ['id', 'contractor_user_id', 'developer_id'];
    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'contractor_user_id');
    }

    public function developer()
    {
        return $this->belongsTo('App\Developer', 'developer_id');
    }
    
    public function defect_types(){
        return $this->belongsToMany('App\DefectType', 'dc_assoc_defect_types', 'dca_id', 'defect_type_id');
    }



}
