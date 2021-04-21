<?php

namespace App\Http\Controllers\Api\Contractor;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Unit;

use App\Http\Resources\Developer\UnitTypeResource;

class UnitController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:contractor']);
    }

    public function getUnitUnitType($proj_id, $unit_id)
    {
        $unit = Unit::with(['unit_type.floors'])
            ->where('project_id', $proj_id)
            ->where('id', $unit_id)
            ->first();
        return response()->json(new UnitTypeResource($unit->unit_type));
    }

    public function getUnitFloorPlan($proj_id, $unit_id, $floor_id) {
        $unit = Unit::where('id', $unit_id)->where('project_id', $proj_id)->first();

        if(empty($unit)) {
            return response(null, 404);
        }

        $floor = $unit->unit_type->floors()->where('id', $floor_id)->first();

        $imageMedia = $floor->floor_plan_media;

        if(empty($imageMedia)) {
            return response(null, 404);
        }

        $dataUrl = 'data:'.$imageMedia->mimetype.';base64,'.base64_encode($imageMedia->data);
        return response()->json($dataUrl, 200);
    }
}
