<?php

namespace App\Http\Controllers\Developer\Admin;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

use App\Constants\CaseStatus;
use App\Constants\DefectStatus;
use App\Services\RunningNumberService;
use Carbon\Carbon;
use App\ProjectCase;
use App\CaseTag;
use App\Unit;
use App\User;
use App\Defect;
use App\Http\Controllers\Controller;
use App\Notifications\NewCaseStatus;
use App\Notifications\CaseCowAssigned;
use App\Notifications\CaseCowUnassigned;

use Illuminate\Notifications\Notifiable;

use Maatwebsite\Excel\Facades\Excel;
use App\Excel\Exports\CaseReportExport;

class CaseController extends Controller
{
    use Notifiable;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:dev-admin');
        $this->middleware('project.dev-admin.access');
    }


    public function index($proj_id)
    {
        $cases = ProjectCase::where('project_id', $proj_id)->get();
        return view('dev-admin.projects.cases.index', ['cases' => $cases, 'proj_id' => $proj_id]);
    }

    public function getDataTableCases(Request $request, $proj_id)
    {
        
        $cases = ProjectCase::where('project_cases.project_id', $proj_id)
            ->select([
                'project_cases.id',
                'project_cases.title',
                'project_cases.status',
                \DB::raw('count(case when defects.status like "open" then 1 end) + 
                            count(case when defects.status like "wip" then 1 end) as count'),
                'project_cases.created_by',
                'project_cases.project_id',
                'units.unit_no as unit_no'
            ])
            ->leftjoin('defects', 'defects.case_id', '=', 'project_cases.id')
            ->leftjoin('units', 'units.id', '=', 'project_cases.unit_id')
            ->groupBy('project_cases.id');
            
        return DataTables::of($cases)
            ->addIndexColumn()
            ->addColumn('viewUrl', function ($row) {  
                return route('dev-admin.projects.cases.view', ['proj_id' => $row->project_id, 'id' => $row->id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('dev-admin.projects.cases.delete', ['proj_id' => $row->project_id, 'id' => $row->id]);
            })
            ->make(true);
    }

    public function addCase($proj_id)
    {
        $units = Unit::where('project_id', $proj_id)->get();
        return view('dev-admin.projects.cases.add', ['proj_id' => $proj_id, 'units' => $units]);
    }

    public function postAddCase($proj_id)
    {
        $data = request()->validate([
            'unit_id' => 'required|integer',
            'title' => 'required',
            'description' => 'required|max:2048',
            'tags' => ''
        ]);

        $user = auth()->user();
        
        $description = $data['description'];
        $json_description = json_encode(array("ops"=> array(array("insert"=>"$description\n"))));
        
        $data['developer_id'] = $user->developer_admin->developer_id;
        $data['project_id'] = $proj_id;
        $data['status'] = CaseStatus::OPEN;
        $data['created_by'] = $user->id;
        $data['ref_no'] = RunningNumberService::nextCaseRefNo($data['developer_id']);
        $data['description'] = $json_description;

        $case = ProjectCase::create($data);

        if ($data['tags']) {
            $tag_array = explode(',', $data['tags']);
    
            foreach ($tag_array as $tag) {
                CaseTag::create([
                    'case_id' => $case->id,
                    'tag' => $tag,
                ]);
            }
        }
        
        return redirect()->route('dev-admin.projects.cases.view', ['proj_id' => $proj_id, 'id' => $case->id])->with('status', 'Case is successfully added.');
    }

    public function caseReportExport($proj_id, $id)
    {
        return Excel::download(new CaseReportExport($id), 'Case Report.xlsx');
    }

    public function deleteCase(Request $request, $proj_id)
    {
        $defects = Defect::where('case_id', $request->id)->count();

        $case = ProjectCase::find($request->id);

        if ($defects == 0) {

            $case->delete();

            return redirect()->route('dev-admin.projects.cases.index', ['project_id' => $proj_id])->with('status', 'Case is successfully deleted.');
        } else {
            return redirect()->route('dev-admin.projects.cases.index', ['project_id' => $proj_id])->with('error', 'Case cannot be deleted due to defects already created under this case');
        }
    }


    public function viewCase($proj_id, $id)
    {
        $case = ProjectCase::find($id);

        if ($case->project_id != $proj_id) {
            abort(403, 'Unauthorized action.');
        }

        $developer_id = request()->user()->developer_admin->developer_id;

        return view('dev-admin.projects.cases.view', ['developer_id' => $developer_id, 'proj_id' => $proj_id, 'case' => $case]);
    }

    public function ajaxPostCaseStatus($proj_id, $id)
    {
        $data = request()->validate([
            'status' => 'required',
        ]);

        $defects = Defect::forProject($proj_id)->unclosed()->where('case_id', $id)->count();

        if ($defects == 0) {
            $user = request()->user();
            $case = ProjectCase::find($id);
            $previous_status = $case->status;
            $case->status = $data['status'];

            if($data['status'] == 'open') {
                $case->closed_date = null;
                $case->save();   
            }
            if($data['status'] == 'closed'){
                $case->closed_date = Carbon::now();
                $case->save();   
            }
            
            // Notifications
            if (!empty($case->assigned_cow) && ($case->assigned_cow->id != auth()->user()->id)) {
                $case->assigned_cow->notify(new NewCaseStatus($case->project, $case, $previous_status, $user->name));
            }
            return response()->json("Case status successfully updated!");
        } else {
            return response()->json("Case status cannot be updated!");
        }
    }

    public function ajaxPostCaseDescription($proj_id, $id)
    {
        $case = ProjectCase::find($id);
        if($case->status != 'closed'){
            $data = request()->validate([
                'description' => 'required|max:2048',
            ]);
    
            $case->description = $data['description'];
    
            $case->save();
    
            return response()->json("Case description successfully updated!");
        } else {
            return response()->json("Unable to to update description as the case has been closed");
        }
    }

    public function ajaxPostAssignCow($proj_id, $id)
    {
        $case = ProjectCase::find($id);
        if($case->status != 'closed'){
            $data = request()->validate([
                'assigned_cow_user_id' => 'required',
            ]);
    
            $user = request()->user();
    
            if (!empty($case->assigned_cow)) {
                $case->assigned_cow->notify(new CaseCowUnassigned($case->project, $case, $user->name));
            }
    
            $case_assigned_cow = User::find($data['assigned_cow_user_id']);
            $case->assigned_cow()->associate($case_assigned_cow);
    
            $case->save();
    
            if ($case_assigned_cow->id != auth()->user()->id) {
                $case->assigned_cow->notify(new CaseCowAssigned($case->project, $case, $user->name));
            }
            return response()->json("Case assigned CoW successfully updated!");
        } else {
            return response()->json("Unable to assign cow as the case has been closed");
        }
    }

    public function ajaxPostCaseTags($proj_id, $id)
    {
        $case = ProjectCase::find($id);
        if($case->status != 'closed'){
            $data = request()->validate([
                'tags' => '',
            ]);
    
            CaseTag::where('case_id', $id)->delete();
    
            if ($data['tags']) {
                $tag_array = explode(',', $data['tags']);
        
                foreach ($tag_array as $tag) {
                    CaseTag::create([
                        'case_id' => $id,
                        'tag' => $tag,
                    ]);
                }
            }
            return response()->json("Case tags successfully updated!");
        } else {
            return response()->json("Unable to update case tags as the case has been closed");
        }
    }
}
