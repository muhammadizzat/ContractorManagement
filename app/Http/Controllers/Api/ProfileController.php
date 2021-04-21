<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Media;
use Illuminate\Support\Facades\DB;

use ImageOptimizer;
use App\Http\Resources\User as UserResource;

class ProfileController extends Controller
{
    public $successStatus = 200;
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */

    public function postUserChangePassword(Request $request) {  
        $data = request()->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_new_password' => 'required|min:8',
        ]);

        if (!(Hash::check($data['current_password'],  Auth::user()->password))) {
                return back()->with('error', 'Your current password is incorrect.');
        }else if($data['new_password'] != $data['confirm_new_password']){
            return back()->with('error', 'Passwords do not match');
        }else{
            User::where('id', Auth::user()->id)->update([
                'password' => bcrypt($data['new_password']),
                'change_password' => 1
            ]);
            return back()->with('status', 'Password was successfully updated.');
        }
    }
    
    public function getProfile()
    {
        $user = auth()->user();
        if ($user->hasRole('contractor')) {
            $id = auth()->user()->id;
            $user = User::with('contractor')->where('id', $id)->first();
            return response()->json(['user' => $user]);
        } else {
            $id = auth()->user()->id;
            $user = User::where('id', $id)->first();
            return response()->json(['user' => $user]);
        }
    }


    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUpdateProfile(ProfileRequest $request)
    {
        $user = auth()->user();
        $name = request()->validate([
            'name' => 'required'
        ]);

        if ($user->hasRole('contractor')) {
            $contractor = request()->validate([
                'contact_no' => 'required|regex:/(01)[0-9]/',
                'address_1' => 'required',
                'address_2' => 'required',
                'city' => 'required',
                'state' => 'required',
                'postal_code' => 'required|numeric|digits_between:1,10',

                'name' => 'required',

            ]);

            $user->update($contractor);
            $user->contractor->update($contractor);
        }
        else {
            $user->update($name);
        }

        return response()->json(null, 200);
    }

    public function getProfilePic()
    {
        $user = Auth::user();
        if (empty($user->profile_pic_media)) {
            return response()->json(null, 204);
        }

        $media = $user->profile_pic_media;
        $dataUrl = 'data:'.$media->mimetype.';base64,'.base64_encode($media->data);

        return response()->json(['data_url' => $dataUrl]);
    }

    public function postProfilePic()
    {
        $data = request()->validate([
            'image_data_url' => 'required',
        ]);

        $imageData = self::processBase64DataUrl($data['image_data_url']);
        $imageData = self::optimizeImage($imageData);

        $user = auth()->user();
        $updateLogoData['category'] = 'user-profile-icon';
        $updateLogoData['mimetype'] = $imageData['mime_type'];
        $updateLogoData['data'] = $imageData['data'];
        $updateLogoData['size'] = $imageData['size'];
        $updateLogoData['filename'] = 'user_profile_' . date('Y-m-d_H:i:s') . "." . $imageData['extension'];
        $updateLogoData['created_by'] = $user->id;
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

        return response()->json(null, 200);
    }

    private static function processBase64DataUrl($dataUrl) {
        $parts = explode(',', $dataUrl);

        preg_match('#data:(.*?);base64#', $parts[0], $matches);
        $mimeType = $matches[1];
        $extension = explode('/', $mimeType)[1];

        $data = base64_decode($parts[1]);

        return [
            'data' => $data,
            'mime_type' => $mimeType,
            'size' => mb_strlen($data),
            'extension' => $extension
        ];
    }

    private static function optimizeImage($imageData) {
        $filename = 'temp_img_'.rand().'_'.date('Y-m-d_H:i:s').".".$imageData['extension'];
        $temp_filepath = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($temp_filepath, $imageData['data']);
        ImageOptimizer::optimize($temp_filepath);
        $optimized_image_data = file_get_contents($temp_filepath);
        unlink($temp_filepath);

        $imageData['data'] = $optimized_image_data;
        $imageData['size'] = mb_strlen($optimized_image_data);

        return $imageData;
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function info()
    {
        $user = Auth::user();

        return response()->json($user, $this->successStatus);
    }
}
