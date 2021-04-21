<?php

namespace App\Http\Controllers\Developer\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Project;
use App\DeveloperAdmin;
use App\Defect;
use App\ProjectCase;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;
use Illuminate\Support\Facades\Redirect;

use Maatwebsite\Excel\Facades\Excel;

use App\Excel\Exports\ProjectsExport;


class DeveloperProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
        $this->middleware('project.dev-admin.access');
    }

    //display all projects
    public function displayProjects()
    {
        $user = auth()->user();
        $primary_admin = $user->developer_admin->primary_admin;
        return view('dev-admin.projects.index', ['primary_admin' => $primary_admin]);
    }

    public function getDataTableProjects(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $primary_admin = $user->developer_admin->primary_admin;
            $dev_id = $user->developer_admin->developer_id;

            //if developer admin is Manager, then display all projects
            if ($primary_admin) {
                $projects = Project::where('developer_id', $dev_id)->get();
            } else {
                //if developer admin is Manager, then display all projects
                $projects = $user->projects_dev_admins;
            }

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('viewUrl', function ($row) {
                    return route('dev-admin.projects.dashboard', ['proj_id' => $row->id]);
                })
                ->addColumn('unitUrl', function ($row) {
                    return route('dev-admin.projects.units.index', ['proj_id' => $row->id]);
                })
                ->addColumn('assignProjectDevAdminsUrl', function ($row) {
                    return route('dev-admin.projects.dev-admins.index', ['proj_id' => $row->id]);
                })
                ->addColumn('assignProjectClerkOfWorksUrl', function ($row) {
                    return route('dev-admin.projects.dev-cows.index', ['proj_id' => $row->id]);
                })
                ->addColumn('unitTypeUrl', function ($row) {
                    return route('dev-admin.projects.unit-types.index', ['proj_id' => $row->id]);
                })
                ->addColumn('casesUrl', function ($row) {
                    return route('dev-admin.projects.cases.index', ['proj_id' => $row->id]);
                })
                ->addColumn('defectsUrl', function ($row) {
                    return route('dev-admin.projects.defects.index', ['proj_id' => $row->id]);
                })

                ->make(true);
        }
    }

    public function getProjectLogo($proj_id)
    {
        $project = Project::with('logo_media')->find($proj_id);
        $logoMedia = $project->logo_media;
        if (!empty($logoMedia)) {
            return response($logoMedia->data, 200)
                ->header('Content-Type', $logoMedia->mimetype);
        }
        return response(null, 204);
    }

    public function assignDeveloperAdmin($proj_id)
    {

        $id = auth()->user()->id;

        $primary_admin = DeveloperAdmin::select('primary_admin')->where('user_id', $id)->where('primary_admin', 1)->count();

        if ($primary_admin == 1) {
            return view('dev-admin.projects.dev-admins.index', ['proj_id' => $proj_id]);
        } else {
            return Redirect::back()->with('error', 'You are not a Manager.');
        }
    }

    public function getDataTableAssignDeveloperAdmin(Request $request, $proj_id)
    {
        $project_dev_admin = DB::table('project_dev_admin')
            ->join('users', 'users.id', '=', 'project_dev_admin.dev_admin_user_id')
            ->where('project_id', $proj_id)->get();

            return DataTables::of($project_dev_admin)
            ->addIndexColumn()
            ->addColumn('UnassignedUrl', function ($row) {
                return route('dev-admin.projects.dev-admins.unassign', ['proj_id' => $row->project_id, 'id' => $row->dev_admin_user_id]);
            })

            ->make(true);
    }

    public function addAssignDeveloperAdmin($proj_id)
    {

        $id = auth()->user()->id;

        $primary_admin = DeveloperAdmin::select('primary_admin')->where('user_id', $id)->where('primary_admin', 1)->count();

        if ($primary_admin == 1) {
            return view('dev-admin.projects.dev-admins.assign', ['proj_id' => $proj_id]);
        } else {
            return Redirect::back()->with('error', 'You are not a Manager.');
        }
    }

    public function postAssignDeveloperAdmin(Request $request, $proj_id)
    {

        $data = request()->validate([
            'dev_admin_user_id' => 'required'
        ]);

        $project = Project::find($proj_id);


        $dev_admin_user_id = DB::table('project_dev_admin')
            ->where('project_id', $proj_id)
            ->where('dev_admin_user_id', $data['dev_admin_user_id'])
            ->count();

        if ($dev_admin_user_id == 0) {

            $project->dev_admin_users()->attach($data['dev_admin_user_id']);

            return redirect()->route('dev-admin.projects.dev-admins.index', ['proj_id' => $proj_id])->with('status', 'Project Developer Admin assigned.');
        } else {
            return Redirect::back()->with('error', 'Developer Admin already assigned');
        }
    }

    public function assignProjectClerkOfWork($proj_id)
    {

        $id = auth()->user()->id;

        $primary_admin = DeveloperAdmin::select('primary_admin')->where('user_id', $id)->where('primary_admin', 1)->count();

        if ($primary_admin == 1) {
            return view('dev-admin.projects.dev-cows.index', ['proj_id' => $proj_id]);
        } else {
            return Redirect::back()->with('error', 'You are not a Manager.');
        }
    }

    public function postUnassignDeveloperAdmin($proj_id, $id)
    {
        $project_dev_admin = ProjectCase::Open()->where('created_by', $id)->count();

        if ($project_dev_admin == 0) {
            $project = Project::find($proj_id);

            $project->dev_admin_users()->detach($id);

            return Redirect::back()->with('status', 'Developer Admin Unassigned');
        } else {
            return Redirect::back()->with('error', 'Developer Admin cannot unassign.There is/are case that still open created by her/him');
        }
    }


    public function getDataTableAssignProjectClerkOfWork($proj_id)
    {
        $project_dev_cow = DB::table('project_dev_cow')
        ->join('users', 'users.id', '=', 'project_dev_cow.dev_cow_user_id')
        ->where('project_id', $proj_id)->get();

        return DataTables::of($project_dev_cow)
            ->addIndexColumn()
            ->addColumn('UnassignedUrl', function ($row) {
                return route('dev-admin.projects.dev-cows.unassign', ['proj_id' => $row->project_id, 'id' => $row->dev_cow_user_id]);
            })
            ->make(true);
    }


    public function addAssignProjectClerkOfWork($proj_id)
    {
        $id = auth()->user()->id;

        $primary_admin = DeveloperAdmin::select('primary_admin')->where('user_id', $id)->where('primary_admin', 1)->count();

        if ($primary_admin == 1) {
            return view('dev-admin.projects.dev-cows.assign', ['proj_id' => $proj_id]);
        } else {
            return Redirect::back()->with('error', 'You are not a Manager.');
        }
    }

    public function postAssignProjectClerkOfWork($proj_id)
    {
        $data = request()->validate([
            'dev_cow_user_id' => 'required'
        ]);

        $project = Project::find($proj_id);

        $dev_cow_user_id = DB::table('project_dev_cow')
            ->where('project_id', $proj_id)
            ->where('dev_cow_user_id', $data['dev_cow_user_id'])
            ->count();

        if ($dev_cow_user_id == 0) {
            $project->dev_cow_users()->attach($data['dev_cow_user_id']);
            return redirect()->route('dev-admin.projects.dev-cows.index', ['proj_id' => $proj_id])->with('status', 'Clerk Of Work assigned.');
        } else {
            return Redirect::back()->with('error', 'Clerk Of Work already assigned');
        }
    }

    public function postUnassignProjectClerkOfWork($proj_id, $id)
    {
        $project_dev_cow = ProjectCase::Open()->where('assigned_cow_user_id', $id)->count();

        if ($project_dev_cow == 0) {
            $project = Project::find($proj_id);

            $project->dev_cow_users()->detach($id);

            return Redirect::back()->with('status', 'Developer Clerk Of Work Unassigned');
        } else {
            return Redirect::back()->with('error', 'Developer Clerk Of Work cannot unassign.There is/are case that still open created by her/him');
        }
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
