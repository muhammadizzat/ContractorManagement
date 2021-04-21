<?php

namespace App\Http\Controllers\Developer\Admin;

use App\Excel\Exports\UnitTypesExport;
use App\Excel\Imports\UnitTypesImport;
use App\Excel\Templates\UnitTypesTemplate;
use App\Http\Controllers\Controller;
use App\Media;
use App\Unit;
use App\UnitType;
use App\UnitTypeFloor;
use Illuminate\Http\Request;
use ImageOptimizer;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UnitTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
        $this->middleware('project.dev-admin.access');
    }

    public function index($proj_id)
    {
        return view('dev-admin.projects.unit-types.index', ['proj_id' => $proj_id]);
    }

    public function getDataTableUnitTypes(Request $request, $proj_id)
    {
        $unit_types = UnitType::where('unit_types.project_id', $proj_id)->select([
            'unit_types.id',
            'unit_types.name',
            'unit_types.created_at',
            \DB::raw('count(units.unit_type_id) as count'),
            'unit_types.project_id'
        ])->leftjoin('units', 'units.unit_type_id', '=', 'unit_types.id')
        ->groupBy('unit_types.id');

        return DataTables::of($unit_types)
            ->addIndexColumn()
            ->addColumn('viewUrl', function ($row) {
                return route('dev-admin.projects.unit-types.index', ['proj_id' => $row->project_id]);
            })
            ->addColumn('editUrl', function ($row) {
                return route('dev-admin.projects.unit-types.edit', ['proj_id' => $row->project_id, 'id' => $row->id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('dev-admin.projects.unit-types.delete', ['proj_id' => $row->project_id, 'id' => $row->id]);
            })
            ->make(true);
    }

    public function createExcelUnitTypesTemplate($proj_id)
    {
        return Excel::download(new UnitTypesTemplate($proj_id), 'Unit Types Import.xlsx');
    }

    public function importUnitTypes($proj_id)
    {
        return view('dev-admin.projects.unit-types.import', ['proj_id' => $proj_id]);
    }

    public function postUnitTypesExcelImport(Request $request, $proj_id)
    {
        $this->validate($request, [
            'unit_types_excel' => 'required|mimes:xls,xlsx',
        ]);

        $user_id = auth()->user()->id;
        $path = $request->file('unit_types_excel');
        Excel::import(new UnitTypesImport($user_id, $proj_id), $path);
        return redirect()->route('dev-admin.projects.unit-types.index', ['proj_id' => $proj_id])->withStatus(__('Unit Type successfully imported.'));
    }

    public function getUnitTypesExcelExport($proj_id)
    {
        return Excel::download(new UnitTypesExport($proj_id), 'Unit Types.xlsx');
    }

    public function addUnitType($proj_id)
    {
        return view('dev-admin.projects.unit-types.add', ['proj_id' => $proj_id]);
    }

    public function postAddUnitType($proj_id)
    {
        $data = request()->validate([
            'name' => 'required',
        ]);
        $data['project_id'] = $proj_id;
        $data['created_by'] = auth()->user()->id;

        $unit_type = UnitType::create($data);

        return redirect()->route('dev-admin.projects.unit-types.edit', ['proj_id' => $proj_id, 'unit_type' => $unit_type])->withStatus(__('Unit Type successfully created.'));
    }

    public function editUnitType(Request $request, $proj_id, $id)
    {
        $unit_type = UnitType::find($id);

        return view('dev-admin.projects.unit-types.edit', ['unit_type' => $unit_type, 'proj_id' => $proj_id, 'id' => $id]);
    }

    public function postEditUnitType(Request $request, $proj_id)
    {
        $id = $request->id;
        $data = request()->validate([
            'name' => 'required',
        ]);

        $data['project_id'] = $proj_id;
        $data['created_by'] = auth()->user()->id;

        $unit_types = UnitType::find($id)->update($data);

        return redirect()->route('dev-admin.projects.unit-types.index', ['unit_types' => $unit_types, 'proj_id' => $proj_id])->withStatus(__('Unit Type is successfully updated.'));

    }

    public function deleteUnitType(Request $request, $proj_id)
    {
        $id = $request->id;
        $unit = Unit::where('unit_type_id', $id)->count();
        if ($unit == 0) {
            $unit_type = UnitType::where('id', $id)->delete();
            // $floors = UnitTypeFloor::where('unit_type_id', $id)->get();
            // if(count($floors) != 0){
            //     return redirect()->route('dev-admin.projects.unit-types.index', ['proj_id' => $proj_id])->with('failed', 'Unit Type failed to delete due to existing floor plan.');
            // } else {
            //     $unit_type->delete();
            //     return redirect()->route('dev-admin.projects.unit-types.index', ['proj_id' => $proj_id])->with('status', 'Unit Type is successfully deleted.');
            // }
            return redirect()->route('dev-admin.projects.unit-types.index', ['proj_id' => $proj_id])->with('status', 'Unit Type is successfully deleted.');
        } else {
            return redirect()->route('dev-admin.projects.unit-types.index', ['proj_id' => $proj_id])->with('failed', 'Unit Type failed to delete due to existing unit(s).');

        }
    }

    public function getUnitTypeFloorPlanImage($proj_id, $unit_type_id, $id)
    {
        $floor = UnitTypeFloor::find($id);

        $imageMedia = $floor->floor_plan_media;

        if (empty($imageMedia)) {
            return response(null, 404);
        }

        return response()->make($imageMedia->data, 200, [
            'Content-Type' => $imageMedia->mimetype,
            'Content-Disposition' => 'inline; filename="' . $imageMedia->filename . '"',
        ]);
    }

    public function postAddUnitTypeFloor($proj_id, $id)
    {
        $data = request()->validate([
            'name' => 'required',
        ]);

        $imageData = request()->validate([
            'floor_plan_data_url' => '',
        ]);

        $unit_type = UnitType::find($id);
        $floor = $unit_type->floors()->create($data);

        if (!empty($imageData['floor_plan_data_url'])) {
            $imageData = self::processBase64DataUrl($imageData['floor_plan_data_url']);
            $imageData = self::optimizeImage($imageData);

            $imageMedia = Media::create([
                'category' => 'floor-plan-image',
                'mimetype' => $imageData['mime_type'],
                'data' => $imageData['data'],
                'size' => $imageData['size'],
                'filename' => 'floor_plan_img_' . ($floor->id) . '_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
                'created_by' => auth()->user()->id,
            ]);

            $floor->floor_plan_media_id = $imageMedia->id;

            $floor->save();
        }

        return $floor;
    }

    public function postEditUnitTypeFloor($proj_id, $id)
    {
        $imageData = request()->validate([
            'floorPlanId' => 'required',
            'name' => 'required',
            'floor_plan_data_url' => '',
        ]);

        $unit_type_floor = UnitTypeFloor::where('id', $imageData['floorPlanId'])->first();

        if (!empty($unit_type_floor->floor_plan_media_id)) {
            $unit_type_floor_image = Media::where('id', $unit_type_floor->floor_plan_media_id);

            if (!empty($unit_type_floor_image)) {
                $unit_type_floor->update([
                    'name' => $imageData['name'],
                ]);
            }

            if (!empty($imageData['floor_plan_data_url'])) {
                $imageData = self::processBase64DataUrl($imageData['floor_plan_data_url']);
                $imageData = self::optimizeImage($imageData);

                $unit_type_floor_image->update([
                    'category' => 'floor-plan-image',
                    'mimetype' => $imageData['mime_type'],
                    'data' => $imageData['data'],
                    'size' => $imageData['size'],
                    'filename' => 'floor_plan_img_' . ($unit_type_floor->id) . '_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
                    'created_by' => auth()->user()->id,
                ]);
            }

            return $unit_type_floor;

        } else {

            $unit_type_floor = UnitTypeFloor::where('id', $imageData['floorPlanId'])->first();

            if (!empty($imageData['floor_plan_data_url'])) {
                $imageData = self::processBase64DataUrl($imageData['floor_plan_data_url']);
                $imageData = self::optimizeImage($imageData);

                $imageMedia = Media::create([
                    'category' => 'floor-plan-image',
                    'mimetype' => $imageData['mime_type'],
                    'data' => $imageData['data'],
                    'size' => $imageData['size'],
                    'filename' => 'floor_plan_img_' . ($unit_type_floor->id) . '_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
                    'created_by' => auth()->user()->id,
                ]);

                $unit_type_floor->floor_plan_media_id = $imageMedia->id;

                $unit_type_floor->save();
            }

            return $unit_type_floor;
        }

    }

    public function postDeleteUnitTypefloor($proj_id, $id)
    {
        $data = request()->validate([
            'floorPlanId' => 'required',
        ]);

        $unit_type = UnitType::find($id);
        $unit_type_floor = UnitTypeFloor::where('id', $data['floorPlanId'])->first();
        $floor_plan_media_id = $unit_type_floor->floor_plan_media_id;
        $unit_type_floor_image = Media::where('id', $floor_plan_media_id);

        $unit_type_floor->delete();
        $unit_type_floor_image->delete();

        return $unit_type_floor;
    }

    private static function processBase64DataUrl($dataUrl)
    {
        $parts = explode(',', $dataUrl);

        preg_match('#data:(.*?);base64#', $parts[0], $matches);
        $mimeType = $matches[1];
        $extension = explode('/', $mimeType)[1];

        $data = base64_decode($parts[1]);

        return [
            'data' => $data,
            'mime_type' => $mimeType,
            'size' => mb_strlen($data),
            'extension' => $extension,
        ];
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
}
