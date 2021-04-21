<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\Defect;
use App\Excel\Exports\ProjectsExport;
use App\Http\Controllers\Controller;
use App\Project;
use App\User;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class DeveloperProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }

    public function assignDeveloperAdmin($proj_id)
    {
        return view('dev-cow.projects.dev-admins.index', ['proj_id' => $proj_id]);
    }

    public function getDataTableAssignDeveloperAdmin(Request $request, $proj_id)
    {
        $project_dev_admin = DB::table('project_dev_admin')
            ->join('users', 'users.id', '=', 'project_dev_admin.dev_admin_user_id')
            ->where('project_id', $proj_id)->get();

        return DataTables::of($project_dev_admin)
            ->addIndexColumn()
            ->make(true);
    }

    public function assignProjectClerkOfWork($proj_id)
    {
        return view('dev-cow.projects.dev-cows.index', ['proj_id' => $proj_id]);
    }

    public function getDataTableAssignProjectClerkOfWork($proj_id)
    {
        $project_dev_cow = DB::table('project_dev_cow')
            ->join('users', 'users.id', '=', 'project_dev_cow.dev_cow_user_id')
            ->where('project_id', $proj_id)->get();

        return DataTables::of($project_dev_cow)
            ->addIndexColumn()
            ->make(true);
    }

    public function projectsExcelExport()
    {
        $user = auth()->user();
        $primary_admin = $user->developer_admin->primary_admin;

        if ($primary_admin) {
            $dev_id = $user->developer_admin->developer_id;
            $projects = Project::where('developer_id', $dev_id)->get();
        } else {
            $projects = $user->projects_dev_admins;
        }
        return Excel::download(new ProjectsExport($projects), 'Projects.xlsx');
    }

    public function viewProject($proj_id)
    {
        $project = Project::with('logo_media')->find($proj_id);

        $logoMedia = $project->logo_media;

        $defects = Defect::forProject($proj_id)->get();

        return view('dev-admin.projects.dashboard', ['project' => $project, 'logoMedia' => $logoMedia, 'defects' => $defects]);
    }
}
