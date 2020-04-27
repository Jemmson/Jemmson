<?php

namespace Laravel\Spark\Http\Controllers\Settings\Profile;

use Illuminate\Http\Request;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Contracts\Interactions\Settings\Profile\UpdateContactInformation;
use Illuminate\Support\Facades\Auth;
use App\CustomerNeedsUpdating;

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

        $location->address_line_1 = $request->addressline1;
        $location->address_line_2 = $request->addressline2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->zip = $request->zip;

        try {
            $location->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $cnu = new CustomerNeedsUpdating();
        $cnu->customerHasUpdatedSettings(Auth::user()->getAuthIdentifier());

    }
}
