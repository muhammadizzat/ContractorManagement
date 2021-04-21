<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\Http\Controllers\Controller;
use App\Unit;
use App\UnitType;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

use App\Excel\Exports\UnitsExport;

class UnitController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }

    public function index($proj_id)
    {
        return view('dev-cow.projects.units.index', ['proj_id' => $proj_id]);
    }

    public function unitsExcelExport($proj_id)
    {
        return Excel::download(new UnitsExport($proj_id), 'Units.xlsx');
    }

    public function addUnit($proj_id)
    {
        $unit_types = UnitType::select('id', 'name')->where('project_id', $proj_id)->get();
        return view('dev-cow.projects.units.add', ['proj_id' => $proj_id, 'unit_types' => $unit_types]);
    }

    public function postAddUnit($proj_id)
    {
        $data = request()->validate([
            'unit_no' => 'required',
            'unit_type_id' => 'required',
            'owner_name' => '',
            'owner_contact_no' => 'numeric|nullable',
            'owner_email' => 'email|nullable',
        ]);

        $data['project_id'] = $proj_id;
        $data['created_by'] = auth()->user()->id;

        Unit::create($data);

        return redirect()->route('dev-cow.projects.units.index', ['proj_id' => $proj_id])->withStatus(__('Unit successfully created.'));
    }

    public function getDataTableUnits($proj_id)
    {
        $units = Unit::with('unit_type')->where('project_id', $proj_id);
        return DataTables::of($units)
            ->addIndexColumn()
            ->addColumn('viewUrl', function ($row) {
                return route('dev-cow.projects.unit-types.index', ['proj_id' => $row->project_id]);
            })
            ->addColumn('editUrl', function ($row) {
                return route('dev-cow.projects.units.edit', ['proj_id' => $row->project_id, 'id' => $row->id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('dev-cow.projects.units.delete', ['proj_id' => $row->project_id, 'id' => $row->id]);
            })
            ->make(true);
    }

    public function editUnit(Request $request, $proj_id)
    {
        $id = $request->id;
        $units = Unit::find($id);
        $unit_types = UnitType::select('id', 'name')->where('project_id', $proj_id)->get();
        return view('dev-cow.projects.units.edit', ['units' => $units, 'proj_id' => $proj_id, 'unit_types' => $unit_types, 'id' => $id]);
    }

    public function postEditUnit(Request $request, $proj_id)
    {
        $id = $request->id;
        $data = request()->validate([
            'unit_no' => 'required',
            'unit_type_id' => 'required',
            'owner_name' => '',
            'owner_contact_no' => 'numeric|nullable',
            'owner_email' => 'email|nullable',
        ]);

        $data['project_id'] = $proj_id;
        $data['created_by'] = auth()->user()->id;

        $units = Unit::find($id)->update($data);

        return redirect()->route('dev-cow.projects.units.index', ['units' => $units, 'proj_id' => $proj_id])->withStatus(__('Unit is successfully updated.'));
    }

    public function deleteUnit(Request $request, $proj_id)
    {
        $id = $request->id;
        $units = Unit::where('id', $id)->delete();
        return redirect()->route('dev-cow.projects.units.index', ['units' => $units, 'proj_id' => $proj_id])->withStatus(__('Unit is successfully deleted.'));
    }
}
