<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

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
}
