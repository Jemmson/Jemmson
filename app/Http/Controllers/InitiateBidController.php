<?php

namespace App\Http\Controllers;

use App\Contractor;
use App\ContractorCustomer;
use App\JobStatus;
use App\Quickbook;
use App\CustomerNeedsUpdating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Job;
use App\Customer;
use App\Notifications\BidInitiated;
use App\Services\SanatizeService;
use Illuminate\Support\Facades\DB;

class InitiateBidController extends Controller
{

    /**
     * Returning the initial page to initiate the view
     *
     * @return \Illuminate\Contracts\View\Factory\Illuminate\View\View
     */
    public function index()
    {
        return view('initiate-bid');
    }


    public function automationSendUpdated($customerId,
                                          $phone,
                                          $contractorId,
                                          $jobName,
                                          $paymentType,
                                          $first_name, $last_name
    )
    {

//        does customer exist in DB
        $customer = self::customerExists($customerId, $phone, $first_name, $last_name);
        $job = self::createJob($jobName, $contractorId, $customer->id);
        $contractor = self::getContractor($contractorId);
        self::updatePaymentType($job, $paymentType);
        self::setJobStatus($job);

        return [
            'jobId' => $job->id,
            'customerId' => $customer->id
        ];
    }

    private function setJobStatus($job)
    {
        $js = new JobStatus();
        $js->setStatus($job->id, config("app.initiated"));
    }

    private function getContractor($contractorId)
    {
        return User::find($contractorId);
    }

    private function createJob($jobName, $contractorId, $customerId)
    {
        // create the job
        $job = new Job();
        $job->job_name = $jobName;
        $job->contractor_id = $contractorId;
        $job->customer_id = $customerId;
        $job->save();
        return $job;
    }

    private function customerExists($customerId, $phone, $first_name, $last_name)
    {
        if ($customerId != 'new') {
            $customer = User::find($customerId);
        } else {
            $customerName = $first_name . " " . $last_name;
            $customer = Customer::createNewCustomer(
                $phone, $customerName, 1, $first_name, $last_name);
        }

        return $customer;
    }


