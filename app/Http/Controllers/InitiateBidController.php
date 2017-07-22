<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\User;
use App\Job;
use App\Mail\PasswordlessBidPageLogin;

class InitiateBidController extends Controller
{
    //
    public function index()
    {
        return view('/contractors/initiateBid');
    }

    public function send(Request $request)
    {
        //dd($request);
        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the bid page

        // look to see if customer is in the db
        $user = User::where('email', $request->email)->first();
        if (!$user) {
          return redirect()->back()->with('error', 'User not found');
        }

        // create bid
        $job_id = $this->createBid($user->id, $request->jobName);
        if($job_id == -1){
          return redirect()->back()->with('error', 'Sorry couldn\'t create bid, please try again.');
        }

        // generate token and save it
        $token = $user->generateToken(true);

        $user = ['email' => $request->email, 'link' => $token->token, 'job_name' => $request->jobName, 'job_id' => $job_id];
        // send mail
        // send a notification along with the passwordless link if the customer or sub is in the system
        $resp = Mail::to($request->email)
              ->queue(new PasswordlessBidPageLogin($user)); // no response from queue or send
        return redirect('/contractor/bid-list');
    }

    /**
     * create new job
     * @var [type]
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
