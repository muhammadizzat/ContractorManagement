<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use ImageOptimizer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Developer;
use App\Project;
use App\Media;
use App\ProjectCase;



class DeveloperProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $id = $request->id;
        $project = Project::where('developer_id', $id)->get();
        $developer = Developer::where('id', $id)->get();

        return view('admin.developers.projects.index', ['projects' => $project, 'developer' => $developer]);
    }

    public function displayProjects($dev_id)
    {
        return view('admin.developers.projects.index', ['dev_id' => $dev_id]);
    }

    public function getDataTableProjects($dev_id)
    {

        $projects = Project::where('developer_id', $dev_id);

        return DataTables::of($projects)
            ->addIndexColumn()
            ->addColumn('logoUrl', function ($row) {
                return route('admin.developers.projects.logo', ['id' => $row->id, 'dev_id' => $row->developer_id]);
            })
            ->addColumn('editUrl', function ($row) {
                return route('admin.developers.projects.edit', ['id' => $row->id, 'dev_id' => $row->developer_id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('admin.developers.projects.delete', ['id' => $row->id, 'dev_id' => $row->developer_id]);
            })
            ->make(true);
    }

    public function addProject($dev_id)
    {
        return view('admin.developers.projects.add', ['dev_id' => $dev_id]);
    }

    /**
     * Create a new project
     *
     * @param  \App\Project  $model
     * @return \Illuminate\View\View
     */
    public function postAddProject($dev_id)
    {
        $data = request()->validate([
            'name' => 'required',
            'address' => 'required|max:100',
            'address2' => 'required_with:address3|max:100',
            'address3' => 'nullable|max:100',
            'zipcode' => 'required|numeric|digits:5',
            'attachment' => 'mimes:jpeg,png',
            'description' => 'max:250'
        ]);

        $data['developer_id'] = $dev_id;
        $data['status'] = 'active';
        $data['created_by'] = auth()->user()->id;

        $project = Project::create($data);

        if (!empty($data['attachment'])) {
            $attachment_file = $data['attachment'];
            $logo_file_contents = file_get_contents($attachment_file);
            $imageData = [
                'data' => $logo_file_contents,
                'mime_type' => $attachment_file->getClientMimeType(),
                'size' => mb_strlen($logo_file_contents),
                'extension' => $attachment_file->getClientOriginalExtension()
            ];

            $imageData = self::optimizeImage($imageData);

            $imageMedia = Media::create([
                'category' => 'project-icon',
                'mimetype' => $imageData['mime_type'],
                'data' => $imageData['data'],
                'size' => $imageData['size'],
                'filename' => 'project_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
                'created_by' => auth()->user()->id
            ]);

            $project->logo_media()->associate($imageMedia);
            $project->save();
        }

        $dev_id = $project->developer_id;

        return redirect()->route('admin.developers.projects.index', ['dev_id' => $dev_id])->withStatus(__('Project successfully created.'));
    }

    public function viewProject(Request $request, $id)
    {
        $id = $request->id;
        $project = Project::with('logo_media')->find($id);
        $logoMedia = $project->logo_media;

        return view('admin.developers.projects.view', ['project' => $project, 'logoMedia' => $logoMedia]);
    }

    public function editProject(Request $request, $id)
    {
        $id = $request->id;
        $project = Project::with('logo_media')->find($id);
        $logoMedia = $project->logo_media;

        return view('admin.developers.projects.edit', ['project' => $project, 'logoMedia' => $logoMedia]);
    }

    public function postEditProject(Request $request, $dev_id)
    {
        $data = request()->validate([
            'name' => 'required',
            'address' => 'required|max:100',
            'address2' => 'required_with:address3|max:100',
            'address3' => 'nullable|max:100',
            'zipcode' => 'required|numeric|digits:5',
            'attachment' => 'mimes:jpeg,png',
            'description' => 'max:250'
        ]);

        $id = $request->id;
        $project = Project::find($id);

        $project_name = $project->name;
        $project_address = $project->address;
        $project_address2 = $project->address2;
        $project_address3 = $project->address3;
        $zipcode = $project->zipcode;
        $description = $project->description;

        if (
            $project_name != request()->name
            || $project_address != request()->address
            || $project_address2 != request()->address2
            || $project_address3 != request()->address3
            || $zipcode != request()->zipcode
            || $description != request()->description
            || !empty($data['attachment'])
        ) {


            $project->update($data);

            if (!empty($data['attachment'])) {
                $attachment_file = $data['attachment'];
                $logo_file_contents = file_get_contents($attachment_file);
                $imageData = [
                    'data' => $logo_file_contents,
                    'mime_type' => $attachment_file->getClientMimeType(),
                    'size' => mb_strlen($logo_file_contents),
                    'extension' => $attachment_file->getClientOriginalExtension()
                ];

                $imageData = self::optimizeImage($imageData);

                $imageMedia = Media::create([
                    'category' => 'project-icon',
                    'mimetype' => $imageData['mime_type'],
                    'data' => $imageData['data'],
                    'size' => $imageData['size'],
                    'filename' => 'project_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
                    'created_by' => auth()->user()->id
                ]);

                DB::transaction(function () use ($id, $imageMedia) {
                    $project = Project::find($id);
                    $oldProjectMedia = $project->logo_media;

                    if (!empty($oldProjectMedia)) {
                        $project->logo_media()->dissociate();
                        $project->save();

                        $oldProjectMedia->delete();
                    }

                    $project->logo_media()->associate($imageMedia);
                    $project->save();
                });
            }

            return redirect()->route('admin.developers.projects.index', ['dev_id' => $dev_id])->withStatus(__('Project is successfully updated.'));
        } else {
            return back();
        }
    }

    public function getProjectLogo(Request $request, $dev_id, $proj_id)
    {
        $project = Project::with('logo_media')->find($proj_id);
        $logoMedia = $project->logo_media;
        if (!empty($logoMedia)) {
            return response($logoMedia->data, 200)
                ->header('Content-Type', $logoMedia->mimetype);
        }
        return response(null, 204);
    }

    private static function optimizeImage($imageData)
    {
        $filename = 'temp_img_' . rand() . '_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'];
        $temp_filepath = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($temp_filepath, $imageData['data']);
        ImageOptimizer::optimize($temp_filepath);
        $optimized_image_data = file_get_contents($temp_filepath);
        unlink($temp_filepath);

        $imageData['data'] = $optimized_image_data;
        $imageData['size'] = mb_strlen($optimized_image_data);

        return $imageData;
    }

    public function deleteDeveloperProject($dev_id, $id)
    {
        $project_case_count = ProjectCase::where('project_id', $id)->open()->count();
        $project = Project::where('developer_id', $dev_id)->where('id', $id)->first();
        $project_name = $project->name;


        if ($project_case_count == 0) {
            $project->delete();

            return redirect()->route('admin.developers.projects.index', ['dev_id' => $dev_id])->with('status', '' . $project_name . ' is successfully deleted.');
        } else {
            return Redirect::back()->with('error', '' . $project_name . ' still has open cases.');
        }
    }
}
