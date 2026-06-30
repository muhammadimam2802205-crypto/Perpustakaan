<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendLoginOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $email;

    public function __construct($otpCode, $email)
    {
        $this->otpCode = $otpCode;
        $this->email = $email;
    }

    public function build()
    {
        return $this->subject('Kode OTP Login Anda')
                    ->view('emails.login-otp')
                    ->with([
                        'otpCode' => $this->otpCode,
                        'email' => $this->email,
                    ]);
    }
}