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
    //
    public function index()
    {
        return view('/contractors/initiateBid');
    }

    public function send(Request $request)
    {
        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the bid page

        // look to see if request fields have been filled out
        $email = $request->email == '' ? -1 : $request->email;
        $phone = $request->phone == '' ? -1 : $request->phone;

        // redirect back to the initiate bid page if they were missed
        if ($email == -1 && $phone == -1) {
            return redirect()->back()->with('error', __('validation.missing.2', ['val1' => 'email', 'val2' => 'phone']));
        }

        // find user
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();
        $pass = RandomPasswordService::randomPassword();

        // send psw email
        if (!$user && $email != -1 && $phone != -1) {
            $user = User::create([
                'name' => explode('@', $email)[0],
                'email' => $email,
                'phone' => $phone,
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]);
        } elseif (!$user && $phone != -1) { // send psw phone
            $user = User::create([
                'name' => $phone,
                'phone' => $phone,
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]);
        } elseif (!$user && $email != -1) {
            $user = User::create([
                'name' => explode('@', $email)[0],
                'email' => $email,
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]);
        }

        // create bid
        $job_id = $this->createBid($user->id, $request->jobName);
        if ($job_id == -1) {
            return redirect()->back()->with('error', 'Sorry couldn\'t create the bid, please try again.');
        }

        // generate token and save it
        $token = $user->generateToken(true);
        // generate data for views
        $data = ['email' => $request->email, 'link' => $token->token, 'job_name' => $request->jobName, 'job_id' => $job_id, 'contractor' => Auth::user()->name];

        if ($email != -1) {
            //send passwordless email
            $resp = Mail::to($request->email)
                ->queue(new PasswordlessBidPageLogin($data)); // no response from queue or send
        }

        if ($phone != -1) {
            // send sms passwordless link
            session()->put('phone', $phone);
            SMS::send('sms.passwordlessbidpagelogin', $data, function ($sms) {
                $sms->to(session('phone'));
            });
            session()->forget('phone');
        }

        return redirect('/contractor/bid-list');
    }

    /**
     * create new job
     * @var [job id]
     */
    private function createBid($customer_id, $job_name)
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
            return -1;
        }
    }
}
