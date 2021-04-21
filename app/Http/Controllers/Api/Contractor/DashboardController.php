<?php

namespace App\Http\Controllers\Api\Contractor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth; 
use App\Defect;


class DashboardController extends Controller
{    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:contractor']);
    }
    
    public function getStats() 
    {
        $defects = Defect::where('assigned_contractor_user_id', auth()->user()->id);

        $pendingDefectsCount = $defects->unclosed()->count();
        $overdueDefectsCount = $defects->unclosed()->where('due_date', '<', Carbon::today())->count();
        $completedDefectsCount = $defects->where('status', 'closed')->count();

        return response()->json([
                'pending_defects' => $pendingDefectsCount, 
                'overdue_defects' => $overdueDefectsCount, 
                'completed_defects' => $completedDefectsCount
            ], 200);
    }
}