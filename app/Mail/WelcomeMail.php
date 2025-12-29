<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Добро пожаловать!')
            ->view('emails.welcome')
            ->with(['name' => 'Пользователь']);
    }
}