    public function automationSend(
        $customerId,
        $phone,
        $contractorId,
        $jobName,
        $paymentType
    )
    {


        $customerId = intval($customerId);

//                dd($customerId);

        $customer = User::find($customerId);

//        $customer = DB::select('select * from users where id = ' . $customerId)->get;


        $contractor = User::find($contractorId);

//        dd($contractor);

        // create a new customer if the customer is not in the database
        $customerName = $customer->first_name . " " . $customer->last_name;

        $phone = SanatizeService::phone($phone);
        $customer = User::checkIfUserExistsByPhoneNumber($phone);

//        dd($customerName . "\n ". $phone . "\n ". $customer);

        if (empty($customer)) {
            dd($customer->first_name . "\n" . $customer->last_name . "\n" . $customerName);
        }

        if (empty($customer)) {
            // quickbooks feature must be turned on
            // contractor must have a quickbooks account
            if (config('app.quickBooks')) {
                $accountingSoftware = $contractor->checkAccountingSoftware();
                if ($accountingSoftware != null) {
                    // customer exists in QB but not in Jemmson
                    if (!empty($request->quickbooks_id)) {
                        $customer = $contractor->firstOrCreateAccountingSoftwareCustomer(
                            $accountingSoftware,
                            Auth::user()->getAuthIdentifier(),
                            $customerName, $phone, $request->quickbooks_id
                        );
                    } else {
                        // customer is new but is added in Jemmson and not from Quickbooks
                        $customer = Customer::createNewCustomer(
                            $phone, $customerName, Auth::user()->getAuthIdentifier(), $customer->first_name, $customer->last_name);
                        $quickbookId = Quickbook::addNewCustomerToQuickBooks($customer);
                        ContractorCustomer::addQuickBookIdToAssociation(Auth::user()->getAuthIdentifier(), $customer->id, $quickbookId);
                        CustomerNeedsUpdating::addEntryToCustomerNeedsUpdatingIfNeeded(
                            Auth::user()->getAuthIdentifier(),
                            $customer->id,
                            $quickbookId
                        );
                    }
                } else {
                    $customer = Customer::createNewCustomer(
                        $phone, $customerName, Auth::user()->getAuthIdentifier(), $customer->first_name, $customer->last_name);
                }
            } else {
                $customer = Customer::createNewCustomer(
                    $phone, $customerName, 1, $customer->first_name, $customer->last_name);
            }

        }

//        associating the new customer to the contractor
        $cc = new ContractorCustomer();
        $cc->associateCustomer(1, $customer->id);

        // create the job
        $job = new Job();
        $jobName = $job->jobName($jobName);

//        $job = $job->createBid($customer->id, $jobName, Auth::user()->id);
        if (
            !$job->createEstimate(
                $customer->id, $jobName, 1,
                $paymentType
            )) {
            return response()->json(
                [
                    'message' => 'Unable to create new job.',
                    'errors' => ['job_creation_failed' => 'Estimate could not be created. Please try initiating the bid again']
                ], 422);
        }

        $this->updatePaymentType($contractor, $paymentType);

        $js = new JobStatus();
        $js->setStatus($job->id, config("app.initiated"));

        return $job->id;
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

//        dd($request);

        // validate the input
        $this->validate($request, [
            'phone' => 'required|min:10|max:14',
            'customerName' => 'required',
            'jobName' => 'nullable|regex:/^[a-zA-Z0-9 .\-#\',]+$/i'
        ]);


        if (!$this->jobNameMustBeUniqueForContractor($request->jobName)) {
            return response()->json(
                [
                    'message' => 'A Job With this job name already exists. Job Names must be unique',
                    'errors' => ['job_creation_failed' => 'A Job With this job name already exists. Job Names must be unique']
                ], 422);
        }


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
                    // customer exists in QB but not in Jemmson
                    if (!empty($request->quickbooks_id)) {
                        $customer = $contractor->firstOrCreateAccountingSoftwareCustomer(
                            $accountingSoftware,
                            Auth::user()->getAuthIdentifier(),
                            $customerName, $phone, $request->quickbooks_id
                        );
                    } else {
                        // customer is new but is added in Jemmson and not from Quickbooks
                        $customer = Customer::createNewCustomer(
                            $phone, $customerName, Auth::user()->getAuthIdentifier(), $request->firstName, $request->lastName);
                        $quickbookId = Quickbook::addNewCustomerToQuickBooks($customer);
                        ContractorCustomer::addQuickBookIdToAssociation(Auth::user()->getAuthIdentifier(), $customer->id, $quickbookId);
                        CustomerNeedsUpdating::addEntryToCustomerNeedsUpdatingIfNeeded(
                            Auth::user()->getAuthIdentifier(),
                            $customer->id,
                            $quickbookId
                        );
                    }
                } else {
                    $customer = Customer::createNewCustomer(
                        $phone, $customerName, Auth::user()->getAuthIdentifier(), $request->firstName, $request->lastName);
                }
            } else {
                $customer = Customer::createNewCustomer(
                    $phone, $customerName, Auth::user()->getAuthIdentifier(), $request->firstName, $request->lastName);
            }

        }

        $cc = new ContractorCustomer();
        $cc->associateCustomer(Auth::user()->getAuthIdentifier(), $customer->id);

        // create the job
        $job = new Job();
        $jobName = $job->jobName($request->jobName);

//        $job = $job->createBid($customer->id, $jobName, Auth::user()->id);
        if (
            !$job->createEstimate(
                $customer->id, $jobName, Auth::user()->id,
                $request->paymentType
            )) {
            return response()->json(
                [
                    'message' => 'Unable to create new job.',
                    'errors' => ['job_creation_failed' => 'Estimate could not be created. Please try initiating the bid again']
                ], 422);
        }

        $this->updatePaymentType($contractor, $request->paymentTypeDefault);

        $js = new JobStatus();
        $js->setStatus($job->id, config("app.initiated"));

        //notify the customer the job was created
        if (is_null($customer->email)) {
            $customer->notify(new BidInitiated($job, $customer));
        }

        // notify the user
        $request->session()->flash('status', 'Your bid was created');
        return response()->json([
            "job" => $job,
            "customer" => $customer,
            "jobStatuses" => $js
        ]);

    }


    private function jobNameMustBeUniqueForContractor($jobName)
    {
        return is_null(Job::where('job_name', '=', $jobName)->where('contractor_id', '=', Auth::user()->getAuthIdentifier())->get()->first());
    }


    private function updatePaymentType($job, $requestPaymentType)
    {

        $job->payment_type = $requestPaymentType;
        try {
            $job->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

}
