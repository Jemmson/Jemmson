<?php

namespace App\Http\Controllers;

use App\Contractor;
use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Quickbook;
use App\User;
//use Illuminate\Validation\ValidationException;
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
     * @param Request $request
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

//        dd($request);


//        $message = "hello";

        // validation
        try {
            $this->validate($request, [
                'form' => [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone_number' => 'required',
                    'email' => 'required',
                    'password' => 'required',
                    'terms' => 'required'
                ]
            ]);
        } catch (Illuminate\Validation\ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Error',
                'errors' => $exception->errors(),
            ], 422);
        }

        $fullName = $request['form']['first_name'] . " " . $request['form']['last_name'];

        // create a new user for the contractor
        $user = new User();
        $user->fill([
            'name' => $fullName,
            'first_name' => $request['form']['first_name'],
            'last_name' => $request['form']['last_name'],
            'email' => $request['form']['email'],
            'phone' => $request['form']['phone_number'],
            'billing_address' => $request['form']['address_line_1'],
            'billing_address_line_2' => $request['form']['address_line_2'],
            'billing_city' => $request['form']['city'],
            'billing_state' => $request['form']['state'],
            'billing_zip' => $request['form']['zip'],
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

        if ($request->softwareType == 'quickBooks') {
            $qb = new Quickbook();
            $qb->saveAccessToken($user->id);
            $qb->syncCustomerInformationFromQB($user->id);
            $qb->syncTasksFromQB($user->id);
        }

        if (empty(session('prevDestination'))) {
            return response()->json('/home', 200);
        } else {
            $link = session()->pull('prevDestination');
            return response()->json($link, 200);
        }

    }

}
