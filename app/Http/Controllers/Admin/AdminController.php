<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\VerifyUser;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Validator;

use App\Services\UserManager;

use Maatwebsite\Excel\Facades\Excel;
use App\Excel\Exports\AdminsExport;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function displayAdmins()
    {
        return view('admin.admins.index');
    }

    public function getDataTableAdmin(Request $request) //admin list
    {
        if ($request->ajax()) {
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            });
            return DataTables::of($admins)
                ->addIndexColumn()
                ->addColumn('editUrl', function ($row) {
                    return route('admin.admins.edit', ['id' => $row->id]);
                })
                ->addColumn('deleteUrl', function ($row) {
                    return route('admin.admins.delete', ['id' => $row->id]);
                })
                ->make(true);
        }
    }

    public function getAdminsExcelExport()
    {
        $role = 'admin';
        $dev_id = 0;
        return Excel::download(new AdminsExport($role, $dev_id), 'LinkZZapp Admins.xlsx');
    }

    public function addAdmin(User $users)
    {
        return view('admin.admins.add', ['users' => $users->all()]);
    }

    public function postAddAdmin(Request $request, User $model)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL|email',
        ]);

        $returnMessage;
        try {
            $success = UserManager::createAdminUser($data['name'], $data['email']);
        } catch (Exception $e) {
            $returnMessage = $e->getMessage();
        }

        return redirect()->route('admin.admins.index')->withStatus(__($returnMessage ??'Linkzzapp Admin successfully created.'));
    }

    public function editAdmin(Request $request, User $admin)
    {
        $id = $request->id;
        $admin = User::find($id);
        return view('admin.admins.edit', ['admin' => $admin]);
    }

    public function postEditAdmin(Request $request)
    {
        $id = $request->id;
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

        User::find($id)->update(['name' => $request['name'], 'is_disabled' => $is_disabled]);

        return redirect()->route('admin.admins.index')->withStatus(__('Linkzzapp Admin is successfully updated.'));
    }

    public function deleteAdmin(Request $request)
    {
        $current_user_id = auth()->user()->id;
        $id = $request->id;

        if ($current_user_id != $id) {

            User::where('id', $id)->delete();
            return redirect()->route('admin.admins.index')->withStatus(__('Linkzzapp Admin is successfully deleted.'));
        } else {

            return redirect()->route('admin.admins.index')->with('error','You cannot delete your own account.');
        }
    }

    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (isset($verifyUser)) {
            $user = $verifyUser->user;
            if (!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $user->change_password = 0;
                $user->save();
                $status = "Your e-mail is verified. Please change your password.";
                return redirect('/first-time-login')->with('status', $status);
            } else {
                $status = "Your e-mail is already verified. You can now login.";
            }
        } else {
            return redirect('/login')->with('warning',"Sorry your email cannot be identified.");
        }
    }

    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();
        return redirect('/login')->with('status','We sent you an activation code. Check your email and click on the link to verify.');
    }
}
