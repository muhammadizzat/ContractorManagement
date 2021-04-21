<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

use App\Defect;
use \stdClass;
use App\DefectType;

use App\Http\Controllers\Controller;

class ProjectCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:cow');
        $this->middleware('project.dev-cow.access');
    }

    public function ajaxGetCalendarData($proj_id)
    {
    
        $defects = Defect::forProject($proj_id)->caseDefectUnclosed()->select('id', 'title', 'due_date AS start', 'case_id')->get();
  
        return response()->json($defects);
    }

    public function calendar($proj_id)
    {
   
        return view('dev-cow.projects.calendar', ['proj_id' => $proj_id]);
    }

}
