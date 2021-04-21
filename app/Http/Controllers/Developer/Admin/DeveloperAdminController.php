<?php

namespace App\Http\Controllers\Developer\Admin;

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
        $this->middleware(['role:dev-admin']);
    }

    public function getDataTableDeveloperAdmins(Request $request)
    {
        $developer_id = auth()->user()->developer_admin->developer_id;

        $developer_admins = DeveloperAdmin::with('user')->where('developer_id', $developer_id)->get();

        return DataTables::of($developer_admins)
            ->addIndexColumn()
            ->addColumn('editUrl', function ($row) {
                return route('dev-admin.developer-admins.edit', ['id' => $row->user_id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('dev-admin.developer-admins.delete', ['id' => $row->user_id]);
            })
            ->make(true);
    }

    public function getDeveloperAdminsExcelExport()
    {
        $role = 'dev-admin';
        $dev_id = request()->user()->developer_admin->developer_id;
        return Excel::download(new AdminsExport($role, $dev_id), 'Developer Admins.xlsx');
    }

    public function displayDeveloperAdmin()
    {
        return view('dev-admin.developer-admins.index');
    }

    public function addDeveloperAdmin()
    {
        return view('dev-admin.developer-admins.add');
    }

    public function postAddDeveloperAdmin(Request $request)
    {
        $id = auth()->user()->id;
        $dev_id = DeveloperAdmin::where('user_id', $id)->first();

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL|email',
        ]);

        $pw = str_random(8);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($pw),
        ]);
        $user->assignRole('dev-admin');

        $developerAdmin = DeveloperAdmin::create([
            'user_id' => $user->id,
            'developer_id' => $dev_id->developer_id,
            'created_by' => Auth::user()->id,
            'primary_admin' => 0,
        ]);

        // User verification
        $verifyUser = VerifyUser::create([
            'user_id' => $user->id,
            'token' => str_random(40),
        ]);

        // Send email
        $token = app('auth.password.broker')->createToken($user);
        Mail::send('emails.welcome', ['user' => $user, 'token' => $token, 'pw' => $pw], function ($m) use ($user) {
            $m->from('hello@appsite.com', 'LinkZZapp');
            $m->to($user->email)->subject('Welcome to LinkZZapp');
        });

        return redirect()->route('dev-admin.developer-admins.index')->withStatus(__($returnMessage ?? 'Developer Admin is successfully created.'));
    }

    public function editDeveloperAdmin(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $developer_admin = DeveloperAdmin::where('user_id', $id)->first();

        return view('dev-admin.developer-admins.edit', ['user' => $user, 'developer_admin' => $developer_admin]);
    }

    public function postEditDeveloperAdmin(Request $request)
    {
        // Edited User Details
        $user = User::find($request['id']);
        $primary_admin = $user->developer_admin->primary_admin;

        // Check if current user is Manager
        $current_user_id = auth()->user()->id;
        $current_user = User::find($current_user_id);
        $is_manager = $current_user->developer_admin->primary_admin;

        if ($current_user_id == $user->id) {
            return redirect()->route('dev-admin.developer-admins.index')->with('error', 'Edit failed. Please update your profile in the profile settings.');
        } else {
            // If the edited user is not a manager, AND if current user is a manager
            if ($primary_admin == 0 && $is_manager == 1) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ]);

                if ($validator->fails()) {
                    return back()->with('error', 'Please insert name');
                }

                if ($request['is_disabled']) {
                    $is_disabled = 1;
                } else {
                    $is_disabled = 0;
                }

                User::find($user->id)->update(['name' => $request['name'], 'is_disabled' => $is_disabled]);

                return redirect()->route('dev-admin.developer-admins.index')->with('status', 'Developer Admin is successfully updated.');
            } else {
                return redirect()->route('dev-admin.developer-admins.index')->with('error', 'You are not a manager or the user you are editing is a manager.');
            }
        }
    }

    public function deleteDeveloperAdmin(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $current_user_id = auth()->user()->id;
        $assign_dev_admin = User::with('projects_dev_admins')->withCount('projects_dev_admins')->where('id', $id)->get();
        $assign_dev_admin_count = $assign_dev_admin->first->projects_dev_admins_count;
        $defects_record = DefectActivity::where('user_id', $id)->count();

        $primary_admin = $user->developer_admin->primary_admin;

        if ($current_user_id != $id) {
            if ($primary_admin == 0) {
                if ($assign_dev_admin_count == null) {
                    if ($defects_record == 0) {
                        DB::transaction(function () use ($id) {
                            DeveloperAdmin::where('user_id', $id)->delete();
                            User::where('id', $id)->delete();
                        });

                        return redirect()->route('dev-admin.developer-admins.index')->with('status', 'Developer Admin is successfully deleted.');
                    } else {

                        return redirect()->route('dev-admin.developer-admins.index')->with('error', 'Unable to delete a developer admin assigned to defect(s).');
                    }
                } else {

                    return redirect()->route('dev-admin.developer-admins.index')->with('error', 'Unable to delete a developer admin assigned to project(s).');
                }
            } else {
                return redirect()->route('dev-admin.developer-admins.index')->with('error', 'You are not allowed to delete a manager.');
            }
        } else {

            return redirect()->route('dev-admin.developer-admins.index')->with('error', 'You are not allowed to delete your own account.');
        }
    }
}
