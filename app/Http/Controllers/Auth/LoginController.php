<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Mail\SendLoginOtp;
use App\Models\LoginOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        
        // Debug: log attempt
        Log::info('Login attempt:', ['email' => $request->email]);
        
        // Cek kredensial
        if (!Auth::validate($credentials)) {
            Log::warning('Login failed: invalid credentials', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput();
        }

        // Generate OTP untuk login
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $email = $request->email;

        Log::info('OTP generated for login:', [
            'email' => $email,
            'otp_code' => $otpCode
        ]);

        // Simpan email ke session untuk verifikasi OTP
        Session::put('login_email', $email);
        Session::put('login_otp_code', $otpCode);

        // Hapus OTP lama
        LoginOtp::where('email', $email)->delete();
        
        // Simpan OTP baru ke database
        LoginOtp::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'is_verified' => false,
            'expires_at' => now()->addMinutes(10)
        ]);

        // Kirim email OTP
        Mail::to($email)->send(new SendLoginOtp($otpCode, $email));

        return redirect()->route('verify.otp.form')
            ->with('message', 'Kode OTP telah dikirim ke email Anda untuk verifikasi login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}