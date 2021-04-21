<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        
        View::composer('admin.developers.developer-sidebar', 'App\Http\View\Composers\DeveloperComposer');
        View::composer('dev-admin.project-sidebar', 'App\Http\View\Composers\ProjectComposer');
        View::composer('dev-cow.project-sidebar', 'App\Http\View\Composers\ProjectComposer');
    }
}