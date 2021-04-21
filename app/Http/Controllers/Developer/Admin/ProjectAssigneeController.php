<?php

namespace App\Http\Controllers\Developer\Admin;


use App\Project;
use App\Developer;
use App\User;
use App\Defect;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\ProjectRequest;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;


class ProjectAssigneeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
        $this->middleware('project.dev-admin.access');
    }

    public function index($proj_id)
    {
        return view('dev-admin.projects.assignees.index', ['proj_id' => $proj_id]);  
    }

    public function getDataTableAssignees(Request $request, $proj_id)
    {
        $users = User::whereHas("roles", function($q) {
            $q->where("name", "contractor");
        })
        ->join('defects', 'users.id', 'defects.assigned_contractor_user_id')
        ->where('defects.project_id', $proj_id)
        ->where('defects.status', '!=', 'closed')
        ->groupBy('users.id')
        ->select(DB::raw('users.id, users.name, users.email, count(*) as defects_count'));

        return DataTables::of($users)
            ->addIndexColumn()
            ->make(true);
    }

    public function ajaxGetAssigneesDefects(Request $request, $proj_id, $user_id)
    {
        $defects = Defect::with(['type', 'case'])
        ->forProject($proj_id)
        ->unclosed()
        ->where('assigned_contractor_user_id', $user_id)
        ->get();

        return $defects;
    }
}
