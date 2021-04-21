<?php

namespace App\Http\View\Composers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\UserRepository;

use App\Developer;

class DeveloperComposer
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
        $developer = null;

        $dev_id = $this->request->dev_id;
        if(!empty($dev_id)) {
            $developer = Developer::find($dev_id);
        }

        $view->with('developer', $developer);
    }
}