<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\User;
use App\Defect;
use App\Project;
use App\ProjectCase;
use App\CaseTag;
use Carbon\Carbon;
use App\Constants\CaseStatus;
use App\Services\RunningNumberService;

use App\Http\Resources\Developer\ProjectCaseResource;
use App\Http\Resources\Developer\ProjectCaseInfoResource;

use App\Notifications\CaseCowAssigned;
use App\Notifications\CaseCowUnassigned;
use App\Notifications\NewCaseStatus;

class CaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }

    public function getProjectCases($proj_id)
    {
        $devId = Auth::user()->clerk_of_work->developer_id;

        $project = Project::with(['cases.defects.assigned_contractor', 'cases.assigned_cow', 'cases.unit'])->where('developer_id', $devId)->where('id', $proj_id)->first();

        return response()->json(ProjectCaseInfoResource::collection($project->cases), 200);
    }

    public function getProjectCase($proj_id, $case_id)
    {
        $devId = Auth::user()->clerk_of_work->developer_id;

        $case = ProjectCase::with(['defects.assigned_contractor', 'defects.type', 'assigned_cow', 'unit', 'tags'])->where('developer_id', $devId)->where('id', $case_id)->first();

        return response()->json(new ProjectCaseResource($case), 200);
    }

    public function postAddCase($proj_id)
    {
        $data = request()->validate([
            'unit_id' => 'required|integer',
            'title' => 'required',
        ]);

        $user = auth()->user();

        $data['developer_id'] = $user->clerk_of_work->developer_id;
        $data['project_id'] = $proj_id;
        $data['status'] = CaseStatus::OPEN;
        $data['created_by'] = $user->id;
        $data['ref_no'] = RunningNumberService::nextCaseRefNo($data['developer_id']);

        $case = ProjectCase::create($data);

        return response()->json(new ProjectCaseResource($case), 200);
    }

    public function postAssignCow($proj_id, $id)
    {
        $data = request()->validate([
            'assigned_cow_user_id' => 'required',
        ]);

        $user = request()->user();

        $case = ProjectCase::find($id);

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
    }

    public function postCaseStatus($proj_id, $id)
    {
        $data = request()->validate([
            'status' => 'required',
        ]);

        if (!array_key_exists($data['status'], CaseStatus::$dict)) {
            return response()->json('Invalid status', 400);
        }

        $defects = Defect::forProject($proj_id)->unclosed()->where('case_id', $id)->count();

        if ($defects == 0) {
            $user = request()->user();
            $case = ProjectCase::find($id);
            $previous_status = $case->status;
            $case->status = $data['status'];

            if ($case->status != 'closed') {
                $case->closed_date = null;
                $case->save();
            } else {
                $case->closed_date = Carbon::now();
                $case->save();
            }

            if (!empty($case->assigned_cow) && ($case->assigned_cow->id != auth()->user()->id)) {
                $case->assigned_cow->notify(new NewCaseStatus($case->project, $case, $previous_status, $user->name));
            }

            return response()->json("Case status successfully updated!");

        } else {
            return response()->json('Case status cannot be updated, case has '. $defects . ' non-closed defect(s)', 400);
        }
    }

    public function postCaseDescription($proj_id, $id)
    {
        $data = request()->validate([
            'description' => 'required|max:2048',
        ]);

        $case = ProjectCase::find($id);

        $case->description = $data['description'];

        $case->save();

        return response()->json("Case description successfully updated!");
    }

    public function postCaseTags($proj_id, $id)
    {
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
    }
}
