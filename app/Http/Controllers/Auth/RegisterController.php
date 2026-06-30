<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\SendRegistrationOtp;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        // Generate OTP
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $email = $request->email;

        // Simpan data user ke session
        Session::put('registration_data', $request->only(['name', 'email', 'password', 'role']));
        Session::put('registration_otp_code', $otpCode);
        Session::put('registration_email', $email);

        // Hapus OTP lama
        EmailOtp::where('email', $email)->delete();
        
        // Buat OTP baru
        EmailOtp::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'is_verified' => false,
            'expires_at' => now()->addMinutes(10)
        ]);

        // Kirim email OTP
        Mail::to($email)->send(new SendRegistrationOtp($otpCode, $email));

        return redirect()->route('verify.otp.form')
            ->with('message', 'Kode OTP telah dikirim ke email Anda.');
    }
}