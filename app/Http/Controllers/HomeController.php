<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contractor;
use App\Customer;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        // TODO: Need to make the user_id unique and update if the user is already in the table
        $this->validate(
        $request,
            [
                'phone_number' => 'required|min:2',
                'address_line_1' => 'required|min:2',
                'city' => 'required|min:2',
                'state' => 'required|min:2',
                'zip' => 'required|min:2',
            ]
        );

        if (!Auth::user()->password_updated) {
            $this->validate($request, [
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6'
            ]);
            Auth::user()->updatePassword(request('password'));
        }
        
        $user_id = Auth::user()->id;

        if (Auth::user()->usertype == 'contractor') {

            // TODO: maybe the registration page does not open if the user is in the system
            // TODO: if email method of contact is selected then there must be an email address
            // TODO: if sms or phone is selected then a phone number must be present
            // TODO: need to add functionality for handling images for company logos if a contractor wants to add it

            $this->validate($request, [
                'company_name' => 'required|min:2'
            ]);

            $contractor = Contractor::firstOrCreate([
                'user_id' => $user_id,
            ]);

            $contractor->update([
                'address_line_1' => request('address_line_1'), //
                'address_line_2' => request('address_line_2'),
                'city' => request('city'), //
                'state' => request('state'), //
                'zip' => request('zip'), //
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

            $customer->update([
                'address_line_1' => request('address_line_1'),
                'address_line_2' => request('address_line_2'),
                'city' => request('city'),
                'state' => request('state'),
                'zip' => request('zip'),
                'notes' => request('notes'),
                'email_method_of_contact' => request('email_contact'),
                'sms_method_of_contact' => request('sms_text'),
                'phone_method_of_contact' => request('phone_contact')
            ]);
        }

        $this->updateUsersPhoneNumber($request->phone_number, $user_id);

        if (empty(session('prevDestination'))) {
            return response()->json('home', 200);
        } else {
            return response()->json(session('prevDestination'), 200);
        }
    }

    public function updateUsersPhoneNumber($phoneNumber, $userId)
    {
        $user = User::find($userId);
        $user->phone = $phoneNumber;
        $user->save();
    }
}
