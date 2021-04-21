<?php

namespace App\Http\Middleware;

use App\ClerkOfWork;
use App\Project;
use Closure;

class ProjectDevCowAccess
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
        if (!empty($request->proj_id)) {

            $user = auth()->user();

            $project = Project::where('id',$request->proj_id)
                ->where('developer_id', $user->clerk_of_work->developer_id)
                ->first();

            if(!empty($project)) {
                $projectHasCowUser = $project->dev_cow_users()->where('users.id', $user->id)->count();
                if($projectHasCowUser > 0) {
                    return $next($request);
                }
            }
        } else {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
