<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;

use App\Excel\Exports\ProjectsExport;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');

    }
    /**
     * Display a listing of the projects
     *
     * @param  \App\Project  $model
     * @return \Illuminate\View\View
     */
    public function displayProjects()
    {
        return view('dev-cow.projects.index');
    }

    public function getDataTableProjects(Request $request)
    {
        $user = auth()->user();
        $dev_id = $user->clerk_of_work->developer_id;
        if ($request->ajax()) {

            $projects = Project::where('developer_id',$dev_id)
                ->whereHas('dev_cow_users', function($q) use ($user) {
                    $q->where('users.id', $user->id);
            });


            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('viewUrl', function ($row) {
                    return route('dev-cow.projects.units.index', ['proj_id' => $row->id]);
                })
                ->addColumn('unitTypeUrl', function ($row) {
                    return route('dev-cow.projects.unit-types.index', ['proj_id' => $row->id]);
                })
                ->addColumn('projectCasesUrl', function ($row) {
                    return route('dev-cow.projects.cases.index', ['proj_id' => $row->id]);
                })
                ->addColumn('defectsUrl', function ($row) {
                    return route('dev-cow.projects.defects.index', ['proj_id' => $row->id]);
                })
                ->make(true);
        }
    }

    public function getProjectLogo($proj_id)
    {
        $project = Project::with('logo_media')->find($proj_id);
        $logoMedia = $project->logo_media;
        if(!empty($logoMedia)) {
            return response($logoMedia->data, 200)
                ->header('Content-Type', $logoMedia->mimetype);
        }
        return response(null, 204);
    }
    
    public function projectsExcelExport()
    {
        $user = auth()->user();
        $dev_id = $user->clerk_of_work->developer_id;
        $projects = Project::where('developer_id', $dev_id)
            ->whereHas('dev_cow_users', function($q) use ($user) {
                $q->where('users.id', $user->id);
        })->get();

        return Excel::download(new ProjectsExport($projects), 'Projects.xlsx');
    }

    public function viewProject($proj_id)
    {
        $project = Project::with('logo_media')->find($proj_id);
        $logoMedia = $project->logo_media;

        return view('dev-cow.projects.dashboard', ['project' => $project, 'logoMedia' => $logoMedia]);
    }
    

}
