<?php

namespace App\Http\Controllers\Contractor;

use App\Unit;

use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:contractor']);
    }

    
    public function ajaxGetUnitUnitType($proj_id, $unit_id)
    {
        $unit = Unit::with(['unit_type.floors'])
            ->where('project_id', $proj_id)
            ->where('id', $unit_id)
            ->first();
        return $unit->unit_type;
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

        return response()->make($imageMedia->data, 200, [
            'Content-Type' => $imageMedia->mimetype,
            'Content-Disposition' => 'inline; filename="'.$imageMedia->filename.'"'
        ]);
    }
}
