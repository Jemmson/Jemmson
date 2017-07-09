<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordlessBidPageLogin extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        // user information email, phone, job name
        $this->user = ['email' => $user->email, 'jobName' => $user->jobName, 'link' => $user->link];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.passwordlessbidpagelogin')
                      ->with(['email' => $this->user['email'],
                              'jobName' => $this->user['jobName'],
                              'link' => $this->user['link']]);
    }
}
