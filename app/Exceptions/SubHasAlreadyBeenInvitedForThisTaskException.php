<?php

namespace App\Exceptions;


class SubHasAlreadyBeenInvitedForThisTaskException extends \RuntimeException {

    public function __construct($message)
    {
        $this->message = $message;
    }
}


