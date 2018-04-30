<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contractor;
use App\Customer;
use App\User;
use App\Feedback;

use Illuminate\Support\Facades\Auth;
use Log;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use App\Services\SanatizeService;


class HomeController extends Controller
{
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
        return view('home', compact('user'));
    }

    /**
     * Test different actions with a route
     * @return [type] [description]
     */
    public function test(Request $request)
    {

    }

    public function create(Request $request)
    {
        //check that the phone number is unique for a contractor in the database


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

        $user_id = Auth::user()->id;
        $phone = SanatizeService::phone($request->phone_number);
        $this->updateUsersPhoneNumber($phone, $user_id);

        if (!Auth::user()->password_updated) {
            $this->validate($request, [
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6'
            ]);
            Auth::user()->updatePassword(request('password'));
        }
        

        if (Auth::user()->usertype == 'contractor') {

            // TODO: maybe the registration page does not open if the user is in the system
            // TODO: if email method of contact is selected then there must be an email address
            // TODO: if sms or phone is selected then a phone number must be present
            // TODO: need to add functionality for handling images for company logos if a contractor wants to add it

            $contractor = User::where('phone', $phone)->first();

            if (!empty($contractor)) {
                return response()->json([
                    'message' =>
                        "<span class='notification-error-response'>A contractor already has this phone number registered.<br>".
                        "You may already be registered. ".
                        "<br>Please verify the phone number ".
                        " and resubmit.</span>"], 422);
            }

            $this->validate($request, [
                'company_name' => 'required|min:2'
            ]);

            $contractor = Contractor::firstOrCreate([
                'user_id' => $user_id,
            ]);

            $contractor->updateLocation($request);
            $contractor->update([
                'company_logo_name' => request('file_name'), //
                'email_method_of_contact' => request('email_contact'), //
                'sms_method_of_contact' => request('sms_text'), //
                'phone_method_of_contact' => request('phone_contact'), //
                'company_name' => request('company_name'), //
            ]);

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
        }

        $user = Auth::user();
        $user->email = $request->email;
        $user->name = $request->name;

        $user->save();

        if (empty(session('prevDestination'))) {
            return response()->json('/#/home', 200);
        } else {
            $link = session()->pull('prevDestination');
            return response()->json($link, 200);
        }
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
            $disk->delete('logos/'.$matches[1]);
        }
        return $url;
    }

    protected function formatImage($file)
    {
        $images = new ImageManager;
        return (string) $images->make($file->path())
                            ->fit(150)->encode();
    }

    public function updateUsersPhoneNumber($phoneNumber, $userId)
    {
        $user = User::find($userId);
        $user->phone = $phoneNumber;
        Log::debug('saving phone');
        try {
            $user->save();
        } catch (\Exception $e) {
            Log::error('Update Phone: ' . $e->getMessage());
            abort(422, 'This number exists in the system already, please login or try another number.');
        }
    }
}
