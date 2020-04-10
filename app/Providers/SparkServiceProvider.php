<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;
use Auth;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor' => 'Jemmson, Inc',
        'product' => 'Contractor Services',
        'street' => '2035 Sunset Lake RoadSuite B-2',
        'location' => 'Newark, Delaware 19702',
        'phone' => '489-703-4902',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = null;

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        //
        'pike.shawn@gmail.com',
        'davene1919@gmail.com'
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {

//        Spark::validateUsersWith(function () {
//            return [
//                'name' => 'required|max:255',
//                'email' => 'required|email|max:255|unique:users',
//                'password' => 'required|confirmed|min:6',
//                'vat_id' => 'max:50|vat_id',
//                'terms' => 'required|accepted',
//                'usertype' => 'required'
//            ];
//        });



        try {
//            $this->validate($request, [
//                'first_name' => 'required',
//                'last_name' => 'required',
//                'email' => 'required|email|unique:users,email',
//                'password' => 'required',
//                'terms' => 'required',
//            ]);


            Spark::validateUsersWith(function () {
                return [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'terms' => 'required',
                ];
            });



        } catch (Illuminate\Validation\ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Error',
                'errors' => $exception->errors(),
            ], 422);
        }




        Spark::useStripe()->noCardUpFront()->trialDays(30);

//        Spark::freePlan()
//            ->features([
//                'First', 'Second', 'Third'
//            ]);

        Spark::plan('Subscription', 'basic-monthly')
            ->price(15)
            ->features([
                'Unlimited Jobs With Customers',
                'Unlimited Subcontractors for a Job',
                'Unlimited Payments from Stripe'
            ]);

//        Spark::plan('Pro', 'pro-plan')
//            ->price(20)
//            ->features([
//                'First', 'Second', 'Third'
//            ]);
    }

    /**
     * Load init user and their relationships
     *
     * @return void
     */
    public function register()
    {
//        dd('spark/RegisterController');
        Spark::swap('UserRepository@current',
            function () {
                // Return the current user...
                if (Auth::user() != null) 
                    return Auth::user()->load('subscriptions',
                        'contractor.stripeExpress', 'customer',
                        'contractor.location',
                        'customer.location',
                        'contractor.stripeExpress.stripeAccountVerification');
                else
                    return null;
            }
        );
    }
}
