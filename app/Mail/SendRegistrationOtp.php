<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRegistrationOtp extends Mailable
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
        return $this->subject('Verifikasi Email Anda - OTP Registration')
                    ->view('emails.registration-otp')
                    ->with([
                        'otpCode' => $this->otpCode,
                        'email' => $this->email,
                    ]);
    }
}