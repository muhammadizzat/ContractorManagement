<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\User;
use App\Contractor;
use App\DeveloperContractorAssociation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ContractorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
    }

    public function postSearchContractors(Request $request)
    {
        $pageLimit = $request->get("page_limit");
        $nameQuery = $request->get("q");
        $defectTypeId = $request->get("defect_type_id");
        $developer_id = auth()->user()->clerk_of_work->developer_id;

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
}
