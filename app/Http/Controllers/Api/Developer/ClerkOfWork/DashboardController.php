<?php

namespace App\Http\Controllers\Api\Developer\ClerkOfWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth; 


use App\Defect;
use App\ProjectCase;


class DashboardController extends Controller
{    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
        $this->middleware('project.dev-cow.access');
    }
    
    public function getStats() 
    {
        $user_id = auth()->user()->id;

        $cases = ProjectCase::where('assigned_cow_user_id', $user_id);
        $defects = Defect::whereHas('case', function ($q) use ($user_id) {
            $q->where('assigned_cow_user_id', $user_id);
        });


        $assignedCasesCount = (clone $cases)->count();
        $openCasesCount = (clone $cases)->open()->count();
        $openDefectsCount = $defects->unclosed()->count();

        return response()->json([
                'assigned_cases' => $assignedCasesCount, 
                'open_cases' => $openCasesCount, 
                'open_defects' => $openDefectsCount
            ], 200);
    }
}