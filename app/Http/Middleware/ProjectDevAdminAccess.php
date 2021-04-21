<?php

namespace App\Http\Middleware;

use Closure;
use App\Project;
use App\DeveloperAdmin;

class ProjectDevAdminAccess
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
                ->where('developer_id', $user->developer_admin->developer_id)
                ->first();

            if(!empty($project)) {
                if($user->developer_admin->primary_admin) {
                    return $next($request);
                }

                $projectHasDevAdminUser = $project->dev_admin_users()->where('users.id', $user->id)->count();
                if($projectHasDevAdminUser > 0) {
                    return $next($request);
                }
            }
        } else {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
