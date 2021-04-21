<?php

namespace App\Http\Controllers\Developer\Admin;

use App\DeveloperContractorAssociation;
use App\Contractor;
use App\Developer;
use App\Defect;
use App\DefectType;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

use App\Excel\Exports\ContractorAssociationsExport;

class ContractorAssociationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
    }

    public function index()
    {                           
        return view('dev-admin.associations.index');
    }

    public function getDataTableContractorAssociation(Request $request)
    {
        if ($request->ajax()) {
            $developer_id = request()->user()->developer_admin->developer_id;
            $developer_contractor_association = DeveloperContractorAssociation::join('users', 'users.id', '=', 'developer_contractor_associations.contractor_user_id')
                                                                              ->join('contractors', 'users.id', '=', 'contractors.user_id')
                                                                              ->where('developer_contractor_associations.developer_id', $developer_id)
                                                                              ->select('developer_contractor_associations.id as id', 
                                                                                       'users.name as name', 
                                                                                       'contractors.contact_no as contact_no', 
                                                                                       'users.email as email', 
                                                                                       'developer_contractor_associations.created_at as created_at');
            return DataTables::of($developer_contractor_association)
                ->addIndexColumn()
                ->addColumn('editAssociationUrl', function ($developer_contractor_association) {
                    return route('dev-admin.associations.edit', ['id' => $developer_contractor_association['id']]);
                })
                ->addColumn('deleteAssociationUrl', function ($developer_contractor_association) {
                    return route('dev-admin.associations.delete', ['id' => $developer_contractor_association['id']]);
                })
                ->make(true);
        }
    }

    public function getContractorAssociationsExcelExport()
    {
        $developer_id = request()->user()->developer_admin->developer_id;
        return Excel::download(new ContractorAssociationsExport($developer_id), 'Contractor Scope of Work.xlsx');
    }
    
    public function addContractorAssociation(Request $request, DeveloperContractorAssociation $developer_contractor_association)
    {
        $developer_id = request()->user()->developer_admin->developer_id;
        $defect_type_list = DefectType::where('developer_id',$developer_id)->orWhere('developer_id', null)->select('id','title')->get();
        
        return view('dev-admin.associations.add', ['developer_id' =>$developer_id, 'defect_type_list' => $defect_type_list]);
    }
    
    public function getContractorProfile(Request $request)
    {
        $data = $request->validate(['contractor_email' => 'required|email']);
        $contractor = Contractor::whereHas('user', function ($query) use($data) {
            $query->where('email', $data['contractor_email']);
        })->first();

        if($contractor){
            $developer_id = request()->user()->developer_admin->developer_id;
            $developer_contractor_association = DeveloperContractorAssociation::where('contractor_user_id', $contractor->user_id)->where('developer_id', $developer_id)->first();
            
            if($developer_contractor_association){
                $contractor = [
                    'message' => "Contractor has already associated",
                ];
            } else {
                $contractor['name'] = $contractor->user->name;
                $contractor['contractor_user_id'] = $contractor->user->id;               
            }
        } else {
            $contractor = [
                'message' => "Contractor with the specified e-mail was not found",
            ];
        }
        return $contractor;
    }

    public function postAddContractorAssociation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'defect_type_ids' => 'required',
            'contractor_user_id' => 'required'
        ]);

        $developer_id = request()->user()->developer_admin->developer_id;

        if ($validator->fails()) {
            return back()->with('error', 'Please select any related defect type(s)');
        }

        $contractorData = ([
            'contractor_user_id' => $request['contractor_user_id'],
            'developer_id' => $developer_id,
        ]);

        $dca = DeveloperContractorAssociation::create($contractorData);
        $dca->defect_types()->sync($request['defect_type_ids']);
        return redirect()->route('dev-admin.associations.index')->withStatus(__('Contractor Scope of Work successfully added.'));      
    }

    public function editContractorAssociation(DeveloperContractorAssociation $developer_contractor_association, Request $request, $id)
    {
        $developer_contractor_association = DeveloperContractorAssociation::whereHas('defect_types', function ($query) use ($id) {
            $query->where('developer_contractor_associations.id', $id);
        })->orwhereHas('user', function ($query) use ($id) {
            $query->where('developer_contractor_associations.id', $id);
        })->first();
        $developer_id = $developer_contractor_association['developer_id'];
        $defect_type_list = DefectType::where('developer_id',$developer_id)->orWhere('developer_id', null)->select('id','title')->get();

        return view('dev-admin.associations.edit', ['developer_contractor_association' => $developer_contractor_association, 'id' => $id, 'developer_id' =>$developer_id, 'defect_type_list' => $defect_type_list]);
    }

    public function postEditContractorAssociation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dca_id' => 'required',
            'defect_type_ids' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Please select any related defect type(s)');
        }

        $dca = DeveloperContractorAssociation::findOrFail($request['dca_id']);
        $dca->defect_types()->sync($request['defect_type_ids']);
        return redirect()->route('dev-admin.associations.index')->with('status', 'Contractor Assocation has been edited.');       
    }
    
    public function deleteContractorAssociation(Request $request)
    {
        $id = $request->id;
        $dca = DeveloperContractorAssociation::findOrFail($id);
        $defect_count = Defect::where('assigned_contractor_user_id', $dca->contractor_user_id)->count();

        if ($defect_count != 0){
            return redirect()->route('dev-admin.associations.index')->with('error', 'This contractor is being assigned to defect(s).');
        } else {
            $dca->defect_types()->sync([]);
            $dca->delete();        
            return redirect()->route('dev-admin.associations.index')->with('status', 'Contractor Scope of Work has been successfully deleted.');
        };
    }
}
