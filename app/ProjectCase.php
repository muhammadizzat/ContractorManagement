<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;


class ProjectCase extends Model
{
    use SoftDeletes;

    protected $fillable = ['developer_id', 'project_id', 'assigned_cow_user_id', 'unit_id', 'ref_no', 'created_by', 'title', 'status', 'description'];


    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function assigned_cow()
    {
        return $this->belongsTo('App\User', 'assigned_cow_user_id');
    }

    public function created_by()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit', 'unit_id');
    }

    public function defects() {
        return $this->hasMany('App\Defect', 'case_id');
    }

    public function tags() {
        return $this->hasMany('App\CaseTag', 'case_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', '!=', 'closed');
    }

    public function scopeWithOverdueDefects($query)
    {
        return $query->where('status', '!=', 'closed')->whereHas('defects', function ($query) {
            $query->whereDate('due_date', '<', Carbon::now());
        });
    }

    public function scopeForProject($query, $project_id) {
        return $query->where('project_id', $project_id);
    }
}
