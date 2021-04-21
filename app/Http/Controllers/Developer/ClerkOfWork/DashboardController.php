<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dev-cow.dashboard');
    }
}
