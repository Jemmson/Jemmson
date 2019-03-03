<?php

namespace App\Http\Controllers;

use App\Contractor;
use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Location;

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

        // validation
        $this->validate($request, [
            'form.email' => 'required|email|unique:users,email'
        ]);

        // create a new user for the contractor
        $user = new User();
        $user->fill([
            'name' => $request['form']['name'],
            'email' => $request['form']['email'],
            'phone' => $request['form']['phone_number'],
            'usertype' => 'contractor',
            'password' => bcrypt($request['form']['password']),
//        'last_name' => '',
//        'first_name' => '',
        ]);

        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }


        // create a new location for the contractor
        $location = new Location();
        $location->fill([
            'user_id' => $user->id,
            'default' => true,
            'address_line_1' => $request['form']['address_line_1'],
            'address_line_2' => $request['form']['address_line_2'],
            'city' => $request->$request['form']['city'],
            'area' => $request->$request['form']['city'],
            'state' => $request->$request['form']['state'],
            'zip' => $request->$request['form']['zip']
        ]);

        try {
            $location->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }

        // attach the location to the user
        $user->location_id = $location->id;
        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }

        // add user to the contractor table
        $contractor = new Contractor();
        $contractor->fill([
            'user_id' => $user->id,
            'company_name' => $request['form']['company_name'],
            'location_id' => $location->id
        ]);

        if ($request->softwareType != '') {
            $contractor->accounting_software = $request->softwareType;
        }

        try {
            $contractor->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }

        // log the contractor in and then send him to the right location
        Auth::loginUsingId($user->id);
        if (empty(session('prevDestination'))) {
            return response()->json('/home', 200);
        } else {
            $link = session()->pull('prevDestination');
            return response()->json($link, 200);
        }

    }

}
