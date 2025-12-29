<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $verificationUrl;

    function __construct(string $name, string $verificationUrl)
    {
        $this->name = $name;
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->subject('Добро пожаловать!')
            ->view('emails.verify-email')
            ->with(['name' => $this->name, 'verificationUrl' => $this->verificationUrl]);
    }
}
