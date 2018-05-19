<?php

namespace App\Http\Controllers;

use App\Customer;
use App\User;
use App\Location;

use App\Services\UpdateRecordsService;

use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        dd($customer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit')->with('data', ['customer' => $customer, 'job_id' => session('job_id')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $password_updated = $request->password_updated;
        if ($request->password != $request->confirm_password && $password_updated == null) {
            return redirect()->back()->with('data', ['user_id' => $customer->user_id, 'job_id' => $request->job_id])->withErrors(['Passwords need to match']);
        }
        if ($request->password == '' || $request->confirm_password == '' && $password_updated == null) {
            return redirect()->back()->with('data', ['user_id' => $customer->user_id, 'job_id' => $request->job_id])->withErrors(['Password field is required']);
        }


        $customer->preferred_method_of_contact = $request->preferred_method_of_contact;
        $customer->sms_text = $request->sms_text == "on" ? 1 : 0;

        $user = User::find($customer->user_id);
        $user->name = UpdateRecordsService::shouldUpdate($request->name) ? $request->name : $user->name;
        $user->password = $password_updated == null ? bcrypt($request->password) : $user->password;
        $user->password_updated = 1;

        $result = DB::transaction(function () use ($location, $user, $customer) {
            $customer->updateLocation($request);
            $user->save();
            $customer->save();
        });

        // try {
        // } catch (\Exception $e) {
        //   Log::error('Error Saving Customer: ' . $e->getMessage());
        //   return redirect()->back()->with('data', ['user_id' => $customer->user_id, 'job_id' => $request->job_id])->withErrors([__('error.data.updated')]);
        // }

        // if theres a job id show that job
        if ($request->job_id != null) {
            return redirect('/job/' . $request->job_id . '/edit')->with('success', __('success.data.updated'));
        }
        return view('home')->with('success', __('success.data.updated'));
    }

    public function getAddress(Request $request)
    {
        $location = DB::select("select address_line_1, address_line_2, city, state, zip from locations where id = $request->locationId");

        $address = $location[0]->address_line_1 . " " .
            $location[0]->address_line_2 . " " .
            $location[0]->city . " " .
            $location[0]->state . " " .
            $location[0]->zip;
//            dd($address);
//        dd($location[0]);
        return $address;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function getName(Request $request)
    {
        $customer = User::select()->where("id", "=", $request->id)->get()->first();
        return $customer;
    }
}
