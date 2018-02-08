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
                // 'email_method_of_contact' => 'required|min:2',
                // 'address_line_1' => 'required|min:2',
                // 'address_line_2' => 'required|min:2',
                // 'city' => 'required|min:2',
                // 'state' => 'required|min:2',
                // 'zip' => 'required|min:2',
                // 'company_logo_name' => 'required|min:2',
                // 'sms_method_of_contact' => 'required|min:2',
                // 'phone_method_of_contact' => 'required|min:2',
                // 'phone_number' => 'required|min:2',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6'
            ]
        );

        Auth::user()->updatePassword(request('password'));
        $user_id = Auth::user()->id;

        if (Auth::user()->usertype == 'contractor') {

            // TODO: maybe the registration page does not open if the user is in the system
            // TODO: if email method of contact is selected then there must be an email address
            // TODO: if sms or phone is selected then a phone number must be present

            $this->validate(request(), [
                'company_name' => 'required',
            ]);

//            TODO: need to add functionality for handling images for company logos if a contractor wants to add it

            $contractor = Contractor::firstOrCreate([
                'user_id' => $user_id,
            ]);

            $contractor->update([
                'email_method_of_contact' => request('email_contact'), //
                'address_line_1' => request('address_line_1'), //
                'address_line_2' => request('address_line_2'),
                'city' => request('city'), //
                'state' => request('state'), //
                'zip' => request('zip'), //
                'company_logo_name' => request('file_name'), //
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
                'email_method_of_contact' => request('email_method_of_contact'),
                'address_line_1' => request('address_line_1'),
                'address_line_2' => request('address_line_2'),
                'city' => request('city'),
                'state' => request('state'),
                'zip' => request('zip'),
                'notes' => request('notes'),
                'sms_method_of_contact' => request('sms_method_of_contact'),
                'phone_method_of_contact' => request('phone_method_of_contact')
            ]);
        }

        $this->updateUsersPhoneNumber($request->phone_number, $user_id);

        return redirect(session('prevDestination'));
    }

    public function updateUsersPhoneNumber($phoneNumber, $userId)
    {
        $user = User::find($userId);
        $user->phone = $phoneNumber;
        $user->save();
    }
}
