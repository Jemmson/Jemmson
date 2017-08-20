<?php

namespace App\Http\Controllers;

use App\Customer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Check that we have all pertinent data
     * @return view
     */
    public function checkCustomerData()
    {
      $data = session()->get('data');
      $user_id = $data['user_id'];

      if($data['job_id'] != null)
        session()->put('job_id', $data['job_id']);

      // find or create customer
      try {
        $customer = Customer::where('user_id', "=", $user_id)->firstOrFail();
      } catch (\Exception $e) {
        Log::error('Error Finding Customer: ' . $e->getMessage());
        $customer = new Customer;
        $customer->user_id = $user_id;
        try {
          $customer->save();
        } catch (\Exception $e) {
          Log::error('Error Saving Customer: ' . $e->getMessage());
        }
      }

      // if data is missing, get data
      if(!$customer->preferred_method_of_contact || !$customer->address_line_1
                                                 || !$customer->city
                                                 || !$customer->state
                                                 || !$customer->zip){
        return $this->edit($customer);
      }else{
        return redirect('/job/'.session('job_id').'/edit');
      }
    }
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
      $customer->address_line_1 = $request->address_line_1;
      $customer->address_line_2 = $request->address_line_2;
      $customer->city = $request->city;
      $customer->state = $request->state;
      $customer->zip = $request->zip;
      $customer->preferred_method_of_contact = $request->preferred_method_of_contact;
      $customer->sms_text = $request->sms_text == "on" ? 1 : 0;

      if($request->name != null){
        $user = User::find($customer->user_id);
        $user->name = $request->name;
        try {
          $user->save();
        } catch (\Exception $e) {
          Log::error('Error Saving User: ' . $e->getMessage());
        }

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
