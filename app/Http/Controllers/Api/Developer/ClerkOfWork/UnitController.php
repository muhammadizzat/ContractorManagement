<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth; 

use App\User; 
use App\Project; 

use App\Http\Resources\Developer\ProjectResource;
use App\Http\Resources\Developer\UnitInfoResource;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }

    public function getUnits() 
    { 
        $devId = Auth::user()->clerk_of_work->developer_id; 

        $project = Project::where('developer_id', $devId)->first();
     
        return response()->json(UnitInfoResource::collection($project->units), 200); 
    } 
}
