<?php

namespace Laravel\Spark\Http\Controllers\Auth;

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
use App\Traits\Utilities;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RedirectsUsers;
    use Utilities;

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

    public function registerUser(Request $request)
    {

        Log::info("************Create Method - Home Controller - Begin****************");

        $user = new User();
        $user->name = $request['form']['name'];
        $user->email = $request['form']['email'];
        $user->usertype = 'contractor';
        $user->password = bcrypt($request['form']['password']);
        $user->phone = $this->digitsOnly($request['form']['phone_number']);
//        $user->first_name = '';
//        $user->last_name = '';
        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }


        $contractor = new Contractor();
        $contractor->user_id = $user->id;
        $contractor->company_name = $request['form']['company_name'];

        try {
            $contractor->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }

        Auth::login($user);

        if (empty(session('prevDestination'))) {
            Log::info("going to /#/home");
            Log::info("************Create Method - Home Controller - End****************");
            return response()->json('/home', 200);
//            return response()->json('/#/home', 200);
        } else {
            $link = session()->pull('prevDestination');
            Log::info("going to previous destination");
            Log::info("************Create Method - Home Controller - End****************");
            return response()->json($link, 200);
        }

    }

    public function registerContractor(Request $request)
    {

        Log::info("************Create Method - Home Controller - Begin****************");


        try {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'terms' => 'required',
            ]);
        } catch (Illuminate\Validation\ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Error',
                'errors' => $exception->errors(),
            ], 422);
        }


        $user = new User();
        $user->name = $request->first_name . " " . $request->last_name;
        $user->email = $request->email;
        $user->usertype = $request->usertype;
        $user->password = bcrypt($request->password);
        $user->phone = $this->digitsOnly($request->phone);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;

        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }

//        Auth::loginUsingId($user->id);

//        event(new UserRegistered($user));

        return response()->json([
            'redirect' => '/#/furtherInfo'
        ]);


    }


    public function registerTheCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'terms' => 'required',
            'phoneNumber' => 'required',
            'addressLine1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $user = new User();
        $user->name = $request->first_name . " " . $request->last_name;
        $user->email = $request->email;
        $user->usertype = $request->usertype;
        $user->password = bcrypt($request->password);
        $user->phone = Utilities::digitsOnlyStatic($request->phone);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = Utilities::digitsOnlyStatic($request->phoneNumber);
        $user->billing_address = $request->addressLine1;
        $user->billing_address_line_2 = $request->addressLine2;
        $user->billing_city = $request->city;
        $user->billing_state = $request->state;
        $user->billing_zip = $request->zip;
        $user->billing_country = $request->country;
        $user->password_updated = true;
        $user->terms = true;
        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }


        $location = new \App\Location();
        $location->user_id = $user->id;
        $location->address_line_1 = $request->addressLine1;
        $location->address_line_2 = $request->addressLine2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->zip = $request->zip;
        $location->country = $request->country;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }


        $customer = new \App\Customer();
        $customer->user_id = $user->id;
        $customer->location_id = $location->id;

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $user->location_id = $location->id;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        Auth::loginUsingId($user->id);

        $userData = DB::select(['name','first_name','last_name'])->where('id','=',$user->id)->get()->first();

        return response()->json([
            'redirect' => '/#/home',
            'user' => $userData
        ]);
    }


    public function registerTheContractor(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'terms' => 'required',
            'companyName' => 'required',
            'phoneNumber' => 'required',
            'addressLine1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $user = new User();
        $user->name = $request->first_name . " " . $request->last_name;
        $user->email = $request->email;
        $user->usertype = $request->usertype;
        $user->password = bcrypt($request->password);
        $user->phone = Utilities::digitsOnlyStatic($request->phone);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = Utilities::digitsOnlyStatic($request->phoneNumber);
        $user->billing_address = $request->addressLine1;
        $user->billing_address_line_2 = $request->addressLine2;
        $user->billing_city = $request->city;
        $user->billing_state = $request->state;
        $user->billing_zip = $request->zip;
        $user->billing_country = $request->country;
        $user->password_updated = true;
        $user->terms = true;
        try {
            $user->save();
        } catch (\Exception $error) {
            Log::debug($error->getMessage());
        }


        $location = new \App\Location();
        $location->user_id = $user->id;
        $location->address_line_1 = $request->addressLine1;
        $location->address_line_2 = $request->addressLine2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->zip = $request->zip;
        $location->country = $request->country;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $contractor = new \App\Contractor();
        $contractor->user_id = $user->id;
        $contractor->location_id = $location->id;
        $contractor->company_name = $request->companyName;

        try {
            $contractor->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $user->location_id = $location->id;

        \App\License::addLicenses($request->licenses, $user);

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        Auth::loginUsingId($user->id);

//        $userData = User::where('id', '=', $user->id)->select('name','first_name','last_name')->get()->first();
        $userData = $user->select(
            'name',
            'email',
            'usertype',
            'phone',
            'first_name',
            'last_name',
            'billing_address',
            'billing_address_line_2',
            'billing_city',
            'billing_state',
            'billing_zip',
            'billing_country',
            'password_updated',
            'id',
            'location_id'
        )->get()->first();

        return response()->json([
            'redirect' => '/#/home',
            'user' => $userData
        ]);
    }


    /**
     * Handle a registration request for the application.
     *
     * @param RegisterRequest $request
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
     * @param RegisterRequest $request
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
