<?php

namespace App\Http\Controllers\Developer\Admin\Configuration;

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
        $this->middleware(['role:dev-admin']);
    }

    public function getDataTableDefectType(Request $request)
    {
        $dev_id = auth()->user()->developer_admin->developer_id;

        if ($request->ajax()) {
            $defecttypes = DefectType::forDeveloper($dev_id);
            return DataTables::of($defecttypes)
                ->addIndexColumn()
                ->addColumn('editUrl', function ($row) {
                    return route('dev-admin.configuration.defect-types.edit', ['id' => $row->id]);
                })
                ->addColumn('deleteUrl', function ($row) {
                    return route('dev-admin.configuration.defect-types.delete', ['id' => $row->id]);
                })
                ->make(true);
        }
    }

    public function displayDefectType()
    {
        return view('dev-admin.configuration.defect-types.index');
    }

    public function addDefectType()
    {
        return view('dev-admin.configuration.defect-types.add');
    }

    public function postAddDefectType()
    {
        $id = auth()->user()->id;
        $dev_id = DeveloperAdmin::where('user_id', $id)->first()->developer_id;

        $data = request()->validate([
            'title' => 'required',
            'details' => 'required',
        ]);


        $data['developer_id'] = $dev_id;

        DefectType::create(
            [
                'title' => $data['title'],
                'details' => $data['details'],
                'created_by' => auth()->user()->id,
                'is_custom' => true,
                'developer_id' => $data['developer_id'],
            ]
        );

        return redirect()->route('dev-admin.configuration.defect-types.index')->with('status', 'Defect Type successfully created.');
    }

    public function editDefectType(Request $request, DefectType $defecttype)
    {
        $id = $request->id;
        $defecttype = DefectType::find($id);
        return view('dev-admin.configuration.defect-types.edit', ['defecttype' => $defecttype]);
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
            $dev_id = Auth::user()->developer_admin->developer_id;
            $defect_type = DefectType::find($id);
            if ($dev_id != $defect_type->developer_id || $defect_type->is_custom == false) {
                return redirect()->route('dev-admin.configuration.defect-types.index')->with('error', 'You do not have the access to edit this defect type.');
            } else {
                $defect_type->update($data);
                return redirect()->route('dev-admin.configuration.defect-types.index')->with('status', 'Defect Type is successfully updated.');
            };
        } else {
            return redirect()->route('dev-admin.configuration.defect-types.index')->with('error', 'You cannot edit this defect type.');
        }
    }

    public function deleteDefectType(Request $request)
    {
        $id = $request->id;
        $dev_id = Auth::user()->developer_admin->developer_id;
        $defect_type = DefectType::find($id);

        $defects = Defect::where('defect_type_id', $id)->count();

        if ($defects == 0) {
            if ($dev_id != $defect_type->developer_id || $defect_type->is_custom == false) {
                return redirect()->route('dev-admin.configuration.defect-types.index')->with('error', 'You do not have the access to delete this defect type.');
            } else {
                $defect_type->delete();
                return redirect()->route('dev-admin.configuration.defect-types.index')->with('status', 'Defect Type is successfully deleted.');
            };
        } else {
            return redirect()->route('dev-admin.configuration.defect-types.index')->with('error', 'This defect type is being assigned to defect(s).');
        }
    }
}
