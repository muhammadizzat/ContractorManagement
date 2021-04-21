<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth; 

use App\DefectType; 

use App\Http\Resources\Developer\DefectTypeInfoResource;

class DefectTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
    }

    public function getDefectTypes()
    {
        $devId = Auth::user()->clerk_of_work->developer_id; 

        $defectTypes = $defecttypes = DefectType::forDeveloper($devId)->get();

        return response()->json(DefectTypeInfoResource::collection($defectTypes), 200);
    }
}
