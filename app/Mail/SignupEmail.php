<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_user;
    // public $reset_code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->email_user = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('signup-email')->with([
            'email_user' => $this->email_user

        ]);

        //    return $this->from(
        //     env(('key.MAIL_USERNAME'),  'name.jean108adjanohoun@gmail.com')
        //    )
        //    ->subject("BIENVENU SUR LE SITE DE YOUPIGOO")
        //    ->view('signup-email',
        //     [
        //         'email_user' =>$this->email_user
        //     ]
        // );
    }
}