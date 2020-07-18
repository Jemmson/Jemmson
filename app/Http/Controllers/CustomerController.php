<?php

namespace App\Http\Controllers;

use App\ContractorCustomer;
use App\Customer;
use App\QuickbooksCustomer;
use App\User;
use App\Location;
use Illuminate\Support\Facades\Auth;
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

    public function updateCustomerNotes(Request $request)
    {
//        TODO:: this has to change so that these notes are contractor to customer notes. they are contractor_customer_general. they are customer_contractor_job_specific

        $customer = Customer::where('user_id', '=', $request->customer_id)->get()->first();
        $customer->notes = $request->customerNotesMessage;

        try {
            $customer->save();
            return response()->json([
                'success' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e
            ], 422);

        }

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
//        dd($customer);
    }

    public function getCustomer($id)
    {
        $user = [];
        $user['user'] = User::select(
            [
                'name',
                'first_name',
                'last_name',
                'email',
                'photo_url',
                'phone'
            ]
        )->find($id);
        $user['user']['customer'] = User::find($id)->customer()->select([
            'notes',
        ])->get()->first();
        $user['user']['location'] = User::find($id)->customer()->get()->first()->location()->select([
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'zip',
            'country'
        ])->get()->first();

        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit')->with('data', ['customer' => $customer, 'job_id' => session('job_id')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Customer $customer
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

//        $result = DB::transaction(function () use ($location, $user, $customer) {
//            $customer->updateLocation($request);
//            $user->save();
//            $customer->save();
//        });

        // try {
        // } catch (\Exception $e) {
        //   Log::error('Error Saving Customer: ' . $e->getMessage());
        //   return redirect()->back()->with('data', ['user_id' => $customer->user_id, 'job_id' => $request->job_id])->withErrors([__('error.data.updated')]);
        // }

        // if theres a job id show that job
        if ($request->job_id != null) {
            return redirect('/job/' . $request->job_id . '/edit')->with('success', __('success.data.updated'));
        }
        return view('/#/home/')->with('success', __('success.data.updated'));
    }

    public function getAddress(Request $request)
    {
        $location = DB::select("select address_line_1, address_line_2, city, state, zip from locations where id = $request->locationId");

        return $location[0]->address_line_1 . " " .
            $location[0]->address_line_2 . " " .
            $location[0]->city . " " .
            $location[0]->state . " " .
            $location[0]->zip;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function getName(Request $request)
    {
        return User::select()->where("id", "=", $request->id)->get()->first();
    }

    public function getCustomerAssociatedToContractor(Request $request)
    {

        // TODO: needs to search users table, customer table, contractorcustomer table, and quickbooks_customer table
        $query = $request->query('query');

        $users = User::getCustomersInUserTableByName($query);

        if (empty($users[0])) {
            return QuickbooksCustomer::getAssociatedCustomers($query, Auth::user()->getAuthIdentifier());
        } else {
            $associatedUsers = ContractorCustomer::getAssociatedCustomers($users, Auth::user()->getAuthIdentifier());
            $jobNumber = self::getLatestJobNumber();
            $users = [];
            foreach ($associatedUsers as $user) {
                $u = User::select(['id', 'name', 'first_name', 'last_name', 'phone', 'email'])
                    ->where('id', '=', $user['user_id'])->get()->first()
                    ->toArray();
                $u['payment_type'] = $user['quickbooks_id'];
                $u['quickbooks_id'] = $user['quickbooks_id'];
                $u['jobNumber'] = $jobNumber + 1;
                array_push($users, $u);
            }

            return $users;
        }

    }

    public function getLatestJobNumber()
    {
        $jobs = Auth::user()->jobs()->get();

        $jobNames = [];
        foreach ($jobs as $job) {
            array_push($jobNames, $job->job_name);
        }

        $jobNumbers = [];
        foreach ($jobNames as $jobName) {
            $jobNumberArray = explode('-', $jobName);
            if (count($jobNumberArray) > 1 && is_numeric($jobNumberArray[1])) {
                array_push($jobNumbers, (int) $jobNumberArray[1]);
            }
        }

        if (count($jobNumbers) > 0) {
            sort($jobNumbers);
            return $jobNumbers[count($jobNumbers) - 1];
        } else {
            return 99;
        }

    }
}
