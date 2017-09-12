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

        $email = $request->email;
        $phone = $request->phone;
        $jobName = $request->jobName;

        $this->validateInput($email, $phone);

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

        $this->sendEmail($data, $email);

        $this->sendText($data, $phone);

        return redirect('/bid-list');
    }

    public function sendEmail($data, $email)
    {
        if ($email != -1) {
            //send passwordless email
            $resp = Mail::to($email)
                ->queue(new PasswordlessBidPageLogin($data)); // no response from queue or send
        }
    }

    public function sendText($data, $phone)
    {
        if ($phone != -1) {
            // send sms passwordless link
            session()->put('phone', $phone);
            SMS::send('sms.passwordlessbidpagelogin', $data, function ($sms) {
                $sms->to(session('phone'));
            });
            session()->forget('phone');
        }
    }

    public function createNewUser($email, $phone)
    {
        if ($email == -1)
            $email = NULL;

        if ($phone == -1)
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

    public function validateInput($email, $phone)
    {
        // look to see if request fields have been filled out
        $email = $email == '' ? -1 : $email;
        $phone = $phone == '' ? -1 : $phone;

        // TODO: need to handle this error (1/1) Swift_RfcComplianceException Address in mailbox given [] does not comply with RFC 2822, 3.6.2. when malformed email is present.

        // redirect back to the initiate bid page if they were missed
        if ($email == -1 && $phone == -1) {
            try {
//                dd('there was an error');
//                return redirect()->back()->with('error', __('validation.missing.2', ['val1' => 'email', 'val2' => 'phone']));
//                return redirect()->action('InitiateBidController@index');
                return view('initiate-bid');
            } catch (\Swift_RfcComplianceException $e) {
                dd($e);
            }
        }

//        $rules = array(
//            'phone' => 'required_without_all:email',
//            'email' => 'required_without_all:phone',
//        );
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
////            dd('validator fails');
//            dd($validator);
//            return redirect('initiate-bid')
//                ->withErrors($validator)
//                ->withInput();
//        }

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
