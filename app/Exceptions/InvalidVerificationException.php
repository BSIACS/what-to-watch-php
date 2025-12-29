<?php

namespace App\Exceptions;

use Exception;

class InvalidVerificationException extends Exception
{
    protected $message = 'Invalid verification link';
    protected $code = 400;

    public function __construct(string $message = null, int $code = 400)
    {
        $this->message = $message ?? $this->message;
        $this->code = $code;

        parent::__construct($this->message, $this->code);
    }
}
