<?php

namespace App\Http\Controllers\Developer\Admin;

use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
    }

    public function index()
    {
        return view('dev-admin.audit-log');
    }

}
