<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.Forgotpassword')
        ->from('code.404.initiative@gmail.com')
        ->subject('Forgot Password')
        ->with([
            'name' => 'New Serenity User',
            'link' => 'http://localhost:3000/'
        ]);
         ;
    }
}
