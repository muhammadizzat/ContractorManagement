<?php

namespace App\Http\Controllers\Developer\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dev-admin.dashboard');
    }
}
