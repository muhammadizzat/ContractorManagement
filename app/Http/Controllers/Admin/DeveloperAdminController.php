<?php

namespace App\Http\Controllers\Admin;

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
        $this->middleware(['role:super-admin|admin']);
    }

    public function getDataTableDevelopersAdmins()
    {
        $admins = DeveloperAdmin::with('user');
        return DataTables::of($admins)
            ->addIndexColumn()
            ->addColumn('editUrl', function ($row) {
                return route('admin.developer-admins.edit', ['id' => $row->user_id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('admin.developer-admins.delete', ['id' => $row->user_id]);
            })
            ->make(true);
    }

    public function getDataTableDeveloperAdmin($dev_id)
    {
        $admins = DeveloperAdmin::with('user')->where('developer_id', $dev_id);
        return DataTables::of($admins)
            ->addIndexColumn()
            ->addColumn('editUrl', function ($row) {
                return route('admin.developers.admins.edit', ['dev_id' => $row->developer_id, 'id' => $row->user_id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('admin.developers.admins.delete', ['dev_id' => $row->developer_id, 'id' => $row->user_id]);
            })
            ->make(true);
    }

    public function getDevelopersAdminsExcelExport()
    {
        $role = 'dev-admin';
        return Excel::download(new AdminsExport($role, null), 'Developer Admins.xlsx');
    }

    public function getDeveloperAdminsExcelExport($dev_id)
    {
        $role = 'dev-admin';
        return Excel::download(new AdminsExport($role, $dev_id), 'Developer Admins.xlsx');
    }

    public function displayDevelopersAdmins()
    {
        return view('admin.developer-admins.index');
    }

    public function displayDeveloperAdmins($dev_id)
    {
        return view('admin.developers.admins.index', ['dev_id' => $dev_id]);
    }

    public function addDeveloperAdmin($dev_id)
    {
        return view('admin.developers.admins.add', ['dev_id' => $dev_id]);
    }

    public function postAddDeveloperAdmin(Request $request, $dev_id)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL|email',
        ]);

        $primary_admin = $request->primary_admin;
        $pw = str_random(8);

        if ($primary_admin == null) {
            $primary_admin = 0;
        } else {
            $primary_admin = 1;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($pw),
        ]);
        $user->assignRole('dev-admin');

        $developerAdmin = DeveloperAdmin::create([
            'user_id' => $user->id,
            'developer_id' => $dev_id,
            'created_by' => Auth::user()->id,
            'primary_admin' => $primary_admin,
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

        return redirect()->route('admin.developers.admins.index', ['dev_id' => $dev_id])->withStatus(__($returnMessage ?? 'Developer Admin successfully created.'));
    }

    public function editDevelopersAdmin(Request $request, User $admin)
    {
        $id = $request->id;
        $admin = User::find($id);
        $user_id = $admin->id;

        $developer_admin = DeveloperAdmin::where('user_id', $id)->first();

        return view('admin.developer-admins.edit', ['admin' => $admin, 'developer_admin' => $developer_admin, 'id' => $user_id]);
    }

    public function editDeveloperAdmin(Request $request, User $admin)
    {
        $id = $request->id;
        $admin = User::find($id);
        $user_id = $admin->id;

        $developer_admin = DeveloperAdmin::where('user_id', $id)->first();
        $dev_id = $developer_admin->developer_id;

        return view('admin.developers.admins.edit', ['admin' => $admin, 'dev_id' => $dev_id, 'developer_admin' => $developer_admin, 'id' => $user_id]);
    }

    public function postEditDevelopersAdmin(Request $request, User $admin)
    {
        $id = $request->id;
        $primary_admin = $request->primary_admin;

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

        $response = self::updateDeveloperAdmin($id, $primary_admin, $request['name'], $is_disabled);

        return redirect()->route('admin.developer-admins.index')->withStatus(__('Developer Admin successfully updated.'));
    }

    public function postEditDeveloperAdmin(Request $request, User $admin)
    {

        $dev_id = $request->dev_id;
        $id = $request->id;
        $primary_admin = $request->primary_admin;

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

        $response = self::updateDeveloperAdmin($id, $primary_admin, $request['name'], $is_disabled);
        return redirect()->route('admin.developers.admins.index', ['dev_id' => $dev_id])->withStatus(__('Developer Admin successfully updated.'));
    }

    public function deleteDevelopersAdmin(Request $request, $id)
    {
        $assign_dev_admin = User::with('projects_dev_admins')->withCount('projects_dev_admins')->where('id', $id)->get();

        $assign_dev_admin_count = $assign_dev_admin->first->projects_dev_admins_count;
        $defects_record = DefectActivity::where('user_id', $id)->count();

        $user = User::find($id);
        $primary_admin = $user->developer_admin->primary_admin;

        if ($primary_admin == 0) {

            if ($assign_dev_admin_count == null) {
                if ($defects_record == 0) {
                    DB::transaction(function () use ($id) {
                        DeveloperAdmin::where('user_id', $id)->delete();
                        User::where('id', $id)->delete();
                    });

                    return redirect()->route('admin.developer-admins.index')->with('status', 'Developer Admin is successfully deleted.');
                } else {
                    return redirect()->route('admin.developer-admins.index')->with('error', 'Unable to delete a developer admin assigned to defect(s).');
                }
            } else {
                return redirect()->route('admin.developer-admins.index')->with('error', 'Unable to delete a developer admin assigned to project(s).');
            }
        } else {
            return redirect()->route('admin.developer-admins.index')->with('error', 'You are not allowed to delete a manager.');
        }
    }

    public function deleteDeveloperAdmin(Request $request, $dev_id, $id)
    {
        $assign_dev_admin = User::with('projects_dev_admins')->withCount('projects_dev_admins')->where('id', $id)->get();

        $assign_dev_admin_count = $assign_dev_admin->first->projects_dev_admins_count;
        $defects_record = DefectActivity::where('user_id', $id)->count();

        $user = User::find($id);
        $primary_admin = $user->developer_admin->primary_admin;

        if ($primary_admin == 0) {

            if ($assign_dev_admin_count == null) {
                if ($defects_record == 0) {
                    DB::transaction(function () use ($dev_id, $id) {
                        DeveloperAdmin::where('developer_id', $dev_id)->where('user_id', $id)->delete();
                        User::where('id', $id)->delete();
                    });

                    return redirect()->route('admin.developers.admins.index', ['dev_id' => $dev_id])->with('status', 'Developer Admin is successfully deleted.');
                } else {
                    return redirect()->route('admin.developers.admins.index', ['dev_id' => $dev_id])->with('error', 'Unable to delete a developer admin assigned to defect(s).');
                }
            } else {
                return redirect()->route('admin.developers.admins.index', ['dev_id' => $dev_id])->with('error', 'Unable to delete a developer admin assigned to project(s).');
            }
        } else {
            return redirect()->route('admin.developers.admins.index', ['dev_id' => $dev_id])->with('error', 'You are not allowed to delete a manager.');
        }
    }

    private static function updateDeveloperAdmin($id, $primary_admin, $name, $is_disabled)
    {
        if ($primary_admin == null) {
            $primary_admin = 0;
        } else {
            $primary_admin = 1;
        }

        $data['primary_admin'] = $primary_admin;
        DeveloperAdmin::where('user_id', $id)->update(array('primary_admin' => $data['primary_admin']));
        User::find($id)->update(['name' => $name, 'is_disabled' => $is_disabled]);

    }
}
