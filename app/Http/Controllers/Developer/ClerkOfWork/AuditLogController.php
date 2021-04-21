<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-cow']);
    }

    public function index()
    {
        return view('dev-cow.audit-log');
    }

}
