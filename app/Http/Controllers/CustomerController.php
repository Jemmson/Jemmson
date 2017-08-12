<?php

namespace App\Http\Controllers;

use App\Customer;
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
      $user = $data['user'];
      $this->job_id = $data['job_id'];

      // find or create customer
      try {
        $customer = Customer::where('user_id', "=", $user->id)->firstOrFail();
      } catch (\Exception $e) {
        Log::error('Customer Error: ' . $e->getMessage());
        $customer = new Customer;
        $customer->user_id = $user->id;
        try {
          $customer->save();
        } catch (\Exception $e) {
          Log::error('Saving Error: ' . $e->getMessage());
        }
      }

      // if data is missing, get data
      if(!$customer->preferred_method_of_contact || !$customer->address_line_1
                                                 || !$customer->city
                                                 || !$customer->state
                                                 || !$customer->zip){
        return $this->edit($customer);
      }else{
        return redirect('/customer/job/'.$this->job_id);
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
      return view('customers.edit')->with('data', ['customer' => $customer, 'job_id' => $this->job_id]);
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
        dd($request, $customer);
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
