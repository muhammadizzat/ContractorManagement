<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Defect extends Model
{
    use SoftDeletes;
    protected $fillable = ['developer_id', 'project_id', 'title','description', 'ref_no', 'defect_type_id', 'due_date', 'status','created_by', 'resolved_date', 'closed_date'];
    protected $dates = ['created_at', 'due_date'];

    public function type()
    {
        return $this->belongsTo('App\DefectType', 'defect_type_id');
    }

    public function case()
    {
        return $this->belongsTo('App\ProjectCase', 'case_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function assigned_contractor()
    {
        return $this->belongsTo('App\User', 'assigned_contractor_user_id');
    }

    public function scopeUnclosed($query)
    {
        return $query->where('defects.status', '!=', 'closed');
    }
    
    public function activities() {
        return $this->hasMany('App\DefectActivity')->orderBy('created_at');
    }

    public function images() {
        return $this->hasMany('App\DefectImage')->orderBy('created_at');
    }

    public function scopeForProject($query, $project_id) {
        return $query->where('defects.project_id', $project_id);
    }

    public function pins() {
        return $this->hasMany('App\DefectPin');
    }

    public function tags() {
        return $this->hasMany('App\DefectTag', 'defect_id');
    }

    public function duplicate_defect()
    {
        return $this->belongsTo('App\Defect', 'duplicate_defect_id');
    }

    public function scopeCaseDefectUnclosed($query)
    {
        return $query->where('defects.status', '!=', 'closed')->whereHas('case', function ($query) {
            $query->where('deleted_at', '=',NULL);
        });
    }

}
