<?php

namespace App\Exceptions;


class UnableToAddPreferredPaymentException extends \RuntimeException {

    public function __construct($message)
    {
        $this->message = $message;
    }
}


