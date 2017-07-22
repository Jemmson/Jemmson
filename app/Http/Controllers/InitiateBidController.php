<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\User;
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
        //
        // send link to user mail
        $user = User::where('email', $request->email)->first();
        if (!$user) {
          return redirect()->back(404)->with('error', 'User not found');
        }

        // generate token and save it
        $token = $user->generateToken(true);

        //  TODO: create link
        $user = ['email' => $request->email, 'link' => $token->token, 'jobName' => $request->jobName];
        // send mail
        // send a notification along with the passwordless link if the customer or sub is in the system
        $resp = Mail::to($request->email)
              ->queue(new PasswordlessBidPageLogin($user)); // no response from queue or send
        return redirect('/contractor/bid-list');
    }
}
