<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Task;
use App\Customer;
use App\Contractor;
use App\JobTask;
use App\PasswordlessToken;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;


class PasswordlessController {

    public function jobBid($type, $job_id, $token) 
    {
        // find token in the db
        $token = PasswordlessToken::where('token', $token)->first();
        // invalid token
        if (!$token) {
            Log::warning('invalid token');
            return redirect('/#/home/')->withErrors(__('passwordless.invalid_token'));
        }

        // find user connected to token
        $user = User::find($token->user_id);
        // user not found or login user if they where found
        if (!$user) {
            Log::warning('user not found with given token');
            return redirect('/#/home/')->withErrors(__('passwordless.no_user'));
        } else {
            if ($user->isValidToken($token->token)) {
                Auth::login($user);
                session(['job_id' => $job_id, 'prevDestination' => '/#/bid/' . $job_id]);
                return redirect('/#/bid/' . $job_id);
            } else {
                return redirect('/#/home/')->withErrors(__('passwordless.invalid'));
            }
        }
    }
    /**
     * TODO: DRY with jobBid
     *
     * @param [type] $type
     * @param [type] $job_id
     * @param [type] $token
     * @return void
     */
    public function taskBid($type, $task_id, $token) 
    {
        // find token in the db
        $token = PasswordlessToken::where('token', $token)->first();
        // invalid token
        if (!$token) {
            return redirect('/#/home/')->withErrors(__('passwordless.invalid_token'));
        }

        // find user connected to token
        $user = User::find($token->user_id);
        // user not found or login user if they where found
        if (!$user) {
            return redirect('/#/home/')->withErrors(__('passwordless.no_user'));
        } else {
            if ($user->isValidToken($token->token)) {
                Auth::login($user);
//                session(['task_id' => $task_id, 'prevDestination' => '/#/tasks?taskId=' . $task_id]);
//                return redirect('/#/tasks?taskId=' . $task_id);
                session(['task_id' => $task_id, 'prevDestination' => '/#/bids']);
                return redirect('/#/bids/subs');

            } else {
                return redirect('/#/home')->withErrors(__('passwordless.invalid'));
            }
        }
    }
}
