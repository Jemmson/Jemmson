<?php

namespace App\Http\Controllers;

use App\Contractor;
use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Events\Auth\UserRegistered;
use Laravel\Spark\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Laravel\Spark\Contracts\Interactions\Auth\Register;
use Laravel\Spark\Contracts\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Log;
use App\Quickbook;
use App\Http\Controllers\QuickBooksController;
use Laravel\Spark\User;

class RegisterController extends Controller
{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

//        $this->redirectTo = Spark::afterLoginRedirect();
    }

    /**
     * Show the application registration form.
     *
     * @param  Request $request
     * @return Response
     */
    public function showRegistrationForm(Request $request)
    {
        if (Spark::promotion() && !$request->has('coupon')) {
            // If the application is running a site-wide promotion, we will redirect the user
            // to a register URL that contains the promotional coupon ID, which will force
            // all new registrations to use this coupon when creating the subscriptions.
            return redirect($request->fullUrlWithQuery([
                'coupon' => Spark::promotion()
            ]));
        }
        return view('spark::auth.register');
    }


    public function registerContractor(Request $request)
    {

        Log::info("************Create Method - Home Controller - Begin****************");

        $user = new User();
        $user->name = $request['form']['name'];
        $user->email = $request['form']['email'];
        $user->usertype = 'contractor';
        $user->password = bcrypt($request['form']['password']);
        $user->phone = $request['form']['phone_number'];
//        $user->first_name = '';
//        $user->last_name = '';
        $user->save();


        $contractor = new Contractor();
        $contractor->user_id = $user->id;
        $contractor->company_name = $request['form']['company_name'];
        $contractor->save();

//        log::debug(Auth::login(user));

        Auth::logout();

        Auth::loginUsingId($user->id);

        if (empty(session('prevDestination'))) {
            Log::info("going to /#/home");
            Log::info("************Create Method - Home Controller - End****************");
            return response()->json('/#/home', 200);
        } else {
            $link = session()->pull('prevDestination');
            Log::info("going to previous destination");
            Log::info("************Create Method - Home Controller - End****************");
            return response()->json($link, 200);
        }

    }

}
