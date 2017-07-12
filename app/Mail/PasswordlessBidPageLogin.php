<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordlessBidPageLogin extends Mailable
{
    use Queueable, SerializesModels;
    private $user = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        // user information email, phone, job name
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.passwordlessbidpagelogin')
                      ->subject(__('email.passwordless.subject'))
                      ->with($this->user);
    }
}
