<?php

namespace App\Http\Controllers\Admin\Configuration;

use Auth;
use App\Defect;
use App\DefectType;
use App\DeveloperAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DefectTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function getDataTableDefectType(Request $request)
    {
        if ($request->ajax()) {
            $defecttypes = DefectType::where('is_custom', false);
            return DataTables::of($defecttypes)
                ->addIndexColumn()
                ->addColumn('editUrl', function ($row) {
                    return route('admin.configuration.defect-types.edit', ['id' => $row->id]);
                })
                ->addColumn('deleteUrl', function ($row) {
                    return route('admin.configuration.defect-types.delete', ['id' => $row->id]);
                })
                ->make(true);
        }
    }

    public function displayDefectType()
    {
        return view('admin.configuration.defect-types.index');
    }

    public function addDefectType()
    {
        return view('admin.configuration.defect-types.add');
    }

    public function postAddDefectType()
    {
        $data = request()->validate([
            'title' => 'required',
            'details' => 'required',
        ]);

        DefectType::create(
            [
                'title' => $data['title'],
                'details' => $data['details'],
                'created_by' => auth()->user()->id,
                'is_custom' => false,
            ]
        );

        return redirect()->route('admin.configuration.defect-types.index')->with('status', 'Defect Type successfully created.');
    }

    public function editDefectType(Request $request, DefectType $defecttype)
    {
        $id = $request->id;
        $defecttype = DefectType::find($id);
        return view('admin.configuration.defect-types.edit', ['defecttype' => $defecttype]);
    }

    public function postEditDefectType(Request $request)
    {
        $id = $request->id;
        $data = request()->validate([
            'title' => 'required',
            'details' => 'required',
        ]);

        $defects = Defect::where('defect_type_id', $id)->count();

        if ($defects == 0) {
            $defect_type = DefectType::find($id);

            $defect_type->update($data);
            return redirect()->route('admin.configuration.defect-types.index')->with('status', 'Defect Type is successfully updated.');
        } else {
            return redirect()->route('admin.configuration.defect-types.index')->with('error', 'You cannot edit this defect type.');
        }
    }

    public function deleteDefectType(Request $request)
    {
        $id = $request->id;
        $defect_type = DefectType::find($id);

        $defects = Defect::where('defect_type_id', $id)->count();

        if ($defects == 0) {
            $defect_type->delete();
            return redirect()->route('admin.configuration.defect-types.index')->with('status', 'Defect Type is successfully deleted.');
        } else {
            return redirect()->route('admin.configuration.defect-types.index')->with('error', 'This defect type is being assigned to defect(s).');
        }
    }
}
