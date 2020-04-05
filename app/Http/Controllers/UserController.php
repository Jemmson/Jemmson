<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Cloudinary;

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
        // get the file
//        $file = $request->profilePhoto;

        $photo = $request->files->get('profilePhoto');

        // create a hash name for storage and retrieval
//        $path = $photo->hashName('profilePhoto');

        $image = Cloudinary\Uploader::upload($photo, [
            "public_id" => 'dslksdlkdslksdlk'
        ]);

        if (empty($image)) {
            return response()->json(['message' => 'Error Uploading Image. Please Try Again'], 400);
        }

//         store the file
//        $disk = Storage::disk('public');
//        $disk->put(
//            $path, $this->formatImage($file)
//        );

        $user = User::find(Auth::user()->getAuthIdentifier());
        $user->photo_url = $image['secure_url'];
//        Auth::user()->photo_url = $image['secure_url'];


        try {
            $user->save();
        } catch (\Exception $e) {
            Log::error('Saving User Image: ' . $e->getMessage());
//            if (preg_match('/logos\/(.*)$/', $url, $matches)) {
//                $disk->delete('tasks/' . $matches[1]);
//            }
            return response()->json(['message' => 'error uploading image', errors => [$e->getMessage]], 400);
        }

        return $image['secure_url'];
    }
}
