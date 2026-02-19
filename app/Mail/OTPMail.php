<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OTPMail extends Mailable
{
    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Reset Your Password')
                    ->markdown('emails.resetpassword');
    }
}
