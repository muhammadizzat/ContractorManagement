<?php

namespace App\Http\Controllers\Developer\Admin;

use App\Defect;

use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\Controller;

class ProjectCalendarController extends Controller
{
    use Notifiable;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:dev-admin');
        $this->middleware('project.dev-admin.access');
      
    }

    public function ajaxGetCalendarData($proj_id)
    {
        $defects = Defect::forProject($proj_id)->caseDefectUnclosed()->select('id', 'title', 'due_date AS start', 'case_id')->get();
  
        return response()->json($defects);
    }

    public function calendar($proj_id)
    {
   
        return view('dev-admin.projects.calendar', ['proj_id' => $proj_id]);
    }

}
