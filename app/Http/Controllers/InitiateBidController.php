<?php

namespace App\Http\Controllers;

use App\ContractorCustomer;
use App\JobStatus;
use App\Quickbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Job;
use App\Customer;
use App\Notifications\BidInitiated;
use App\Services\SanatizeService;


class InitiateBidController extends Controller
{
    /**
     * Returning the initial page to initiate the view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('initiate-bid');
    }

    /**
     * Initiate a bid
     *
     * @param Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function send(Request $request)
    {

        // validate the input
        $this->validate($request, [
            'phone' => 'required|min:10|max:14',
            'customerName' => 'required',
            'jobName' => 'nullable|regex:/^[a-zA-Z0-9 .\-#,]+$/i'
        ]);

        // validate that the contractor can even create a new job
        $contractor = Auth::user()->contractor()->first();
        if (!$contractor->canCreateNewJob()) {
            return response()->json(
                [
                    'message' => 'No more free Jobs left.',
                    'errors' => ['no_free_jobs' => 'No more free Jobs left']
                ], 422);
        }


        // create a new customer if the customer is not in the database
        $customerName = $request->customerName;
        $phone = SanatizeService::phone($request->phone);
        $customer = User::checkIfUserExistsByPhoneNumber($phone);
        if (empty($customer)) {
            // quickbooks feature must be turned on
            // contractor must have a quickbooks account
            if (config('app.quickBooks')) {
                $accountingSoftware = $contractor->checkAccountingSoftware();
                if ($accountingSoftware != null) {
                    if (!empty($request->quickbooks_id)) {
                        $customer = $contractor->firstOrCreateAccountingSoftwareCustomer(
                            $accountingSoftware,
                            Auth::user()->getAuthIdentifier(),
                            $customerName, $phone, $request->quickbooks_id
                        );
                    } else {

                        // TODO: creating a new customer that is not in Quickbooks

                        $customer = Customer::createNewCustomer($phone, $customerName);
                        Quickbook::addNewCustomerToQuickBooks($customer);
//                        $contractor->firstOrCreateAccountingSoftwareCustomer(
//                            $accountingSoftware,
//                            Auth::user()->getAuthIdentifier(),
//                            $customerName, $phone);

                        // associate the customer with the contractor
                        $cc = new ContractorCustomer();
                        if ($cc->checkIfCustomerCurrentlyExistsForContractor(
                            $contractor->id, $customer->id)) {
                            $cc->associateCustomer(
                                $contractor->id,
                                $customer->id);
                        }
                    }
                }
            } else {
                $customer = Customer::createNewCustomer($phone, $customerName);
                // associate the customer with the contractor
                $cc = new ContractorCustomer();
                if ($cc->checkIfCustomerCurrentlyExistsForContractor(
                    $contractor->id, $customer->id)) {
                    $cc->associateCustomer(
                        $contractor->id,
                        $customer->id);
                }
            }

        }

        // create the job
        $job = new Job();
        $jobName = $job->jobName($request->jobName);
//        $job = $job->createBid($customer->id, $jobName, Auth::user()->id);
        if (!$job->createBid($customer->id, $jobName, Auth::user()->id)) {
            return response()->json(
                [
                    'message' => 'Unable to create new job.',
                    'errors' => ['job_creation_failed' => 'Job could not be created. Please try initiating the bid again']
                ], 422);
        }
        $js = new JobStatus();
        $js->setStatus($job->id, config("app.initiated"));

        //notify the customer the job was created
        $customer->notify(new BidInitiated($job, $customer));

        // notify the user
        $request->session()->flash('status', 'Your bid was created');
        return "Bid was created";

    }


}
