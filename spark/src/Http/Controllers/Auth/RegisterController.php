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
