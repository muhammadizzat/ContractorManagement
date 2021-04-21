<?php

namespace App\Http\Controllers\Developer\ClerkOfWork;

use App\ClerkOfWork;
use App\Excel\Exports\ClerkOfWorksExport;
use App\Http\Controllers\Controller;
use App\ProjectCase;
use App\Services\UserManager;
use App\User;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Yajra\Datatables\Datatables;

class ClerkOfWorkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:cow']);
    }

    public function index()
    {
        return view('dev-cow.clerks-of-work.index');
    }

    public function getDataTableClerksOfWork(Request $request)
    {
        $id = auth()->user()->id;
        $dev_id = ClerkOfWork::where('user_id', $id)->first()->developer_id;

        $clerkOfWorks = ClerkOfWork::with('user')->where('developer_id', $dev_id);
        return DataTables::of($clerkOfWorks)
            ->addIndexColumn()
            ->addColumn('editUrl', function ($row) {
                return route('dev-cow.clerks-of-work.edit', ['user_id' => $row->user_id]);
            })
            ->addColumn('deleteUrl', function ($row) {
                return route('dev-cow.clerks-of-work.delete', ['user_id' => $row->user_id]);
            })
            ->make(true);
    }

    public function getClerkOfWorksExcelExport()
    {
        $dev_id = request()->user()->clerk_of_work->developer_id;
        return Excel::download(new ClerkOfWorksExport($dev_id), 'Clerk of Works.xlsx');
    }

    public function addClerkOfWork()
    {
        return view('dev-cow.clerks-of-work.add');
    }

    public function postAddClerkOfWork(Request $request)
    {
        $id = auth()->user()->id;
        $dev_id = ClerkOfWork::where('user_id', $id)->first()->developer_id;

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL|email',
        ]);

        $returnMessage;
        try {
            $success = UserManager::createClerkOfWorkUser($data['name'], $data['email'], $dev_id, 'cow');
        } catch (Exception $e) {
            $returnMessage = $e->getMessage();
        }

        return redirect()->route('dev-cow.clerks-of-work.index')->withStatus(__($returnMessage ?? 'Clerk Of Work is successfully created.'));
    }

    public function editClerkOfWork(Request $request, User $clerkOfWorks)
    {
        $id = $request->id;
        $clerkOfWorks = User::find($id);
        return view('dev-cow.clerks-of-work.edit', ['clerkOfWorks' => $clerkOfWorks]);
    }

    public function postEditClerkOfWork(Request $request, User $clerkOfWorks)
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

        return redirect()->route('dev-cow.clerks-of-work.index')->withStatus(__('Clerk of work is successfully updated.'));
    }

    public function deleteClerkOfWork(Request $request)
    {
        $current_user_id = auth()->user()->id;
        $id = $request->id;

        $total_case = ProjectCase::where('assigned_cow_user_id', $id)->count();

        if ($total_case == 0) {

            if ($current_user_id != $id) {
                DB::transaction(function () use ($id) {
                    ClerkOfWork::where('user_id', $id)->delete();
                    User::where('id', $id)->delete();
                });

                return redirect()->route('dev-cow.clerks-of-work.index')->withStatus(__('Clerk of work is successfully deleted.'));
            } else {
                return redirect()->route('dev-cow.clerks-of-work.index')->with('error', 'You cannot delete your own account.');
            }
        } else {

            return redirect()->route('dev-cow.clerks-of-work.index')->with('error', 'Clerk of Work cannot be deleted if he/she is assigned to any cases.');
        }
    }
}
