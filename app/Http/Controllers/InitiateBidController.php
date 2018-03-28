<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\SMS\Facades\SMS;
use App\User;
use App\Job;
use App\Mail\PasswordlessBidPageLogin;
use Illuminate\Support\Facades\DB;
use App\Services\RandomPasswordService;
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
        //Things this method should do
        // 1. validate the input - done
        // 2. Get the Contractor that initiated the bid - done
        // 3. Check if there are any more free bids and whether the contractor is subscribed or not
        // 4. If subscribed or has more bids then continue
        // 5. Check if the customer exists in the database and return back to the application if the customer exists
        // 6. If the customer does not exist then create the customer
        // 7. create a jobName if one was not provided
        // 8. if Job could not be created then redirect back
        // 9. if job was created then subtract a job from the contractor or leave at zero if no more free jobs
        // 10. notify the customer that the job was created through email and text
        // 11. redirect to all bids


        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the bid page

        $this->validate($request, [
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email|min:10|max:14',
            'customerName' => 'required',
            'jobName' => 'nullable|regex:/^[a-zA-Z0-9 .\-#,]+$/i'
        ]);

        $contractor = Auth::user()->contractor()->first();

        $this->logData($contractor, 65);

        $this->logData($contractor->numberOfJobsLeft(), 67);
        $this->logData($contractor->isSubscribed(), 68);

        if (!$contractor->hasMoreFreeJobs() && !$contractor->isSubscribed()) {
            return response()->json(['message' => 'No more free Jobs left.', 'errors' => ['no_free_jobs' => 'No more free Jobs left']], 422);
        }

        $customerName = $request->customerName;
        $phone = SanatizeService::phone($request->phone);
        $email = $request->email;
        $jobName = $request->jobName;

        $this->logData($customerName, 79);
        $this->logData($phone, 80);
        $this->logData($email, 81);
        $this->logData($jobName, 82);

        // find customer
        $customerExists = $this->customerExistsInTheDatabase($email, $phone, $customerName);

        $this->logData($customerExists['error']);
        $this->logData($customerExists['customer']);


        if ($customerExists['error']) {
            if ($customerExists['errorText'] == 'Create a new customer') {
                $customer = $this->createNewCustomer($email, $phone, $customerName);
                $this->logData($customer, 91);
                if ($customer == null) {
                    return redirect()->back()->with(
                        'error',
                        'Customer could not be created. Please try initiating the bid again'
                    );
                }
            } else {
                return redirect()->back()->with(
                    'error',
                    $customerExists['errorText'] . " The customer name should be " . $customerExists['name']
                );
            }
        } else {
            $customer = $customerExists['customer'];
        }


        // create a job name if one does not exist
        if (empty($jobName)) {
            $jobName = $this->jobName($customer);
        }

//        $this->logData($jobName);

        // create a bid
        $job = $this->createBid($customer->id, $jobName);
        $job_id = $job->id;

//        $this->logData($job);

        // with error
        if ($job_id == null) {
            // TODO: delete job/token if only one was created
            return redirect()->back()->with(
                'error',
                'Sorry couldn\'t create the bid, please try again.'
            );
        }

//        $this->logData($job_id);

        $contractor->subtractFreeJob();

        $customer->notify(new BidInitiated($job, $customer));

        $request->session()->flash('status', 'Your bid was created');

//        return redirect('/#/bids');
        return "Bid was created";

    }

    public function logData ($data, $num = 0) {
        $f = fopen('logs.txt', 'a+');
        fwrite($f, "$num:\t$data\n");
        fclose($f);
    }

    /**
     * Creating a unique name for the job based upon the customers name
     *
     * @param User $user the user the job is based upon
     *
     * @return string
     */
    public function jobName($customer)
    {
        return $customer->name . uniqid();
    }


    /**
     * This function creates a new user
     *
     * @param string $email the email address of the customer
     * @param string $phone the phone number of the customer
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createNewCustomer($email, $phone, $customerName)
    {
        if (empty($email)) {
            $email = null;
        }

        if (empty($phone) || $phone === '') {
            $phone = null;
        }

        $pass = RandomPasswordService::randomPassword();

        $customer = null;

        $customer = User::create(
            [
                'name' => $customerName,
                'email' => $email,
                'phone' => $phone,
                'usertype' => 'customer',
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]
        );

        Customer::create(
            [
                'user_id' => $customer->id
            ]
        );

        return $customer;

    }

    /**
     * Checking if the customer exists in the database
     *
     * @param string $email the email of the customer
     * @param string $phone the phone number of the customer
     *
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function customerExistsInTheDatabase($email, $phone, $customerName)
    {
        // checking to see if a user exists that has an email or phone number that
        // is already in the database
        $customer = User::where('email', $email)->orWhere('phone', $phone)->first();

        $this->logData($customer, 214);

        // if a user exists then I want to check if the name that was entered by the contractor matches
        // the name of the customer because the names entered should be the same. if the name is different
        // then the contractor should select the correct name from the drop down menu so that the names match.
        if ($customer != null) {
            if ($customer->name != $customerName) {
                return [
                    "error" => true,
                    "name" => $customer->name,
                    "errorText" => "Error: Customer already exists please correct the name."
                ];
            } else {
                return [
                    "error" => false,
                    "customer" => $customer
                ];
            }
        } else {
            return [
                "error" => true,
                "customer" => $customer,
                "errorText" => "Create a new customer"
            ];
        }

    }

    /**
     * Creating the bid
     *
     * @param string $customer_id the customers id
     * @param string $job_name the jobs name
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function createBid($customer_id, $job_name)
    {
        // not the best way but autoincrementing the id number
        // TODO: find a better way but this works for now
        $jobId = DB::select('SELECT id FROM jobs ORDER BY id DESC LIMIT 1');

        $job = new Job;
        if ($jobId == []) {
            $job->id = 1;
        } else {
            $job->id = $jobId[0]->id + 1;
        }
        $job->contractor_id = Auth::user()->id; // actually the user id and not the contractor Id
        $job->customer_id = $customer_id;       // also the user Id and not the customer Id
        $job->job_name = $job_name;
        $job->status = __("status.bid.initiated");


        try {
            $job->save();
        } catch (\Exception $e) {
            Log::critical('Failed to create a bid: ' . $e->getMessage());
            return redirect()->back()->with(
                'error',
                'Job could not be created. Please try initiating the bid again'
            );
//            return null;
        }
        return $job;
    }
}
