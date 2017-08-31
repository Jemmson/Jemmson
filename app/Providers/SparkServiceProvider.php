<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

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
        'pike.shawn@gmail.com'
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

        Spark::validateUsersWith(function () {
            return [
                'name' => 'required|max:255',
                'usertype' => 'in:contractor,customer',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|confirmed|min:6',
                'vat_id' => 'max:50|vat_id',
                'terms' => 'required|accepted',
            ];
        });

//        TODO: I need to figure out how to make usertype insert into the database

//        Spark::createUsersWith(function ($request) {
//            $user = Spark::user();
//
//            $data = $request->all();
//
//            $user->forceFill([
//                'name' => $data['name'],
//                'email' => $data['email'],
////                'usertype' => $data['usertype'],
//                'password' => bcrypt($data['password']),
//                'last_read_announcements_at' => Carbon::now(),
//                'trial_ends_at' => Carbon::now()->addDays(Spark::trialDays()),
//            ])->save();
//
//            return $user;
//        });


        Spark::useStripe()->noCardUpFront()->trialDays(10);

        Spark::freePlan()
            ->features([
                'First', 'Second', 'Third'
            ]);

        Spark::plan('Basic', 'basic-monthly')
            ->price(10)
            ->features([
                'First', 'Second', 'Third'
            ]);

        Spark::plan('Pro', 'pro-plan')
            ->price(20)
            ->features([
                'First', 'Second', 'Third'
            ]);
    }
}
