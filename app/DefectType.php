<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefectType extends Model
{
    use SoftDeletes;
    protected $fillable = ['title','details','created_by','is_custom','developer_id'];

    public function defects()
    {
        return $this->hasMany('App\Defect');
    }
    
    public function developer_contractor_associations(){
        return $this->belongsToMany('App\DeveloperContractorAssociation', 'dc_assoc_defect_types', 'dca_id');
    }
    
    public function scopeForDeveloper($query, $developer_id) 
    {
        return $query->where('developer_id', null)->orWhere('developer_id', $developer_id);
    }
}
