<?php

namespace Laravel\Spark\Http\Controllers\Settings\Profile;

use Illuminate\Http\Request;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Contracts\Interactions\Settings\Profile\UpdateContactInformation;
use Illuminate\Support\Facades\Auth;

class ContactInformationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update the user's contact information settings.
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $this->interaction(
            $request, UpdateContactInformation::class,
            [$request->user(), $request->all()]
        );

        if (Auth::user()->usertype === 'contractor') {
            $location = Auth::user()->contractor()->first()->location()->first();
        } else if (Auth::user()->usertype === 'customer') {
            $location = Auth::user()->customer()->first()->location()->first();
        }

//        dd($location);
        $location->address_line_1 = $request->address_line_1;
        $location->address_line_2 = $request->address_line_2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->zip = $request->zip;

        $location->save();

    }
}
