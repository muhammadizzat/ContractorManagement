<?php

namespace App\Http\Controllers\Admin;

use ImageOptimizer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

use App\Developer;
use App\Http\Controllers\Controller;
use App\DeveloperAdmin;
use App\Project;
use App\Media;

class DeveloperController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function getDataTableDeveloper(Request $request)
    {
        if ($request->ajax()) {
            $developers = Developer::all();
            return DataTables::of($developers)
                ->addIndexColumn()
                ->addColumn('logoUrl', function ($row) {
                    return route('admin.developers.logo', ['id' => $row->id]);
                })
                ->addColumn('viewAdminUrl', function ($row) {
                    return route('admin.developers.admins.index', ['dev_id' => $row->id]);
                })
                ->addColumn('viewProjectsUrl', function ($row) {
                    return route('admin.developers.projects.index', ['dev_id' => $row->id]);
                })
                ->addColumn('editUrl', function ($row) {
                    return route('admin.developers.edit', ['id' => $row->id]);
                })
                ->addColumn('deleteUrl', function ($row) {
                    return route('admin.developers.delete', ['id' => $row->id]);
                })
                ->make(true);
        }
    }

    public function displayDevelopers()
    {
        return view('admin.developers.index');
    }
    
    public function addDeveloper()
    {
        return view('admin.developers.add');
    }

    public function postAddDeveloper()
    {
        $data = request()->validate([
            'name' => 'required',
            'attachment' => 'mimes:jpeg,png',
        ]);

        $data['created_by'] = auth()->user()->id;
        $developer = Developer::create($data);
        
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
                'category' => 'developer-icon',
                'mimetype' => $imageData['mime_type'],
                'data' => $imageData['data'],
                'size' => $imageData['size'],
                'filename' => 'developer_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
                'created_by' => auth()->user()->id
            ]);

            $developer->logo_media()->associate($imageMedia);
            $developer->save();
        }

        return redirect()->route('admin.developers.index')->withStatus(__('Developer is successfully added.'));
    }

    public function editDeveloper(Request $request, Developer $developer)
    {
        $id = $request->dev_id;
        $developer = Developer::with('logo_media')->find($id);
        $logoMedia = $developer->logo_media;

        return view('admin.developers.edit', ['developer' => $developer, 'logoMedia' => $logoMedia]);
    }

    public function postEditDeveloper(Request $request, $id)
    {
        $data = request()->validate([
            'name' => 'required',
            'attachment' => 'mimes:jpeg,png'
        ]);


        if ($request['is_disabled']) {
            $is_disabled = 1;
        } else {
            $is_disabled = 0;
        }
        
        Developer::find($id)->update(['name' => $data['name'], 'is_disabled' => $is_disabled]);
        
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
                'category' => 'developer-icon',
                'mimetype' => $imageData['mime_type'],
                'data' => $imageData['data'],
                'size' => $imageData['size'],
                'filename' => 'developer_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
                'created_by' => auth()->user()->id
            ]);
            
            DB::transaction(function () use ($id, $imageMedia) {
                $developer = Developer::find($id);
                $oldProfileMedia = $developer->logo_media;

                if (!empty($oldProfileMedia)) {
                    $developer->logo_media()->dissociate();
                    $developer->save();

                    $oldProfileMedia->delete();
                }

                $developer->logo_media()->associate($imageMedia);
                $developer->save();
            });
        }

        return redirect()->route('admin.developers.index')->withStatus(__('Developer is successfully updated.'));
    }

    public function deleteDeveloper(Request $request)
    {
        $id = $request->dev_id;

        $developer_admin = DeveloperAdmin::where('developer_id', $id)->count();
        $projects = Project::where('developer_id', $id)->count();

        if ($developer_admin == 0 && $projects == 0) {

            Developer::where('id', $id)->delete();
            return redirect()->route('admin.developers.index')->withStatus(__('Developer is successfully deleted.'));
        } else {
            return redirect()->route('admin.developers.index')->withStatus(__('This developer cannot be deleted. A project registered under this developer still exists.'));
        }
    }

    public function getDeveloperLogo($id)
    {
        $developer = Developer::with('logo_media')->find($id);
        $logoMedia = $developer->logo_media;
        if(!empty($logoMedia)) {
            return response($logoMedia->data, 200)
                ->header('Content-Type', $logoMedia->mimetype);
        }
        return response(null, 204);
    }

    private static function optimizeImage($imageData) {
        $filename = 'temp_img_'.rand().'_'.date('Y-m-d_H:i:s').".".$imageData['extension'];
        $temp_filepath = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($temp_filepath, $imageData['data']);
        ImageOptimizer::optimize($temp_filepath);
        $optimized_image_data = file_get_contents($temp_filepath);
        unlink($temp_filepath);

        $imageData['data'] = $optimized_image_data;
        $imageData['size'] = mb_strlen($optimized_image_data);

        return $imageData;
    }
}
