<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function index()
    {
        return view('admin.audit-log');
    }
}
