<?php

namespace App\Http\View\Composers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\UserRepository;

use App\Project;

class ProjectComposer
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $project = null;
        
        $proj_id = $this->request->proj_id;
        if(!empty($proj_id)) {
            $project = Project::find($proj_id);
        }

        $view->with('project', $project);
    }
}