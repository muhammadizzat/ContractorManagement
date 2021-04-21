<?php

namespace App\Http\Controllers\Admin;

use App\Contractor;
use App\Http\Controllers\Controller;
use App\User;
use Validator;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function displayContractor()
    {
        User::whereHas('roles', function ($query) {
            $query->where('name', 'contractor');
        });

        return view('admin.contractors.index');
    }

    public function getDataTableContractor(Request $request)
    {
        if ($request->ajax()) {
            $contractors = User::whereHas('roles', function ($query) {
                $query->where('name', 'contractor');
            })->with('contractor');
            return DataTables::of($contractors)
                ->addIndexColumn()
                ->addColumn('editUrl', function ($row) {
                    return route('admin.contractors.edit', ['id' => $row->id]);
                })
                ->addColumn('deleteUrl', function ($row) {
                    return route('admin.contractors.delete', ['id' => $row->id]);
                })
                ->make(true);
        }
    }

    public function editContractor(Request $request, User $contractor)
    {
        $id = $request->id;
        $user = User::find($id);
        $contractor = Contractor::where('user_id', $id)->first();
        return view('admin.contractors.edit', ['user' => $user, 'contractor' => $contractor]);
    }

    public function postEditContractor(Request $request)
    {
        $id = $request->id;
        $data = $request->validate([
            'name' => 'required',
            'address_1' => 'required',
            'address_2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postal_code' => 'required|numeric|digits_between:1,10',
            'contact_no' => 'required',
        ]);

        if ($request['is_disabled']) {
            $is_disabled = 1;
        } else {
            $is_disabled = 0;
        }

        User::find($id)->update([
            'name' => $data['name'],
            'is_disabled' => $is_disabled,
        ]);

        Contractor::where('user_id', $id)->update([
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'],
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postal_code'],
            'contact_no' => $data['contact_no'],
        ]);

        return redirect()->route('admin.contractors.index')->withStatus(__('Contractor is successfully updated.'));
    }

    public function deleteContractor(Request $request)
    {
        $id = $request->id;

        User::where('id', $id)->delete();
        return redirect()->route('admin.contractors.index')->withStatus(__('Contractor is successfully deleted.'));
    }
}
