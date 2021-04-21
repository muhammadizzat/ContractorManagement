<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\DefectActivity;
use App\DeveloperAdmin;
use App\Excel\Exports\AdminsExport;
use App\Http\Controllers\Controller;
use App\User;
use App\VerifyUser;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Yajra\Datatables\Datatables;

class DeveloperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
    }

    public function getDataTableDeveloperAdmins(Request $request)
    {
        $developer_id = auth()->user()->clerk_of_work->developer_id;

        $developer_admins = DeveloperAdmin::with('user')->where('developer_id', $developer_id)->get();

        return DataTables::of($developer_admins)
            ->addIndexColumn()
            ->make(true);
    }

    public function getDeveloperAdminsExcelExport()
    {
        $role = 'dev-admin';
        $dev_id = request()->user()->clerk_of_work->developer_id;
        return Excel::download(new AdminsExport($role, $dev_id), 'Developer Admins.xlsx');
    }

    public function displayDeveloperAdmin()
    {
        return view('dev-cow.developer-admins.index');
    }
}
