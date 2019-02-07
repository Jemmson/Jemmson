<?php

namespace Laravel\Spark\Http\Controllers\Auth;

use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Events\Auth\UserRegistered;
use Laravel\Spark\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Laravel\Spark\Contracts\Interactions\Auth\Register;
use Laravel\Spark\Contracts\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    use RedirectsUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

        $this->redirectTo = Spark::afterLoginRedirect();
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


    public function registerUser(Request $request)
    {

        Log::info("************Create Method - Home Controller - Begin****************");

        dd($request);

        $this->validate(
            $request,
            [
                'email' => 'required|email',
                'name' => 'required|string',
                'phone_number' => 'required|min:10|max:14',
                'address_line_1' => 'required|min:2',
                'city' => 'required|min:2',
                'state' => 'required|min:2',
                'zip' => 'required|min:2',
            ]
        );

        Auth::login($user = Spark::interact(
            Register::class, [$request]
        ));

        event(new UserRegistered($user));

        $user_id = Auth::user()->id;
        $phone = SanatizeService::phone($request->phone_number);

        Log::info("user_id: $user_id");
//        if(!$this->updateUsersPhoneNumber($phone, $user_id, Auth::user()->usertype)){
//            return response()->json([
//                'message' =>
//                    "<span class='notification-error-response'>".
//                    "A ".Auth::user()->usertype." already has this phone number registered.<br>".
//                    "You may already be registered. ".
//                    "<br>Please verify the phone number ".
//                    " and resubmit.</span>"], 422);
//        }

        if (!Auth::user()->password_updated) {
            $this->validate($request, [
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6'
            ]);
            Auth::user()->updatePassword(request('password'));
        }

        if (Auth::user()->usertype == 'contractor') {

//             TODO: maybe the registration page does not open if the user is in the system

            $this->validate($request, [
                'company_name' => 'required|min:2'
            ]);

            $contractor = Contractor::firstOrCreate([
                'user_id' => $user_id,
            ]);

            $contractor->updateLocation($request);
            $contractor->update([
                'company_logo_name' => request('file_name'), //
//                'email_method_of_contact' => request('email_contact'), //
//                'sms_method_of_contact' => request('sms_text'), //
//                'phone_method_of_contact' => request('phone_contact'), //
                'company_name' => request('company_name'), //
            ]);

            $updateUserLocationID = User::find($user_id);
            $updateUserLocationID->location_id = $contractor->location_id;
            $updateUserLocationID->save();

            if (!empty($request->qbCompanyId)) {
                Quickbook::firstOrCreate([
                    'company_id' => $request->qbCompanyId,
                    'user_id' => $user_id
                ]);
            }

        } else if (Auth::user()->usertype == 'customer') {

            // TODO: if email method of contact is selected then there must be an email address
            // TODO: if sms or phone is selected then a phone number must be present

            $customer = Customer::firstOrCreate([
                'user_id' => $user_id,
            ]);

            $customer->updateLocation($request);
            $customer->update([
                'notes' => request('notes'),
                'email_method_of_contact' => request('email_contact'),
                'sms_method_of_contact' => request('sms_text'),
                'phone_method_of_contact' => request('phone_contact')
            ]);

            $updateUserLocationID = User::find($user_id);
            $updateUserLocationID->location_id = $customer->location_id;
            $updateUserLocationID->save();
        }

        $user = Auth::user();
        $user->email = trim($request->email);
        $user->name = $request->name;
        $splitName = explode(" ", $request->name);
        if (count($splitName) > 1) {
            $user->first_name = $splitName[0];
            $user->last_name = $splitName[1];
        }
        $user->phone = $phone;

        $user->save();

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



    /**
     * Handle a registration request for the application.
     *
     * @param  RegisterRequest $request
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
        Auth::login($user = Spark::interact(
            Register::class, [$request]
        ));

        event(new UserRegistered($user));

        return response()->json([
            'redirect' => '/#/furtherInfo'
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  RegisterRequest $request
     * @return Response
     */
//    public function register(RegisterRequest $request)
//    {
//        Auth::login($user = Spark::interact(
//            Register::class, [$request]
//        ));
//
//        event(new UserRegistered($user));
//
//        return response()->json([
//            'redirect' => '/#/furtherInfo'
//        ]);
//    }
}
