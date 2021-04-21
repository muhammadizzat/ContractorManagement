<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use ImageOptimizer;
use Carbon\Carbon;

use App\ProjectCase;
use App\Defect;
use App\DefectActivity;
use App\DefectImage;
use App\DefectActivityImage;
use App\Media;

use App\Constants\DefectStatus;
use App\Notifications\ContractorDefectRequest;
use App\Notifications\NewDefectStatus;

use Maatwebsite\Excel\Facades\Excel;
use App\Excel\Exports\DefectReportExport;

class DefectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:contractor']);
    }

    public function ajaxGetDefects(Request $request)
    {
        $data = $request->validate([
            'project_id' => '',
        ]);

        $defectsQuery = Defect::with(['type', 'case'])
        ->unclosed()
        ->where('assigned_contractor_user_id', auth()->user()->id);
        
        if(!empty($data['project_id'])) {
            $defectsQuery->where('project_id', $data['project_id']);
        }

        $defects = $defectsQuery->get();

        return $defects;
    }

    public function ajaxGetDefectInfo($id)
    {
        $defect = Defect::with(['type', 'assigned_contractor', 'images', 'case.unit', 'pins', 'tags'])
            ->where('id', $id)
            ->where('assigned_contractor_user_id', auth()->user()->id)
            ->first();
        return $defect;
    }

    public function getDefectImage($defect_id, $id) {
        $defectImage = DefectImage::find($id);

        $imageMedia = $defectImage->image_media;

        return response()->make($imageMedia->data, 200, [
            'Content-Type' => $imageMedia->mimetype,
            'Content-Disposition' => 'inline; filename="'.$imageMedia->filename.'"'
        ]);
    }

    public function ajaxGetDefectActivities($id)
    {
        $defect = Defect::with(['activities.user.roles', 'activities.images', 'activities.request_response_user.roles'])
            ->where('id', $id)
            ->where('assigned_contractor_user_id', auth()->user()->id)
            ->first();
        return $defect->activities;
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

    public function ajaxGetDefectActivityImage($defect_id, $activity_id, $id) {
        $defectActivityImage = DefectActivityImage::find($id);

        $imageMedia = $defectActivityImage->image_media;
     
        return response()->make($imageMedia->data, 200, [
            'Content-Type' => $imageMedia->mimetype,
            'Content-Disposition' => 'inline; filename="'.$imageMedia->filename.'"'
        ]);
    }

    public function ajaxPostAddActivityComment($id)
    {

        $data = request()->validate([
            'comment' => 'required',
        ]);

        $imagesData = request()->validate([
            'images' => '',
        ]);

        $defect = Defect::where('id', $id)
            ->where('assigned_contractor_user_id', auth()->user()->id)
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

        return response()->json($activity);
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

    public function ajaxPostDefectStatus($id)
    {
        $defect = Defect::find($id);
        $prev_status = $defect->status;
        
        $data;
        if ($prev_status == 'open') {
            $data = request()->validate(['status' => 'required|in:'.DefectStatus::WIP]);
        } else if ($prev_status == 'wip') {
            $data = request()->validate(['status' => 'required|in:'.DefectStatus::RESOLVED]);
        } else {
            return response()->json([
                "message" => "Failed to change the defect status.",
                "errors" => [
                    "status" => [
                        "The selected status is invalid."
                    ]
                ]
            ], 422);
        }

        $defect = Defect::find($id);

        $prev_status = $defect->status;
        $defect->status = $data['status'];

        if ($data['status'] == 'resolved') {
            $defect->resolved_date = Carbon::now();
        }

        $defect->save();

        $activity = $defect->activities()->create([
            'type' => 'update',
            'user_id' => auth()->user()->id,
            'content' => 'Changed status from \'' . DefectStatus::$dict[$prev_status] . '\' to \'' . DefectStatus::$dict[$data['status']] . '\''
        ]);

        if(!empty($defect->case->assigned_cow)) {
            $defect->case->assigned_cow->notify(new NewDefectStatus($defect->case->project, $defect->case,  $defect, $prev_status, $activity));
        }

        if(!empty($defect->assigned_contractor) && ($defect->case->assigned_cow->id != auth()->user()->id)) {
            $defect->case->assigned_cow->notify(new NewDefectStatus($defect->case->project, $defect->case,  $defect, $prev_status, $activity));
        }

        return response()->json("Defect status successfully updated!");
    }

    public function ajaxPostDefectRequest($id)
    {
        // Validation
        $data = request()->validate([
            'type' => 'required|in:close,extend,reject',
            'reason' => 'required',
        ]);

        $defect = Defect::find($id);
        // Check if the defect is closed - closed cant have request
        if($defect == DefectStatus::CLOSED) {
            return response()->json(null, 422);
        }

        // Check if there is already a pending request for this defect - can only have one - return error
        $pendingRequestsCount = $defect->activities()
            ->where('type', 'request')
            ->whereNull('request_response')
            ->count();
        if($pendingRequestsCount > 0) {
            $errorMessage = [
                'message' => "Please approve or close existing status request before requesting for another one",
            ];
            return $errorMessage;
        }

        // Create request activity
        $defect->activities()->create([
            'user_id' => auth()->user()->id,
            'type' => 'request',
            'request_type' => $data['type'],
            'content' => $data['reason'],
        ]);

        if (!empty($defect->case->assigned_cow)) {
            $defect->case->assigned_cow->notify(new ContractorDefectRequest($defect->case->project, $defect->case, $defect, auth()->user()->name));
        }

        return response()->json("Defect status successfully updated!");
    }

    // SECTION: Activity
    public function ajaxPostDefectRequestCancel($defect_id, $activity_id)
    {
        $defect = Defect::find($defect_id);

        $defectActivity = $defect->activities()->where('id', $activity_id)->first();

        if($defectActivity->type != "request" || !empty($defectActivity->request_response)) {
            return response()->json(null, 400);
        }

        $defectActivity->request_response = "cancelled";
        $defectActivity->request_response_user_id = auth()->user()->id;

        $defectActivity->save();

        return response()->json("Defect request successfully updated!");
    }
}
