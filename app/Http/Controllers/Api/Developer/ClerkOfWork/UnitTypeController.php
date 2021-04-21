<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\User;
use App\UnitType;
use App\UnitTypeFloor;

use App\Http\Resources\Developer\UnitTypeResource;

class UnitTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }

    public function getUnitType($proj_id, $unit_type_id)
    {
        $devId = Auth::user()->clerk_of_work->developer_id;

        $unit_type = UnitType::with(['floors'])->where('project_id', $proj_id)->where('id', $unit_type_id)->first();

        return response()->json(new UnitTypeResource($unit_type), 200);
    }

    public function getUnitTypeFloorPlan($proj_id, $unit_type_id, $floor_id)
    {
        $unit_type_floor = UnitTypeFloor::with(['floor_plan_media'])
        ->where('unit_type_id', $unit_type_id)
        ->where('id', $floor_id)
        ->first();

        if(empty($unit_type_floor->floor_plan_media)) {
            return response(null, 404);
        }

        $imageMedia = $unit_type_floor->floor_plan_media;
        $dataUrl = 'data:'.$imageMedia->mimetype.';base64,'.base64_encode($imageMedia->data);
        return response()->json($dataUrl, 200);
    }
}
