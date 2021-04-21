<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use Validator;
use Yajra\Datatables\Datatables;

use App\User;
use App\Media;
use App\VerifyUser;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:super-admin|admin']);
    }

    public function displayUsers()
    {
        return view('admin.users.index');
    }

    public function getDataTableUsers(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('profile_pic_media')->whereHas('roles', function ($query) {
                                $query->whereIn('name', ['admin', 'dev-admin', 'cow']);
                            });
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('userProfilePicture', function ($row) {
                    return route('profile.users.image', ['id' => $row->id]);
                })
                ->addColumn('editUrl', function ($row) {
                    return route('admin.users.edit', ['id' => $row->id]);
                })
                ->addColumn('deleteUrl', function ($row) {
                    return route('admin.users.delete', ['id' => $row->id]);
                })
                ->make(true);
        }
    }
    public function edit($id)
    {
        $user = User::find($id);
        $profilePicMedia = $user->profile_pic_media;
        return view('admin.users.edit', ['user' => $user, 'profilePicMedia' => $profilePicMedia]);
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $data = request()->validate([
            'attachment' => 'mimes:jpeg,png',
            'name' => 'required',
        ]);

        if ($request['is_disabled']) {
            $is_disabled = 1;
        } else {
            $is_disabled = 0;
        }

        $user->update(['name' => $request['name'], 'is_disabled' => $is_disabled]);

        if (!empty($data['attachment'])) {
            $attachment_data_url = $data['attachment'];
            $data = file_get_contents($attachment_data_url);
            $updateLogoData['category'] = 'user-profile-icon';
            $updateLogoData['created_by'] = auth()->user()->id;
            $updateLogoData['mimetype'] = $attachment_data_url->getClientMimeType();
            $updateLogoData['data'] = $data;
            $updateLogoData['size'] = mb_strlen($data);
            $updateLogoData['filename'] = 'user_profile_' . date('Y-m-d_H:i:s') . "." . $attachment_data_url->getClientOriginalExtension();
            DB::transaction(function () use ($user, $updateLogoData) {
                $oldProfileMedia = $user->profile_pic_media;
                if (!empty($oldProfileMedia)) {
                    $user->profile_pic_media()->dissociate();
                    $user->save();
                    $oldProfileMedia->delete();
                }

                $user->profile_pic_media()->associate(Media::create($updateLogoData));
                $user->save();
            });
        }

        return redirect()->route('admin.users.index')->with('status', 'User successfully updated.');
    }

    public function deleteUser(Request $request)
    {
        $current_user_id = auth()->user()->id;
        $id = $request->id;

        if ($current_user_id != $id) {

            User::where('id', $id)->delete();
            return redirect()->route('admin.users.index')->withStatus(__('Linkzzapp Admin is successfully deleted.'));
        } else {

            return redirect()->route('admin.users.index')->with('error','You cannot delete your own account.');
        }
    }

}
