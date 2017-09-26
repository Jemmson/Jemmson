<?php

namespace App\Http\Controllers;

use App\Customer;
use App\User;
use App\Services\UpdateRecordsService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        dd($customer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
      return view('customers.edit')->with('data', ['customer' => $customer, 'job_id' => session('job_id')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
      $password_updated = $request->password_updated;
      if($request->password != $request->confirm_password && $password_updated == null){
        return redirect()->back()->with('data', ['user_id' => $customer->user_id, 'job_id' => $request->job_id])->withErrors(['Passwords need to match']);
      }
      if($request->password == '' || $request->confirm_password == '' && $password_updated == null){
        return redirect()->back()->with('data', ['user_id' => $customer->user_id, 'job_id' => $request->job_id])->withErrors(['Password field is required']);
      }

      $customer->address_line_1 = $request->address_line_1;
      $customer->address_line_2 = $request->address_line_2;
      $customer->city = $request->city;
      $customer->state = $request->state;
      $customer->zip = $request->zip;
      $customer->preferred_method_of_contact = $request->preferred_method_of_contact;
      $customer->sms_text = $request->sms_text == "on" ? 1 : 0;

      $user = User::find($customer->user_id);
      $user->name = UpdateRecordsService::shouldUpdate($request->name) ? $request->name : $user->name;
      $user->password = $password_updated == null ? bcrypt($request->password) : $user->password;
      $user->password_updated = 1;

      try {
        $user->save();
      } catch (\Exception $e) {
        Log::error('Error Saving User: ' . $e->getMessage());
      }


      try {
        $customer->save();
      } catch (\Exception $e) {
        Log::error('Error Saving Customer: ' . $e->getMessage());
        return redirect()->back()->with('data', ['user_id' => $customer->user_id, 'job_id' => $request->job_id])->withErrors([__('error.data.updated')]);
      }
      // if theres a job id show that job
      if($request->job_id != null){
        return redirect('/job/'.$request->job_id.'/edit')->with('success', __('success.data.updated'));
      }
      return view('home')->with('success', __('success.data.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
