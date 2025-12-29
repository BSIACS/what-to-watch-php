<?php

namespace App\Exceptions;

use Exception;

class ExpiredVerificationException extends Exception
{
    protected $message = 'Verification link has expired';
    protected $code = 410;

    public function __construct(string $message = null, int $code = 410)
    {
        $this->message = $message ?? $this->message;
        $this->code = $code;

        parent::__construct($this->message, $this->code);
    }
}
