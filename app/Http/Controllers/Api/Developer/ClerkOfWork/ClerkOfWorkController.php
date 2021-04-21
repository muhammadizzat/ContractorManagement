<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth; 

use App\User; 

use App\Http\Resources\Developer\UserResource;

class ClerkOfWorkController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
    }

    public function getClerkOfWorks() {
        $devId = Auth::user()->clerk_of_work->developer_id; 

        $users = User::whereHas('clerk_of_work', function($q) use ($devId) {
            $q->where('developer_id', $devId);
        })->get();

        return response()->json(UserResource::collection($users), 200);
    }
}
