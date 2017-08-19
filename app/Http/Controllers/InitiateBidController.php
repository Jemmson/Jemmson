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

        // look to see if customer is in the db
        $email = $request->email;
        $phone = $request->phone;

        if($email == '' && $phone == ''){
          return redirect()->back()->with('error', __('validation.missing.2', ['val1' => 'email', 'val2' => 'phone']));
        }

        // find user
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();
        $pass = RandomPasswordService::randomPassword(); // TODO send password through email? no need since its a passwordless link

        // send psw email
        if (!$user && $email != '') {
          $user = User::create([
              'name' => explode('@',$email)[0],
              'email' => $email,
              'password' => bcrypt($pass),
          ]);
        }elseif(!$user && $phone != ''){ // send psw phone
          $user = User::create([
              'name' => $phone,
              'phone' => $phone,
              'password' => bcrypt($pass),
          ]);
        }

        // create bid
        $job_id = $this->createBid($user->id, $request->jobName);
        if($job_id == -1){
          return redirect()->back()->with('error', 'Sorry couldn\'t create the bid, please try again.');
        }

        // generate token and save it
        $token = $user->generateToken(true);
        // generate data for views
        $data = ['email' => $request->email, 'link' => $token->token, 'job_name' => $request->jobName, 'job_id' => $job_id, 'contractor' => Auth::user()->name];

        if($email != ''){
          //send passwordless email
          $resp = Mail::to($request->email)
               ->queue(new PasswordlessBidPageLogin($data)); // no response from queue or send
        }

        if($phone != ''){
          // send sms passwordless link
          session()->put('phone', $phone);
          SMS::send('sms.passwordlessbidpagelogin', $data, function($sms) {
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
    private function createBid($customer_id, $job_name){
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
