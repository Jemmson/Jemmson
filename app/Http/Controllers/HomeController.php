<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contractor;

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
        // this is the home page
        return view('home');
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
//        dd($request);

        // TODO: Need to make the user_id unique and update if the user is already in the table
        // TODO: maybe the registration page does not open if the user is in the system

        $this->validate(request(), [
            'user_id' => 'required|',
//            'email_method_of_contact' => 'required|min:2',
//            'address_line_1' => 'required|min:2',
//            'address_line_2' => 'required|min:2',
//            'city' => 'required|min:2',
//            'state' => 'required|min:2',
//            'zip' => 'required|min:2',
//            'company_logo_name' => 'required|min:2',
//            'sms_method_of_contact' => 'required|min:2',
//            'phone_method_of_contact' => 'required|min:2',
//            'phone_number' => 'required|min:2',
            'company_name' => 'required',
        ]);


        Contractor::create([
            'user_id' => request('user_id'),
            'email_method_of_contact' => request('email_contact'), //
            'address_line_1' => request('address_line_1'), //
            'address_line_2' => request('address_line_2'),
            'city' => request('city'), //
            'state' => request('state'), //
            'zip' => request('zip'), //
            'company_logo_name' => request('file_name'), //
            'sms_method_of_contact' => request('sms_text'), //
            'phone_method_of_contact' => request('phone_contact'), //
            'phone_number' => request('phone_number'), //
            'company_name' => request('company_name'), //
        ]);

        return view('/home');
    }
}
