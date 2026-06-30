<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpCode extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $otp;

    public function __construct(User $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Verifikasi OTP - Perpustakaan')
                    ->view('emails.otp')
                    ->with([
                        'name' => $this->user->name,
                        'otp' => $this->otp,
                    ]);
    }
}
