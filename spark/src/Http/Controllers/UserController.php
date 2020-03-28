<?php

namespace Laravel\Spark\Http\Controllers;

use Carbon\Carbon;
use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Laravel\Spark\Contracts\Repositories\UserRepository;
use App\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only(
            'updateLastReadAnnouncementsTimestamp'
        );
    }

    public function test()
    {
        return ['name' => 'Shawn'];
    }

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

    public function search()
    {
        $query = Input::get('query');
        $users = \App\User::where([
            ['name', 'like', '%' . $query . '%'],
            ['usertype', '=', 'customer'],
        ])->get();
        return $users;
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

    /**
     * Get the current user of the application.
     *
     * @return Response
     */
    public function current()
    {
        return Spark::interact(UserRepository::class.'@current');
    }

    public function validateThePhoneNumber(Request $request)
    {
        return User::validatePhoneNumber($request->number);
    }

    /**
     * Update the last read announcements timestamp.
     *
     * @param  Request  $request
     * @return Response
     */
    public function updateLastReadAnnouncementsTimestamp(Request $request)
    {
        $request->user()->forceFill([
            'last_read_announcements_at' => Carbon::now(),
        ])->save();
    }
}
