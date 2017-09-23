<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Psr\Log\NullLogger;
use SimpleSoftwareIO\SMS\Facades\SMS;
use Swift_Events_TransportExceptionEventTest;
use App\User;
use App\Job;
use App\Mail\PasswordlessBidPageLogin;

use App\Services\RandomPasswordService;

class InitiateBidController extends Controller
{
    //
    public function index()
    {
        return view('initiate-bid');
    }

    public function send(Request $request)
    {
        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the bid page

//        dd($request->email);

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

        // create a bid
        $job_id = $this->createBid($user->id, $request->jobName);


        // generate token and save it
        $token = $user->generateToken(true);


        // generate data for views
        $data = ['email' => $email, 'link' => $token->token, 'job_name' => $jobName, 'job_id' => $job_id, 'contractor' => Auth::user()->name];


        if (!empty($email)) {
            $this->sendEmail($data, $email);
        }

        if (!empty($phone)) {
            $this->sendText($data, $phone);
        }

        return redirect('/bid-list');
    }

    public function sendEmail($data, $email)
    {
        //send passwordless email
        $resp = Mail::to($email)
            ->queue(new PasswordlessBidPageLogin($data)); // no response from queue or send
    }

    public function sendText($data, $phone)
    {
        // send sms passwordless link
        session()->put('phone', $phone);
        SMS::send('sms.passwordlessbidpagelogin', $data, function ($sms) {
            $sms->to(session('phone'));
        });
        session()->forget('phone');
    }

    public function createNewUser($email, $phone)
    {
        if (empty($email))
            $email = NULL;

        if (empty($phone))
            $phone = NULL;

        $pass = RandomPasswordService::randomPassword();

        return User::create([
            'name' => explode('@', $email)[0],
            'email' => $email,
            'phone' => $phone,
            'password_updated' => false,
            'password' => bcrypt($pass),
        ]);

    }

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
     * create new job
     * @var [job id]
     */
    public function createBid($customer_id, $job_name)
    {
        $job = new Job;
        $job->contractor_id = Auth::user()->id;
        $job->customer_id = $customer_id;
        $job->job_name = $job_name;

        try {
            $job->save();
            return $job->id;
        } catch (\Exception $e) {
            Log::critical('Failed to create a bid: ' . $e);
            return redirect()->back()->with('error', 'Sorry couldn\'t create the bid, please try again.');
        }
    }
}
