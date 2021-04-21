<?php

namespace App\Http\Controllers\Developer\Admin;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

use Carbon\Carbon;
use ImageOptimizer;

use App\User;
use App\ProjectCase;
use App\Defect;
use App\DefectActivity;
use App\DefectImage;
use App\DefectTag;
use App\DefectActivityImage;
use App\Media;

use App\Constants\DefectStatus;
use App\Constants\DefectClosedStatus;
use App\Constants\DefectConfig;
use App\Services\RunningNumberService;
use App\Notifications\NewDefect;
use App\Notifications\PinUpdated;
use App\Notifications\DefectDueDateExtended;
use App\Notifications\DefectImageUpdated;
use App\Notifications\DefectRequestResponse;
use App\Notifications\NewDefectActivity;
use App\Notifications\DefectContractorUnassign;
use App\Notifications\DefectContractorAssign;
use App\Notifications\NewDefectStatus;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use Maatwebsite\Excel\Facades\Excel;
use App\Excel\Exports\DefectReportExport;

class DefectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:dev-admin');
        $this->middleware('project.dev-admin.access');
    }

    public function ajaxGetDefects($proj_id, $case_id)
    {
        $defects = Defect::with(['assigned_contractor.contractor', 'type'])->where('case_id', $case_id)->get();

        return $defects;
    }

    public function ajaxGetDefectInfo($proj_id, $case_id, $id)
    {
        $defect = Defect::with(['type', 'assigned_contractor.contractor', 'images', 'pins', 'tags'])
            ->where('project_id', $proj_id)
            ->where('case_id', $case_id)
            ->where('id', $id)
            ->first();
        return $defect;
    }

    public function ajaxPostDefect($proj_id, $case_id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $data = request()->validate([
                'title' => 'required',
                'defect_type_id' => 'required',
                'description' => 'max:2048',
            ]);
    
            $user_name = auth()->user()->name;
    
            $due_date = Carbon::now();
            $data['due_date'] = $due_date->addDays(30)->format('Y-m-d');
            $data['status'] = DefectStatus::OPEN;
            $case = ProjectCase::find($case_id);
    
            $data['developer_id'] = $case->developer_id;
            $data['project_id'] = $case->project_id;
            $data['ref_no'] = RunningNumberService::nextCaseDefectNo($case->developer_id);
    
            $defect = $case->defects()->create($data);
    
            if (!empty($case->assigned_cow)) {
                $case->assigned_cow->notify(new NewDefect($case->project, $case, $defect, $user_name));
            }
            return $defect;
        } else {
            return response()->json("Unable to create defect as the case has been closed");
        }
    }

    public function defectReportExport($proj_id, $case_id, $id)
    {
        return Excel::download(new DefectReportExport($id), 'Defect Report.xlsx');
    }

    
    public function ajaxGetDefectActivities($proj_id, $case_id, $id)
    {
        $defect = Defect::where('project_id', $proj_id)
            ->where('case_id', $case_id)
            ->where('id', $id)
            ->first();

        if (!empty($defect)) {
            $activities = DefectActivity::with(['user.roles', 'images', 'request_response_user.roles'])->where('defect_id', $id);
            if (request()->has('last_update_time') && !empty(request()->last_update_time)) {
                $activities->where('created_at', '>', request()->last_update_time);
            }
            return $activities->get();
        }

        return [];
    }

    public function ajaxPostAddActivityComment($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
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
        
                if (!empty($imagesData['images'])) {
                    foreach ($imagesData['images'] as $imageDataUrl) {
                        $imageData = self::processBase64DataUrl($imageDataUrl);
                        $imageData = self::optimizeImage($imageData);
        
                        $imageMedia = Media::create([
                            'category' => 'defect-activity-image',
                            'created_by' => auth()->user()->id,
                            'mimetype' => $imageData['mime_type'],
                            'data' => $imageData['data'],
                            'size' => $imageData['size'],
                            'filename' => 'defect_act_img_' . ($id) . '_' . date('Y-m-d_H:i:s') . "." . $imageData['extension']
                        ]);
        
                        $activity->images()->create([
                            'media_id' => $imageMedia->id
                        ]);
                    }
                }
        
                // Notify: Defect Activity -> COW (if not self), Assigned Contractor (if not self)
                if (!empty($defect->case->assigned_cow)) {
                    $defect->case->assigned_cow->notify(new NewDefectActivity($defect->case->project, $defect->case, $defect, $activity));
                }
        
                if (!empty($defect->assigned_contractor)) {
                    $defect->assigned_contractor->notify(new NewDefectActivity($defect->case->project, $defect->case, $defect, $activity));
                }
        
                return response()->json($activity);
            } else {
                return response()->json("Unable to create activity as the defect has been closed");
            }
        } else {
            return response()->json("Unable to create activity as the case has been closed");
        }
    }

    public function ajaxPostRequestResponse($proj_id, $case_id, $defect_id, $activity_id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $data = request()->validate([
                'response' => 'required|in:approve,reject',
            ]);
            
            $defect = Defect::find($defect_id);
            $defectActivity = $defect->activities()->where('id', $activity_id)->first();
    
            if ($defectActivity->type != "request" || !empty($defectActivity->request_response)) {
                return response()->json(null, 400);
            }
    
            switch ($data['response']) {
                case "approve":
                    $defectActivity->request_response = "approved";
    
                    switch ($defectActivity->request_type) {
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
    
            if (!empty($defect->case->assigned_cow)) {
                $defect->case->assigned_cow->notify(new DefectRequestResponse($defect->case->project, $defect->case, $defect, auth()->user()->name));
            }
    
            if (!empty($defect->assigned_contractor)) {
                $defect->assigned_contractor->notify(new DefectRequestResponse($defect->case->project, $defect->case, $defect, auth()->user()->name));
            }
    
            return response()->json("Defect request successfully updated!");
        } else {
            return response()->json("Unable to create defect request as the case has been closed");
        }
    }

    public function ajaxGetDefectActivitiesUserProfileImage($defect_id, $activity_id, $id)
    {
        $defectActivityUserProfileImage = Media::find($id);
        $imageMedia = $defectActivityUserProfileImage;
        return response()->make($imageMedia->data, 200, [
            'Content-Type' => $imageMedia->mimetype,
            'Content-Disposition' => 'inline; filename="' . $imageMedia->filename . '"'
        ]);
    }

    public function ajaxGetDefectActivityImage($proj_id, $case_id, $defect_id, $activity_id, $id)
    {
        $defectActivityImage = DefectActivityImage::find($id);

        $imageMedia = $defectActivityImage->image_media;

        return response()->make($imageMedia->data, 200, [
            'Content-Type' => $imageMedia->mimetype,
            'Content-Disposition' => 'inline; filename="' . $imageMedia->filename . '"'
        ]);
    }

    public function ajaxPostDescription($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
                $data = request()->validate([
                    'description' => 'required|max:2048',
                ]);
        
                $defect->description = $data['description'];
                $defect->save();
        
                return response()->json("Description successfully updated!");
            } else {
                return response()->json("Unable to create description as the defect has been closed");
            }
        } else {
            return response()->json("Unable to create description as the case has been closed");
        }
    }

    public function ajaxPostPins($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
                $data = request()->validate([
                    'unit_type_floor_id' => 'present',
                    'pins' => 'filled',
                ]);
        
                $defect->unit_type_floor_id = $data['unit_type_floor_id'];
                $defect->save();
        
                // Remove pins with ID not in new pins
                if(!empty($data['pins'])) {
                    $updated_pins = $data['pins'];
                } else {
                    $updated_pins = [];
                }
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
        
                $activity = $defect->activities()->create([
                    'type' => 'update',
                    'user_id' => auth()->user()->id,
                    'content' => 'Pins were updated'
                ]);
        
                if (!empty($defect->case->assigned_cow)) {
                    $defect->case->assigned_cow->notify(new PinUpdated($defect->case->project, $defect->case, $defect, auth()->user()->name));
                }
        
                if (!empty($defect->assigned_contractor)) {
                    $defect->assigned_contractor->notify(new PinUpdated($defect->case->project, $defect->case, $defect, auth()->user()->name));
                }
        
                return response()->json($defect->pins);
            } else {
                return response()->json("Unable to create pin as the defect has been closed");
            }
        } else {
            return response()->json("Unable to create pin as the case has been closed");
        }
    }

    public function ajaxPostDefectImage($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
                $data = request()->validate([
                    'image_data_url' => 'required',
                ]);
                
                if ($defect->images()->count() > 5) {
                    return response()->json(self::error("Maximum images for this defect already!"));
                }
        
        
                $imageData = self::processBase64DataUrl($data['image_data_url']);
                $imageData = self::optimizeImage($imageData);
        
                $imageMedia = Media::create([
                    'category' => 'defect-image',
                    'created_by' => auth()->user()->id,
                    'mimetype' => $imageData['mime_type'],
                    'data' => $imageData['data'],
                    'size' => $imageData['size'],
                    'filename' => 'defect_img_' . ($id) . '_' . date('Y-m-d_H:i:s') . "." . $imageData['extension']
                ]);
        
                $defect->images()->create([
                    'media_id' => $imageMedia->id
                ]);
        
                $activity = $defect->activities()->create([
                    'type' => 'update',
                    'user_id' => auth()->user()->id,
                    'content' => 'Images were updated'
                ]);
        
                if (!empty($defect->case->assigned_cow)) {
                    $defect->case->assigned_cow->notify(new DefectImageUpdated($defect->case->project, $defect->case, $defect, auth()->user()->name));
                }
        
                if (!empty($defect->assigned_contractor)) {
                    $defect->assigned_contractor->notify(new DefectImageUpdated($defect->case->project, $defect->case, $defect, auth()->user()->name));
                }
        
                return response()->json("Defect image successfully added!");
            } else {
                return response()->json("Unable to create defect image as the defect has been closed");
            }
        } else {
            return response()->json("Unable to create defect image as the case has been closed");
        }
    }

    public function getDefectImage($proj_id, $case_id, $defect_id, $id)
    {
        $defectImage = DefectImage::find($id);

        $imageMedia = $defectImage->image_media;


        return response()->make($imageMedia->data, 200, [
            'Content-Type' => $imageMedia->mimetype,
            'Content-Disposition' => 'inline; filename="' . $imageMedia->filename . '"'
        ]);
    }

    private static function error($message)
    {
        return [
            'error' => true,
            'message' => $message
        ];
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
            'extension' => $extension
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

    public function ajaxPostDefectStatus($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $data = request()->validate([
                'status' => 'required',
            ]);

            $defect = Defect::find($id);
            $prev_status = $defect->status;
            $defect->status = $data['status'];
    
            // Update dates
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
                'content' => 'Changed status from \'' . DefectStatus::$dict[$prev_status] . '\' to \'' . DefectStatus::$dict[$data['status']] . '\''
            ]);
    
            if (!empty($defect->case->assigned_cow)) {
                $defect->case->assigned_cow->notify(new NewDefectStatus($defect->case->project, $defect->case, $defect, $prev_status, $activity));
            }
    
            if (!empty($defect->assigned_contractor)) {
                $defect->assigned_contractor->notify(new NewDefectStatus($defect->case->project, $defect->case, $defect, $prev_status, $activity));
            }
    
            return response()->json("Defect status successfully updated!");
        } else {
            return response()->json("Unable to update defect status as the case has been closed");
        }
    }

    public function ajaxPostDefectDueDate($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
                $data = request()->validate([
                    'due_date' => 'required',
                ]);
        
                $defect->due_date = $data['due_date'];

                $defect->save();
        
                return response()->json("Defect due date successfully updated!");
            } else {
                return response()->json("Unable to update defect due date as the defect has been closed");
            }
        } else {
            return response()->json("Unable to update defect due date as the case has been closed");
        }
        
    }

    public function ajaxPostDefectExtendDueDate($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
                $defect->due_date = $defect->due_date->addDays(DefectConfig::EXPIRY_DAYS);
                $defect->extended_count = $defect->extended_count + 1;
                $defect->save();
        
                $activity = $defect->activities()->create([
                    'type' => 'update',
                    'user_id' => auth()->user()->id,
                    'content' => 'Due date was extended'
                ]);
        
                if (!empty($defect->case->assigned_cow)) {
                    $defect->case->assigned_cow->notify(new DefectDueDateExtended($defect->case->project, $defect->case, $defect, auth()->user()->name));
                }
        
                if (!empty($defect->assigned_contractor)) {
                    $defect->assigned_contractor->notify(new DefectDueDateExtended($defect->case->project, $defect->case, $defect, auth()->user()->name));
                }
        
                return response()->json("Defect due date successfully extended!");
            } else {
                return response()->json("Unable to extend due date as the defect has been closed");
            }
        } else {
            return response()->json("Unable to extend due date as the case has been closed");
        }
    }

    public function ajaxPostDefectDefectType($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
                $data = request()->validate([
                    'defect_type_id' => 'required',
                ]);
        
                $defect->defect_type_id = $data['defect_type_id'];
        
                $defect->save();
        
                return response()->json("Defect's type successfully updated!");
            } else {
                return response()->json("Unable to update defect type as the defect has been closed");
            }
        } else {
            return response()->json("Unable to update defect type as the case has been closed");
        }
    }

    public function ajaxPostDefectContractor($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($id);
            if($defect->status != 'closed'){
                $data = request()->validate([
                    'assigned_contractor_user_id' => 'required',
                ]);
        
                $user = request()->user();
        
                $prev_contractor = $defect->assigned_contractor;
        
                if (!empty($prev_contractor)) {
                    if (!empty($defect->case->assigned_cow)) {
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
                return response()->json(["Defect assigned contractor successfully updated!", Defect::with(['assigned_contractor.contractor', 'type'])->where('id', $id)->first()]);
            } else {
                return response()->json("Unable to assign contractor as the defect has been closed");
            }
        } else {
            return response()->json("Unable to assign contractor as the case has been closed");
        }
    }

    public function ajaxPostDeleteDefectImage($proj_id, $case_id, $defect_id, $id)
    {
        $case = ProjectCase::find($case_id);
        if($case->status != 'closed'){
            $defect = Defect::find($defect_id);
            if($defect->status != 'closed'){
                $defectImage = DefectImage::find($id);

                $media = $defectImage->image_media;
                $defectImage->delete();
                $media->delete();
        
                return response()->json("Defect image successfully deleted!");
            } else {
                return response()->json("Unable to delete defect image as the defect has been closed");
            }
        } else {
            return response()->json("Unable to delete defect image as the case has been closed");
        }
    }

    public function ajaxPostDefectTags($proj_id, $case_id, $id)
    {
        $case = ProjectCase::find($case_id);
        $defect = Defect::find($id);

        if($case->status != 'closed'){
            if($defect->status != 'closed'){
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
            } else {
                return response()->json("Unable to update tags as the defect has been closed");
            }
        } else {
            return response()->json("Unable to update tags as the case has been closed");
        }
    }

    public function index($proj_id)
    {
        return view('dev-admin.projects.defects.index', ['proj_id' => $proj_id]);
    }

    public function getDataTableDefects(Request $request, $proj_id)
    {
        if ($request->ajax()) {
            $defects = Defect::where('project_id', $proj_id);
            return DataTables::of($defects)->addIndexColumn()
                ->addIndexColumn()
                ->addColumn('viewUrl', function ($row) {
                    return route('dev-admin.projects.cases.view', ['proj_id' => $row->project_id, 'id' => $row->case_id]);
                })
                ->make(true);
        }
    }

    public function ajaxGetDuplicateDefectInfo($proj_id, $case_id, $id) {
        $defect = Defect::where('project_id', $proj_id)
        ->where('case_id', $case_id)
        ->where('id', $id)
        ->first();

        $duplicateDefect = $defect->duplicate_defect;

        return response()->json([
            'id' => $duplicateDefect->id,
            'ref_no' => $duplicateDefect->ref_no,
            'title' => $duplicateDefect->title,
            'case_id' => $duplicateDefect->case_id,
            'case_ref_no' => $duplicateDefect->case->ref_no,
        ]);
    }

    // Defect Specific

    function ajaxPostSearchDefects(Request $request, $proj_id, $defect_id) {
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
        ->select(['defects.id', 'defects.ref_no', 'defects.title', 'project_cases.ref_no AS case_ref_no'])
        ->where('defects.id', '!=', $defect_id)->get();
    
            
        return response()->json($results);
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
            // TODO $defect->closed_status = $closedStatus;
        }

        $defect->save();

        $activity = $defect->activities()->create([
            'type' => 'defect-status',
            'user_id' => auth()->user()->id,
            'content' => 'Changed status from \'' . DefectStatus::$dict[$prev_status] . '\' to \'' . DefectStatus::$dict[$status] . '\''
        ]);

        if (!empty($defect->case->assigned_cow)) {
            $defect->case->assigned_cow->notify(new NewDefectStatus($defect->case->project, $defect->case, $defect, $prev_status, $activity));
        }

        if (!empty($defect->assigned_contractor)) {
            $defect->assigned_contractor->notify(new NewDefectStatus($defect->case->project, $defect->case, $defect, $prev_status, $activity));
        }
    }

    private function extendDefectDueDate($proj_id, $case_id, $id)
    {
        $defect = Defect::find($id);
        $defect->due_date = $defect->due_date->addDays(DefectConfig::EXPIRY_DAYS);
        $defect->extended_count = $defect->extended_count + 1;
        $defect->save();
    }
}
