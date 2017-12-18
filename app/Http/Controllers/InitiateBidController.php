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
use App\Services\RandomPasswordService;

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


        $email = $request->email;
        $phone = $request->phone;
        $jobName = $request->jobName;

        if (empty($email) && empty($phone)) {
            return redirect('initiate-bid');
        }

        if (!empty($email)) {
            $this->validate(request(), ['email' => 'email']);
        }

        if (!empty($phone)) {
            $this->validate(request(), ['phone' => 'min:7|max:10']);
        }

        // find user
        $user = $this->customerExistsInTheDatabase($email, $phone);

        if ($user == false) {
            $user = $this->createNewUser($email, $phone);
        }

        // create a job name if one does not exist
        if (empty($jobName)) {
            $jobName = $this->jobName($user);
        }

        // create a bid
        $job_id = $this->createBid($user->id, $jobName);

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
            $this->sendEmail($data, $email);
        }

        if (!empty($phone)) {
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
        return $user->name.uniqid();
    }

    /**
     * Sending an Email to the customer or the contractor
     *
     * @param array  $data  the data associated with the job
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
     * @param array  $data  the data associated with the job
     * @param string $phone the phone number of the customer
     */
    public function sendText($data, $phone)
    {
        // send sms passwordless link
        session()->put('phone', $phone);
        SMS::send(
            'sms.passwordlessbidpagelogin', $data, function ($sms) {
                $sms->to(session('phone'));
            }
        );
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
    public function createNewUser($email, $phone)
    {
        if (empty($email)) {
            $email = null;
        }

        if (empty($phone)) {
            $phone = null;
        }

        $pass = RandomPasswordService::randomPassword();

        return User::create(
            [
                'name' => explode('@', $email)[0],
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
    public function customerExistsInTheDatabase($email, $phone)
    {
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();
        $result = count($user);

        if ($result === 1) {
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Creating the bid
     *
     * @param string $customer_id the customers id
     * @param string $job_name    the jobs name
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function createBid($customer_id, $job_name)
    {
        $job = new Job;
        $job->contractor_id = Auth::user()->id;
        $job->customer_id = $customer_id;
        $job->job_name = $job_name;
        $job->status = "initiated";

        try {
            $job->save();
            return $job->id;
        } catch (\Exception $e) {
            Log::critical('Failed to create a bid: ' . $e);
            return null;
        }
    }
}
