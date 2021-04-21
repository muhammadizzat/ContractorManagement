<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth; 

use App\Constants\CaseStatus; 
use App\Constants\DefectStatus; 

use App\Http\Resources\Developer\DefectTypeInfoResource;

class ConstantController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
    }

    public function getCaseStatuses()
    {
        return response()->json(CaseStatus::$dict, 200);
    }

    public function getDefectStatuses()
    {
        return response()->json(DefectStatus::$dict, 200);
    }
}
