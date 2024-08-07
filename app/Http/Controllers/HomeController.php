<?php

namespace App\Http\Controllers;

use App\CustomerNeedsUpdating;
use Illuminate\Http\Request;
use App\Contractor;
use App\Customer;
use App\Quickbook;
use App\User;
use App\Feedback;
use QuickBooksOnline\API\DataService\DataService;

use Illuminate\Support\Facades\Auth;
use Log;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use App\Services\SanatizeService;
use App\Traits\Utilities;


class HomeController extends Controller
{

    use Utilities;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('subscribed');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show()
    {
        $user = Auth::user();

        // this is the home page
        return view('/#/home/', compact('user'));
    }

    /**
     * Test different actions with a route
     * @return [type] [description]
     */
    public function test(Request $request)
    {

    }

    // further info method
    public function create(Request $request)
    {

        Log::info("************Create Method - Home Controller - Begin****************");


        $this->validate(
            $request,
            [
                'email' => 'required|email|unique:users,email',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'phone_number' => 'required|min:10|max:14',
                'address_line_1' => 'required|min:2',
                'city' => 'required|min:2',
                'state' => 'required|min:2',
                'zip' => 'required|min:2',
                'terms' => 'required'
            ]
        );

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
                'company_name' => request('company_name'), //
            ]);

            $updateUserLocationID = User::find($user_id);
            $updateUserLocationID->location_id = $contractor->location_id;

            try {
                $updateUserLocationID->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }

            if (!empty($request->qbCompanyId)) {
                Quickbook::firstOrCreate([
                    'company_id' => $request->qbCompanyId,
                    'user_id' => $user_id
                ]);
            }

            \App\License::addLicenses($request->licenses, Auth::user());

        } else if (Auth::user()->usertype == 'customer') {


            // TODO: if email method of contact is selected then there must be an email address
            // TODO: if sms or phone is selected then a phone number must be present

            $customer = Customer::firstOrCreate([
                'user_id' => $user_id,
            ]);

            // location updated or is new but this location can be added to the job because
            // this is the first job that is created so default location will work
            // once the contractor goes to the created job then they can update the
            // job location if it is different
            $customer->updateLocation($request);
            $customer->update([
                'notes' => request('notes'),
                'email_method_of_contact' => request('email_contact'),
                'sms_method_of_contact' => request('sms_text'),
                'phone_method_of_contact' => request('phone_contact')
            ]);

            $updateUserLocationID = User::find($user_id);
            $updateUserLocationID->location_id = $customer->location_id;

            try {
                $updateUserLocationID->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }

        }

        $user = Auth::user();
        $user->email = trim($request->email);
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        $user->name = "$request->first_name $request->last_name";
        $user->phone = $this->digitsOnly($request->phone_number);
        $user->billing_address = trim($request->address_line_1);
        $user->billing_address_line_2 = trim($request->address_line_2);
        $user->billing_city = trim($request->city);
        $user->billing_state = trim($request->state);
        $user->billing_zip = trim($request->zip);
        $user->billing_country = 'US';
        $user->terms = true;
        $user->sms = $request->sms_text;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $cnu = new CustomerNeedsUpdating();
        $cnu->customerHasUpdatedSettings($user->id);


        if($user->usertype == 'contractor') {
            return response()->json('/bids', 200);
        } else {
            return response()->json('/bids', 200);
        }


//        if (empty(session('prevDestination'))) {
//            Log::info("going to /#/home");
//            Log::info("************Create Method - Home Controller - End****************");
//        } else {
//            $link = session()->pull('prevDestination');
//            Log::info("going to previous destination");
//            Log::info("************Create Method - Home Controller - End****************");
//            return response()->json($link, 200);
//        }


    }

    /**
     * Submit Feedback
     *
     * @param Request $request
     * @return void
     */
    public function feedback(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'page_url' => 'required',
            'comment' => 'required'
        ]);
        $feedback = new Feedback;
        $feedback->user_id = $request->user_id;
        $feedback->page_url = $request->page_url;
        $feedback->comment = $request->comment;

        try {
            $feedback->save();
        } catch (\Exception $e) {
            Log::error('Feedback: ' . $e->getMessage());
            return response()->json(['message' => 'error'], 400);
        }

        return response()->json($feedback, 200);
    }

    public function uploadCompanyLogo(Request $request)
    {
        $this->validate($request, [
            'photo' => 'max:2056',
        ]);
        $file = $request->photo;
        $user = $request->user();

        $path = $file->hashName('logos');

        $disk = Storage::disk('public');

        $disk->put(
            $path, $this->formatImage($file)
        );

        $oldPhotoUrl = $user->logo_url;

        $url = $disk->url($path);
        $user->forceFill([
            'logo_url' => $url,
        ])->save();

        if (preg_match('/logos\/(.*)$/', $oldPhotoUrl, $matches)) {
            $disk->delete('logos/' . $matches[1]);
        }
        return $url;
    }

    protected function formatImage($file)
    {
        $images = new ImageManager;
        return (string)$images->make($file->path())
            ->fit(150)->encode();
    }

    public function updateUsersPhoneNumber($phoneNumber, $userId, $usertype)
    {

        if ($usertype === 'contractor') {
            $user = User::where('phone', $phoneNumber)
                ->where('usertype', '=', 'contractor')->first();
        } else {
            $user = User::where('phone', $phoneNumber)
                ->where('usertype', '=', 'customer')->first();
        }

        if (!empty($user)) {
            return false;
        }

        $user = User::find($userId);
        $user->phone = $this->digitsOnly($phoneNumber);
        Log::debug('saving phone');
        try {
            $user->save();
            return true;
        } catch (\Exception $e) {
            Log::error('Update Phone: ' . $e->getMessage());
            abort(422, 'This number exists in the system already, please login or try another number.');
            return false;
        }
    }

//    ******************************
// utility methods

//    public function ()
//    {
//
//    }
}
