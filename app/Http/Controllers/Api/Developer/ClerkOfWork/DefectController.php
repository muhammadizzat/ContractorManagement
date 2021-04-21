<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


use Illuminate\Support\Facades\Auth;

use App\User;
use App\ProjectCase;
use App\Defect;
use App\DefectTag;
use App\DefectImage;
use App\DefectActivityImage;
use App\Media;
use App\Project;
use App\Notifications\NewDefect;
use App\Notifications\PinUpdated;
use App\Notifications\NewDefectActivity;
use App\Notifications\DefectRequestResponse;
use App\Notifications\DefectContractorUnassign;
use App\Notifications\DefectContractorAssign;
use App\Notifications\DefectDueDateExtended;
use App\Notifications\NewDefectStatus;

use ImageOptimizer;
use App\Constants\DefectStatus;
use App\Constants\DefectConfig;
use App\Constants\DefectClosedStatus;
use App\Services\RunningNumberService;

use App\Http\Resources\Developer\DefectResource;
use App\Http\Resources\Developer\DefectActivityResource;

class DefectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }

    public function getDefect($proj_id, $case_id, $defect_id)
    {
        $devId = Auth::user()->clerk_of_work->developer_id;

        $defect = Defect::with(['case', 'assigned_contractor', 'images', 'type', 'pins', 'tags'])->where('developer_id', $devId)
            ->where('case_id', $case_id)
            ->where('id', $defect_id)
            ->first();

        return response()->json(new DefectResource($defect), 200);
    }

    public function getAllDefects($proj_id)
    {
        $defects = Defect::with(['case', 'type', 'assigned_contractor'])
            ->forProject($proj_id)
            ->where('status', '!=', 'closed')
            ->get();
        return response()->json(DefectResource::collection($defects), 200);
    }

    public function postDefect($proj_id, $case_id) {
        $data = request()->validate([
            'title' => 'required',
            'defect_type_id' => 'required',
            'due_date' => '',
            'description' => '',
        ]);

        $user_name = auth()->user()->name;

        $data['status'] = DefectStatus::OPEN;

        $case = ProjectCase::find($case_id);

        $data['developer_id'] = $case->developer_id;
        $data['project_id'] = $case->project_id;
        $data['ref_no'] = RunningNumberService::nextCaseDefectNo($case->developer_id);

        $defect = $case->defects()->create($data);

        if (!empty($case->assigned_cow)) {
            $case->assigned_cow->notify(new NewDefect($case->project, $case, $defect, $user_name));
        }

        return response()->json(new DefectResource($defect), 200);
    }

    public function getDefectImage($proj_id, $case_id, $defect_id, $id) {
        $defectImage = DefectImage::find($id);
        $imageMedia = $defectImage->image_media;
        $dataUrl = 'data:'.$imageMedia->mimetype.';base64,'.base64_encode($imageMedia->data);
        return response()->json($dataUrl, 200);
    }

    public function postDefectImage($proj_id, $case_id, $id)
    {
        $data = request()->validate([
            'image_data_url' => 'required',
        ]);

        $defect = Defect::find($id);

        if ($defect->images()->count() > 5) {
            return response()->json(self::error("Maximum images for this defect already!"));
        }


        $imageData = self::processBase64DataUrl($data['image_data_url']);
        $imageData = self::optimizeImage($imageData);

        $imageMedia = Media::create([
            'category' => 'defect-image',
            'mimetype' => $imageData['mime_type'],
            'data' => $imageData['data'],
            'size' => $imageData['size'],
            'filename' => 'defect_img_' . ($id) . '_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'],
            'created_by' => auth()->user()->id
        ]);

        $defect->images()->create([
            'media_id' => $imageMedia->id
        ]);

        return response()->json("Defect image successfully added!");
    }

    public function postDeleteDefectImage($proj_id, $case_id, $defect_id, $id)
    {
        $defectImage = DefectImage::find($id);

        $media = $defectImage->image_media;
        $defectImage->delete();
        $media->delete();

        return response()->json("Defect image successfully deleted!");
    }


    // API: Update defect

    public function postPins($proj_id, $case_id, $id)
    {
        $data = request()->validate([
            'unit_type_floor_id' => 'present',
            'pins' => 'present',
        ]);

        $defect = Defect::find($id);

        $defect->unit_type_floor_id = $data['unit_type_floor_id'];
        $defect->save();

        // Remove pins with ID not in new pins
        $updated_pins = $data['pins'];
        $updated_existing_pin_ids = [];
        foreach ($updated_pins as $updated_pin) {
            if (!empty($updated_pin['id'])) {
                $updated_existing_pin_ids[] = $updated_pin['id'];
            }
        }

        $defect->pins()
            ->whereNotIn('id', $updated_existing_pin_ids)
            ->delete();

        // Create or update new pins
        foreach ($updated_pins as $updated_pin) {
            if (!empty($updated_pin['id'])) {
                $defect->pins()->where('id', $updated_pin['id'])->update($updated_pin);
            } else {
                $defect->pins()->create($updated_pin);
            }
        }

        if (!empty($defect->case->assigned_cow) && ($defect->case->assigned_cow->id != auth()->user()->id)) {
            $defect->case->assigned_cow->notify(new PinUpdated($defect->case->project, $defect->case, $defect, auth()->user()->name));
        }

        if (!empty($defect->case->defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new PinUpdated($defect->case->project, $defect->case, $defect, auth()->user()->name));
        }

        return response()->json($defect->pins);
    }

    public function postDefectTags($proj_id, $case_id, $id)
    {
        $data = request()->validate([
            'tags' => '',
        ]);

        DefectTag::where('defect_id', $id)->delete();

        if ($data['tags']) {
            $tag_array = explode(',', $data['tags']);

            foreach ($tag_array as $tag) {
                DefectTag::create([
                    'defect_id' => $id,
                    'tag' => $tag,
                ]);
            }
        }

        return response()->json("Defect tags successfully updated!");
    }

    // API: Activity

    public function getDefectActivities($proj_id, $case_id, $defect_id)
    {
        $defect = Defect::with(['activities.user.roles', 'activities.images', 'activities.request_response_user.roles'])
            ->where('project_id', $proj_id)
            ->where('case_id', $case_id)
            ->where('id', $defect_id)
            ->first();

        return response()->json(DefectActivityResource::collection($defect->activities), 200);
    }

    public function getDefectActivityImage($proj_id, $case_id, $defect_id, $activity_id, $id) {
        $defectActivityImage = DefectActivityImage::find($id);
        $imageMedia = $defectActivityImage->image_media;
        $dataUrl = 'data:'.$imageMedia->mimetype.';base64,'.base64_encode($imageMedia->data);
        return response()->json($dataUrl, 200);
    }

    public function postAddActivityComment($proj_id, $case_id, $id)
    {

        $data = request()->validate([
            'comment' => 'required',
        ]);

        $imagesData = request()->validate([
            'images' => '',
        ]);

        $defect = Defect::where('project_id', $proj_id)
        ->where('case_id', $case_id)
        ->where('id', $id)
        ->first();


        $activity = $defect->activities()->create([
            'type' => 'comment',
            'user_id' => auth()->user()->id,
            'content' => $data['comment']
        ]);

        if(!empty($imagesData['images'])) {
            foreach($imagesData['images'] as $imageDataUrl) {
                $imageData = self::processBase64DataUrl($imageDataUrl);
                $imageData = self::optimizeImage($imageData);

                $imageMedia = Media::create([
                    'category' => 'defect-activity-image',
                    'mimetype' => $imageData['mime_type'],
                    'data' => $imageData['data'],
                    'size' => $imageData['size'],
                    'filename' => 'defect_act_img_'.($id).'_'.date('Y-m-d_H:i:s').".".$imageData['extension'],
                    'created_by' => auth()->user()->id
                ]);

                $activity->images()->create([
                    'media_id' => $imageMedia->id
                ]);
            }
        }

        // Notify: Defect Activity -> COW (if not self), Assigned Contractor (if not self)
        if (!empty($defect->case->assigned_cow) && ($defect->case->assigned_cow->id != auth()->user()->id)) {
            $defect->case->assigned_cow->notify(new NewDefectActivity($defect->case->project, $defect->case, $defect, $activity));
        }

        if (!empty($defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new NewDefectActivity($defect->case->project, $defect->case, $defect, $activity));
        }

        return response()->json($activity);
    }

    public function postDescription($proj_id, $case_id, $id)
    {
        $data = request()->validate([
            'description' => 'required|max:2048',
        ]);

        $defect = Defect::find($id);

        $defect->description = $data['description'];

        $defect->save();

        return response()->json("Description successfully updated!");
    }

    private static function processBase64DataUrl($dataUrl) {
        $parts = explode(',', $dataUrl);

        preg_match('#data:(.*?);base64#', $parts[0], $matches);
        $mimeType = $matches[1];
        $extension = explode('/', $mimeType)[1];

        $data = base64_decode($parts[1]);

        return [
            'data' => $data,
            'mime_type' => $mimeType,
            'size' => mb_strlen($data),
            'extension' => $extension
        ];
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

    public function postDefectContractor($proj_id, $case_id, $id)
    {
        $data = request()->validate([
            'assigned_contractor_user_id' => 'required',
        ]);

        $user = request()->user();

        $defect = Defect::find($id);

        $prev_contractor = $defect->assigned_contractor;

        if (!empty($prev_contractor)) {
            if (!empty($defect->case->assigned_cow) && ($defect->case->assigned_cow->id != auth()->user()->id)) {
                $defect->case->assigned_cow->notify(new DefectContractorUnassign($defect->case->project, $defect->case, $defect, $user->name));
            }

            $defect->assigned_contractor->notify(new DefectContractorUnassign($defect->case->project, $defect->case, $defect, $user->name));
        }

        $defect_assigned_contractor = User::find($data['assigned_contractor_user_id']);
        $defect->assigned_contractor()->associate($defect_assigned_contractor);

        $defect->save();

        if (!empty($defect->case->assigned_cow)) {
            $defect->case->assigned_cow->notify(new DefectContractorAssign($defect->case->project, $defect->case, $defect, $user->name));
        }

        if (!empty($defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new DefectContractorAssign($defect->case->project, $defect->case, $defect, $user->name));
        }

        if (!empty($prev_contractor)) {
            $activity = $defect->activities()->create([
                'type' => 'update',
                'user_id' => auth()->user()->id,
                'content' => 'Changed assigned contractor from \'' . $prev_contractor->name . '\' to \'' . $defect_assigned_contractor->name . '\''
            ]);
        } else {
            $activity = $defect->activities()->create([
                'type' => 'update',
                'user_id' => auth()->user()->id,
                'content' => 'Assigned contractor \'' . $defect_assigned_contractor->name . '\''
            ]);
        }

        return response()->json("Case assigned Contractor successfully updated!");
    }

    public function postDefectStatus($proj_id, $case_id, $id)
    {
        $data = request()->validate([
            'status' => 'required',
        ]);

        $defect = Defect::find($id);

        $prev_status = $defect->status;
        $defect->status = $data['status'];

        if (($prev_status == 'resolved') && ($data['status'] != 'closed')) {
            $defect->resolved_date = null;
        }

        if ($prev_status == 'closed') {
            $defect->resolved_date = null;
            $defect->closed_date = null;
        }

        if ($data['status'] == 'resolved') {
            $defect->resolved_date = Carbon::now();
            $defect->closed_date = null;
        }

        if ($data['status'] == 'closed') {
            $defect->closed_date = Carbon::now();
        }

        // Closed Status
        if($prev_status == 'closed') {
            $defect->closed_status = null;
            $defect->reject_reason = null;
            $defect->duplicate_defect_id = null;
        }

        if ($data['status'] == 'closed') {
            $closedStatusData = request()->validate([
                'closed_status' => 'in:duplicate,reject'
            ]);

            if(!empty($closedStatusData['closed_status'])) {
                switch($closedStatusData['closed_status']) {
                    case "duplicate":
                        $closedStatusData = request()->validate([
                            'duplicate_defect_id' => 'required|integer',
                        ]);

                        $defect->closed_status = "duplicate";
                        $defect->duplicate_defect_id =  $closedStatusData['duplicate_defect_id'];
                        break;
                    case "reject":
                        $closedStatusData = request()->validate([
                            'reject_reason' => 'required',
                        ]);

                        $defect->closed_status = "reject";
                        $defect->reject_reason =  $closedStatusData['reject_reason'];
                        break;
                }
            }
        }

        $defect->save();

        $activity = $defect->activities()->create([
            'type' => 'update',
            'user_id' => auth()->user()->id,
            'content' => 'Changed status from \''.DefectStatus::$dict[$prev_status].'\' to \''.DefectStatus::$dict[$data['status']].'\''
        ]);

        if (!empty($defect->case->assigned_cow) && ($defect->case->assigned_cow->id != auth()->user()->id)) {
            $defect->case->assigned_cow->notify(new NewDefectStatus($defect->case->project, $defect->case,  $defect, $prev_status, $activity));
        }

        if (!empty($defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new NewDefectStatus($defect->case->project, $defect->case,  $defect, $prev_status, $activity));
        }

        return response()->json("Defect status successfully updated!");
    }

    public function postRequestResponse($proj_id, $case_id, $defect_id, $activity_id)
    {
        $data = request()->validate([
            'response' => 'required|in:approve,reject',
        ]);

        $defect = Defect::find($defect_id);

        $defectActivity = $defect->activities()->where('id', $activity_id)->first();

        if($defectActivity->type != "request" || !empty($defectActivity->request_response)) {
            return response()->json(null, 400);
        }

        switch($data['response']) {
            case "approve":
                $defectActivity->request_response = "approved";

                switch($defectActivity->request_type) {
                    case "close":
                        $this->updateDefectStatus($proj_id, $case_id, $defect_id, DefectStatus::CLOSED);
                        break;
                    case "extend":
                        $this->extendDefectDueDate($proj_id, $case_id, $defect_id);
                        break;
                    case "reject":
                        $this->updateDefectStatus($proj_id, $case_id, $defect_id, DefectStatus::CLOSED, DefectClosedStatus::REJECTED);
                        break;
                }
                break;
            case "reject":
                $defectActivity->request_response = "rejected";
                break;
        }

        $defectActivity->request_response_user_id = auth()->user()->id;

        $defectActivity->save();

        if (!empty($defect->case->assigned_cow) && ($defect->case->assigned_cow->id != auth()->user()->id)) {
            $defect->case->assigned_cow->notify(new DefectRequestResponse($defect->case->project, $defect->case, $defect, auth()->user()->name));
        }

        if (!empty($defect->case->defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new DefectRequestResponse($defect->case->project, $defect->case, $defect, auth()->user()->name));
        }

        return response()->json("Defect request successfully updated!");
    }

    public function getPendingRequest($proj_id, $case_id, $id) {
        $defect = Defect::find($id);


        $defectActivity = $defect->activities()->with(['user.roles', 'request_response_user.roles'])
            ->where('type', 'request')
            ->whereNull('request_response')
            ->first();

        if(empty($defectActivity)) {
            return response()->json(null, 204);
        }

        return response()->json(new DefectActivityResource($defectActivity));
    }

    // Requests Functions

    private function updateDefectStatus($proj_id, $case_id, $id, $status, $closedStatus = null)
    {
        $defect = Defect::find($id);

        $prev_status = $defect->status;
        $defect->status = $status;

        if ($status == 'resolved') {
            $defect->resolved_date = Carbon::now();
        }

        if ($status == 'closed') {
            $defect->closed_date = Carbon::now();
        }

        $defect->save();

        $activity = $defect->activities()->create([
            'type' => 'defect-status',
            'user_id' => auth()->user()->id,
            'content' => 'Changed status from \'' . DefectStatus::$dict[$prev_status] . '\' to \'' . DefectStatus::$dict[$status] . '\''
        ]);

        if(!empty($defect->case->assigned_cow)) {
            $defect->case->assigned_cow->notify(new NewDefectStatus($defect->case->project, $defect->case, $defect, $prev_status, $activity));
        }

        if(!empty($defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new NewDefectStatus($defect->case->project, $defect->case, $defect, $prev_status, $activity));
        }
    }

    private function extendDefectDueDate($proj_id, $case_id, $id)
    {
        $defect = Defect::find($id);
        $defect->due_date = $defect->due_date->addDays(DefectConfig::EXPIRY_DAYS);
        $defect->extended_count = $defect->extended_count + 1;
        $defect->save();

        $activity = $defect->activities()->create([
            'type' => 'update',
            'user_id' => auth()->user()->id,
            'content' => 'Due date was extended'
        ]);

        if (!empty($defect->case->assigned_cow) && ($defect->case->assigned_cow->id != auth()->user()->id)) {
            $defect->case->assigned_cow->notify(new DefectDueDateExtended($defect->case->project, $defect->case,  $defect, auth()->user()->name));
        }

        if (!empty($defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new DefectDueDateExtended($defect->case->project, $defect->case,  $defect, auth()->user()->name));
        }
    }

    // SECTION: Defects

    function postSearchDefects(Request $request, $proj_id) {
        $pageLimit = $request->get("page_limit");
        $searchQuery = $request->get("q");

        // Process Query
        $case_ref_no;
        $defect_ref_no;
        if(!empty($searchQuery)) {
            preg_match('/(C(?<case_ref_no>\d+))?(-)?(D(?<defect_ref_no>\d+))?/', $searchQuery, $matches);

            if(!empty($matches['case_ref_no'])) {
                $case_ref_no = $matches['case_ref_no'];
            }
            if(!empty($matches['defect_ref_no'])) {
                $defect_ref_no = $matches['defect_ref_no'];
            }
        }

        $defects = Defect::forProject($proj_id);

        if(!empty($case_ref_no)) {
            $defects->whereHas('case', function($q) use ($case_ref_no) {
                $q->where('project_cases.ref_no', $case_ref_no);
            });
        }

        if(!empty($defect_ref_no)) {
            $defects->where('defects.ref_no', $defect_ref_no);
        }

        $results = $defects
        ->join('project_cases', 'defects.case_id', '=', 'project_cases.id')
        ->select(['defects.id', 'defects.ref_no', 'defects.title', 'project_cases.ref_no AS case_ref_no'])->get();


        return response()->json($results);
    }
}
