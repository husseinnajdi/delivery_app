<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public function __construct(
        protected string $token,
        protected string $email
    ) {}

    public function build()
    {
        $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . urlencode($this->email);

        return $this->subject('Reset Your Password')
                    ->view('emails.password_reset')
                    ->with(['resetUrl' => $resetUrl, 'token'    => $this->token,]);
    }
}
