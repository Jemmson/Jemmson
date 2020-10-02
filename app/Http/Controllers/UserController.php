<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Cloudinary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    //
    public function validatePhoneNumber(Request $request)
    {
        //    dd($request->num);
        $user = User::select()->where("phone", "=", $request->num)->get()->first();
        if (empty($user)) {
            return User::validatePhoneNumber($request->num);
        } else {
            return ['success', 'mobile', 'mobile', 'alreadyExists'];
        }
    }

    public function loggedIn()
    {
        if (Auth::check()) {
            return response()->json([
                'user' => Auth::user()
            ], 200);

        }
    }

    public function savePhoto(Request $request)
    {
        Auth::user()->photo_url = $request->photo;
        Auth::user()->save();
    }

    public function changePassword(Request $request)
    {

         $same = Hash::check($request->currentPassword, Auth::user()->password);

        if (!$same) {
            return response()->json([
                'error' => 'You Entered The Wrong Current Password'
            ]);
        } else {
            Auth::user()->password = Hash::make($request->password);
            Auth::user()->save();
            return response()->json([
                'success' => 'Password Was Successfully Updated'
            ], 200);
        }
    }

    public function furtherInfo()
    {
        return view('auth.furtherInfo', ['password_updated' => Auth::user()->password_updated]);
    }

    public function checkAuth()
    {
        if (Auth::check()) {
            return response()->json([
                'auth' => true
            ], 200);
        } else {
            return response()->json([
                'auth' => false
            ], 200);
        }
    }

    public function test()
    {
        return ['name' => 'Shawn'];
    }

    public function search()
    {
        $query = Input::get('query');
        $users = \App\User::where([
            ['name', 'like', '%' . $query . '%'],
            ['usertype', '=', 'customer'],
        ])->get();
        return $users;
    }

    public function uploadProfileImage(Request $request)
    {

        $photo = $request->files->get('profilePhoto');

        if ($photo->getFilename() !== '') {
            $fileName = $photo->getFilename();
        } else if ($photo->getClientOriginalName() !== '') {
            $fileName = $photo->getClientOriginalName();
        }

        try {
            $image = Cloudinary\Uploader::upload($photo, [
                "public_id" => $fileName
            ]);
        } catch (\Exception $exception) {
            return response()->json([
               'error' => true,
               'message' => $exception->getMessage()
            ], 500);
        }

        if (empty($image)) {
            return response()->json(['message' => 'Error Uploading Image. Please Try Again'], 400);
        }

        $user = User::find(Auth::user()->getAuthIdentifier());
        $user->photo_url = $image['secure_url'];


        try {
            $user->save();
        } catch (\Exception $e) {
            Log::error('Saving User Image: ' . $e->getMessage());
            return response()->json(['message' => 'error uploading image', errors => [$e->getMessage]], 400);
        }

        return $image['secure_url'];
    }
}
