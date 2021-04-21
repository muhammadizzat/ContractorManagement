<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth; 

use App\User; 
use App\Project; 

use App\Http\Resources\Developer\ProjectResource;
use App\Http\Resources\Developer\UserResource;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }

    public function get() 
    { 
        $user = auth()->user();
        $developer_id = $user->clerk_of_work->developer_id;

        $projects = Project::where('developer_id',$developer_id)
            ->whereHas('dev_cow_users', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->get();
     
        return response()->json(ProjectResource::collection($projects), 200); 
    }

    public function getProjectClerkOfWorkUsers($proj_id) {
        $cowUsers = Project::find($proj_id)->dev_cow_users()->get();

        return response()->json(UserResource::collection($cowUsers));
    }
}
