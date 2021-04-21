<?php

namespace App\Http\Middleware;

use Closure;
use App\Developer;
use App\DeveloperAdmin;
use Illuminate\Support\Facades\Auth; 

class DeveloperDevAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id = auth()->user()->id;
        $developer_id = DeveloperAdmin::where('user_id',$user_id)->first();
        
        if (!empty($developer_id)) {
            $developer_id = Developer::find($developer_id);
            if (!empty($developer_id)) {
                return $next($request);
            }
            else
            {

                abort(403, 'Unauthorized action.');
            }
        }
    }
}
