<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;


    public $name;
    public $token;


    function __construct($name, $token)
    {
        $this->name = $name;
        $this->token = $token;
    }


    public function build()
    {

        $user['token'] = $this->token;
        $user = auth()->user();

        return $this->markdown('emails.contact.contact-form', ['user' => $user]);

    }
}
