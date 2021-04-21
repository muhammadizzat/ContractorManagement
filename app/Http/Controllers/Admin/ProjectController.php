<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\VerifyUser;
use App\Mail\VerifyMail;
use App\Project;
use App\Developer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function addProject(Request $request)
    {
        $id = $request->id;
        $developer = Developer::where('id', $id)->get();
        return view('admin.developers.projects.create',['developers' => $developer]);
    }

    public function index(Request $request)
    {
        $id = $request->id;
        $project = Project::where('developer_id', $id)->get();
        $developer = Developer::where('id', $id)->get();

        return view('admin.developers.projects.index', ['projects' => $project, 'developer' => $developer]);
    }

    public function show()
    {
        return view('admin.developers.projects.list-projects');
    }


    public function getProjectList(Request $request)
    {
        if ($request->ajax()) {
            $projects = Project::all();
            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('editUrl', function($row) {
                    return route('admin.developers.projects.edit', ['id' => $row->id]);
                })
                ->addColumn('deleteUrl', function($row) {
                    return route('admin.developers.projects.destroy', ['id' => $row->id]);
                })
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
            'description' => 'required',
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



        return redirect()->route('projects.index',['id'=>$id])->with('status', 'Project successfully created.');
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
