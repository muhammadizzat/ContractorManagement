<?php

namespace App\Http\Controllers\Developer\Admin;


use App\Project;
use App\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\ProjectRequest;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;


class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
        $this->middleware('project.dev-admin.access');

    }
    /**
     * Display a listing of the projects
     *
     * @param  \App\Project  $model
     * @return \Illuminate\View\View
     */
    public function displayProjects()
    {
        return view('dev-admin.projects.index');
    }

    public function getDataTableProjects(Request $request)
    {
        $user = auth()->user();
        $developer_id = $user->developer_admin->developer_id;

        if ($request->ajax()) {
            $projects = Project::where('developer_id',$developer_id);
            if (!$user->developer_admin->primary_admin) {
                $projects = $projects->whereHas('dev_admin_users', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            }
            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('namelink', function ($projects) {
                    return '<a href="' . route('dev-admin.projects.index', ['id' => $projects->id]); 
                })
                ->addColumn('viewUrl', function($row) {
                    return route('dev-admin.projects.dashboard', ['proj_id' => $row->id]);
                })
                // ->addColumn('unitTypeUrl', function($row) {
                //     return route('dev-admin.projects.unit-types.index', ['proj_id' => $row->id]);
                // })
                // ->addColumn('casesUrl', function($row) {
                //     return route('dev-admin.projects.cases.index', ['proj_id' => $row->id]);
                // })
                ->make(true);
        }

    }


    
    /**
     * Create a new project
     *
     * @param  \App\Project  $model
     * @return \Illuminate\View\View
     */
    public function postAddProject(Request $request)
    {
        $id = $request->id;


        $data = request()->validate([
            'name' => 'required',
            'address' => 'required',
            'address2' => 'required',
            'address3' => 'required',
            'contact_no' => 'required|regex:/(01)[0-9]/',
            'zipcode' => 'required',
            'status' => 'required',
            'expiry_date' =>'required',
            'start_date' =>'required',
        ]);


        $data['developer_id'] = $request->id;
        $data['created_by'] = auth()->user()->name;
        $data['expiry_date'] = implode("-", array_reverse(explode("/", $data['expiry_date'])));
        $data['start_date'] = implode("-", array_reverse(explode("/", $data['start_date'])));

        Project::create($data);


        return redirect()->route('projects.index', ['id'=>$id])->with('status', 'Project successfully created.');
    }

    /**
     * Show the form for editing the project.
     *
     * @return \Illuminate\View\View
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }


    /**
     * Update the project
     *
     * @param  \App\Http\Requests\ProjectRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Project $project)
    {
        $data = request()->validate([
            'name' => 'required'
        ]);

        $project->update($data);

        return redirect()->route('projects.index')->with('status', 'Project is successfully updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Project is successfully deleted.');
    }
}
