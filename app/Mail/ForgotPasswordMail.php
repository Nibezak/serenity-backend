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
        ->from('mugisha-dev@letsreason.co')
        ->subject('Forgot Password')
        ->with([
            'name' => 'New Letsreason User',
            'link' => 'https://letsreason.co/'
        ]);
         ;
    }
}
