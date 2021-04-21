<?php

namespace App\Http\Controllers\Api\Contractor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth; 

use App\Constants\DefectStatus; 

class ConstantController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:contractor']);
    }

    public function getDefectStatuses()
    {
        return response()->json(DefectStatus::$dict, 200);
    }
}
