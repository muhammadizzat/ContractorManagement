<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;

use App\Defect;
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

    public function postGetCalendarData($proj_id)
    {
        $data = request()->validate([
            "start_date" => "required",
            "end_date" => "required"
        ]);

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        $defects = Defect::forProject($proj_id)
            ->unclosed()
            ->join('project_cases', 'defects.case_id', 'project_cases.id')
            ->select('defects.id', 'defects.title', DB::raw('DATE_FORMAT(defects.due_date, "%Y-%m-%d") AS formatted_due_date'), 'defects.case_id', 'defects.ref_no', 'project_cases.ref_no AS case_ref_no')
            ->whereDate('defects.due_date', '>=', $startDate)
            ->whereDate('defects.due_date', '<=', $endDate)
            ->get();
  
        return response()->json($defects);
    }

}
