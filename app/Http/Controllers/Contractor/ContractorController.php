<?php

namespace App\Http\Controllers\Contractor;

use App\Defect;
use Carbon\Carbon;

use DB;
use App\Http\Controllers\Controller;

class ContractorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:contractor']);
    }

    public function dashboard()
    {
        if(request()->id == null) {
            $id = 0;
        } else {
            $id = request()->id;
        }
        
        $projects_defects_summary = Defect::where('defects.status', '!=', 'closed')
        ->where('assigned_contractor_user_id', auth()->user()->id)
        ->join('projects', 'projects.id', '=', 'defects.project_id')
        ->join('developers', 'developers.id', '=', 'projects.developer_id')
        ->select('developers.name as developer_name', 'projects.name as project_name', 'defects.project_id', DB::raw('count(*) as total_defects'))
        ->groupBy('defects.project_id')->get();

        $pending_defects_count = Defect::where('defects.status', '!=', 'closed')
        ->where('assigned_contractor_user_id', auth()->user()->id)
        ->count();

        $overdue_defects_count = Defect::where('defects.status', '!=', 'closed')
        ->where('assigned_contractor_user_id', auth()->user()->id)
        ->whereDate('defects.due_date', '<', Carbon::today())
        ->count();

        return view('contractor.dashboard', ['projects_defects_summary' => $projects_defects_summary, 'pending_defects_count' => $pending_defects_count, 'overdue_defects_count' => $overdue_defects_count, 'id' => $id ]);
    }

}
