<?php

namespace App\Exceptions;

use Exception;

class EmailAlreadyVerifiedException extends Exception
{
    protected $message = 'This email is already verified';
    protected $code = 409;

    public function __construct(string $message = null, int $code = 409)
    {
        $this->message = $message ?? $this->message;
        $this->code = $code;

        parent::__construct($this->message, $this->code);
    }
}
