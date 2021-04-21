<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use App\Media;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->user();
        if ($user->hasRole('contractor')) {
            $contractor = $user->contractor;
            $profilePicMedia = $user->profile_pic_media;
            return view('profile.edit', ['user' => $user, 'profilePicMedia' => $profilePicMedia, 'contractor' => $contractor]);
        } else {
            $profilePicMedia = $user->profile_pic_media;
            return view('profile.edit', ['user' => $user, 'profilePicMedia' => $profilePicMedia]);
        }
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('contractor')) {

            $contractor = request()->validate([
                'contact_no' => 'required|regex:/(01)[0-9]/',
                'address_1' => 'required',
                'address_2' => 'required',
                'city' => 'required',
                'state' => 'required',
                'postal_code' => 'required|numeric|digits_between:1,10',
                'attachment' => 'mimes:jpeg,png',
                'name' => 'required',

            ]);

            $user_name = $user->name;
            $user_address_1 = $user->contractor->address_1;
            $user_address_2 = $user->contractor->address_2;
            $user_city = $user->contractor->city;
            $user_state = $user->contractor->state;
            $user_postal_code = $user->contractor->address;
            $user_contact_no = $user->contractor->contact_no;

            if ($user_name != request()->name
                || $user_address_1 != request()->address_1
                || $user_address_2 != request()->address_2
                || $user_city != request()->city
                || $user_state != request()->state
                || $user_postal_code != request()->postal_code
                || $user_contact_no != request()->contact_no
                || !empty($contractor['attachment'])) {

                $user->update($contractor);
                $user->contractor->update($contractor);

                $data = request()->validate([
                    'attachment' => 'mimes:jpeg,png',
                ]);
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
            } else {
                return back();
            }
        } else {

            $data = request()->validate([
                'attachment' => 'mimes:jpeg,png',
                'name' => 'required',
            ]);

            $user_name = $user->name;

            if ($user_name != request()->name || !empty($data['attachment'])) {

                auth()->user()->update($request->except('email'));
                $id = auth()->user()->id;
                $user = User::where('id', $id)->first();

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
            } else {

                return back();
            }
        }

        return back()->with('status', 'Profile successfully updated.');
    }

    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->with('status', 'Password successfully updated.');
    }

    public function getUserProfilePicture($id)
    {
        $user = User::with('profile_pic_media')->find($id);
        $userMedia = $user->profile_pic_media;
        if(!empty($userMedia)) {
            return response($userMedia->data, 200)
                ->header('Content-Type', $userMedia->mimetype);
        }
        return response(null, 204);
    }
}
