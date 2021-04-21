<?php

namespace App\Http\Controllers\Api\Contractor;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth; 

use App\UnitTypeFloor;

use App\Http\Resources\Developer\DefectInfoResource;

class UnitTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:contractor']);
    }

    public function getUnitTypeFloorPlanImage($proj_id, $unit_type_id, $id) {
        $floor = UnitTypeFloor::find($id);

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
