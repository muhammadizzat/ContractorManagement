<?php

namespace App\Http\Controllers\Contractor;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

use Carbon\Carbon;


use App\UnitTypeFloor;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

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
