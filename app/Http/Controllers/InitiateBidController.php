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
     * This method sends an email or sms to the customer
     *
     * @param Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function send(Request $request)
    {
        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the bid page

        $this->validate($request, [
            'email' => 'required_without:phone',
            'phone' => 'required_without:email|min:7|max:10',
            'customerName' => 'required'
        ]);

        $customerName = $request->customerName;
        $email = $request->email;
        $phone = $request->phone;
        $jobName = $request->jobName;

        // find user
        $userExists = $this->customerExistsInTheDatabase($email, $phone, $customerName);

        if ($userExists['error']) {
            if ($userExists['errorText'] == 'Create a new user') {
                $user = $this->createNewUser($email, $phone, $customerName);
            } else {
                // TODO: redirect back to page with the error text and the user name to be corrected
            }
        } else {
            $user = $userExists['user'];
        }

        // create a job name if one does not exist
        if (empty($jobName)) {
            $jobName = $this->jobName($user);
        }

        // create a bid
        $job = $this->createBid($user->id, $jobName);
        $job_id = $job->id;


        // generate token and save it
        $token = $user->generateToken(true);

        // if we fail to create a job or token redirect back 
        // with error
        if ($job_id == null || $token == null) {
            // TODO: delete job/token if only one was created
            return redirect()->back()->with(
                'error',
                'Sorry couldn\'t create the bid, please try again.'
            );
        }

        // generate data for views
        $data = [
            'email' => $email,
            'link' => $token->token,
            'job_name' => $jobName,
            'job_id' => $job_id,
            'contractor' => Auth::user()->name
        ];


        if (!empty($email)) {
            $user->notify(new BidInitiated($job, $user));
        }

        //$phone = "4807034902";
        // TODO: delete phone != '' in prod 
        // phone should be required
        if (!empty($phone) && $phone != '') {
            $this->sendText($data, $phone);
        }

        $request->session()->flash('status', 'Your bid was created');

        return redirect('/bid-list');
    }

    /**
     * Creating a unique name for the job based upon the customers name
     *
     * @param User $user the user the job is based upon
     *
     * @return string
     */
    public function jobName($user)
    {
        return $user->name . uniqid();
    }

    /**
     * Sending an Email to the customer or the contractor
     *
     * @param array $data the data associated with the job
     * @param string $email the email address to send the mail to
     */
    public function sendEmail($data, $email)
    {
        // send passwordless email
        $resp = Mail::to($email)
            ->queue(
                new PasswordlessBidPageLogin($data)
            ); // no response from queue or send
    }

    /**
     * Sending a text to the customer or the contractor
     *
     * @param array $data the data associated with the job
     * @param string $phone the phone number of the customer
     */
    public function sendText($data, $phone)
    {
        // send sms passwordless link
        session()->put('phone', $phone);

        $nexmo = app('Nexmo\Client');

        $nexmo->message()->send([
            'to' => '1' . $phone,
            'from' => env('NEXMO_FROM_NUMBER'),
            'text' => 'Welcome To Jemmson' . '
                       ' . $data['contractor'] . ' has initated a bid.
                      Job Name: ' . $data['job_name'] . ' Login Link: ' . url('/login/' . $data['link'] . '/' . $data['job_id'])
        ]);

        session()->forget('phone');
    }

    /**
     * This function creates a new user
     *
     * @param string $email the email address of the customer
     * @param string $phone the phone number of the customer
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createNewUser($email, $phone, $customerName)
    {
        if (empty($email)) {
            $email = null;
        }

        if (empty($phone) || $phone === '') {
            $phone = null;
        }

        $pass = RandomPasswordService::randomPassword();

        return User::create(
            [
                'name' => $customerName,
                'email' => $email,
                'phone' => $phone,
                'usertype' => 'customer',
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]
        );

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
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();

        if ($user != null) {
            if ($user->name != $customerName) {
                return [
                    "error" => true,
                    "name" => $user->name,
                    "errorText" => "Error: Customer already exists please correct the name"
                ];
            } else {
                return [
                    "error" => false,
                    "user" => $user
                ];
            }
        } else {
            return [
                "error" => true,
                "user" => $user,
                "errorText" => "Create a new user"
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
            return null;
        }
        return $job;
    }
}
