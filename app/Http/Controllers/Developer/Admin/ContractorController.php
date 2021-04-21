<?php

namespace App\Http\Controllers\Developer\Admin;

use App\User;
use App\ProjectCase;
use App\ClerkOfWork;
use App\DeveloperAdmin;
use App\Contractor;
use App\DeveloperContractorAssociation;
use App\Excel\Exports\ContractorsExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ContractorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
    }

    public function postSearchContractors(Request $request)
    {
        $pageLimit = $request->get("page_limit");
        $nameQuery = $request->get("q");
        $defectTypeId = $request->get("defect_type_id");
        $developer_id = auth()->user()->developer_admin->developer_id;

        $contractor_users = User::whereHas('contractor_associations', function($q) use ($developer_id, $defectTypeId) {
            $q->where('developer_id', $developer_id)
            ->whereHas('defect_types', function ($q) use ($defectTypeId) {
                $q->where('defect_type_id', $defectTypeId);
            });

        })
        ->where('name', 'like', "%{$nameQuery}%")
        ->select(['id', 'name'])
        ->get();
    
            
        return response()->json($contractor_users);
    }

    public function index()
    {
        return view('dev-admin.contractor.index');
    }

    public function getDataTableContractor()
    {
        $contractor = Contractor::with('user')->where('status',1);
        return DataTables::of($contractor)
            ->make(true);
    }

    public function getContractorsExcelExport()
    {
        
        return Excel::download(new ContractorsExport, 'Contractors.xlsx');
    }
}
