<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\OtpVerifyRequest;
use App\Models\EmailOtp;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
    public function showForm()
    {
        // Debug: cek session
        Log::info('Session data:', [
            'registration_data' => Session::has('registration_data'),
            'login_email' => Session::get('login_email'),
            'registration_email' => Session::get('registration_email')
        ]);

        // Cek apakah ada data registrasi atau login pending
        if (!Session::has('registration_data') && !Session::has('login_email')) {
            return redirect()->route('login')
                ->with('error', 'Tidak ada sesi verifikasi. Silakan login atau daftar ulang.');
        }

        $email = Session::get('registration_email') ?? Session::get('login_email');
        $type = Session::has('registration_data') ? 'registration' : 'login';
        
        return view('auth.verify-otp', compact('email', 'type'));
    }

    public function verify(OtpVerifyRequest $request)
    {
        $type = $request->type;
        
        // Debug: log request
        Log::info('OTP Verification attempt:', [
            'type' => $type,
            'otp_code' => $request->otp_code,
            'session_email' => Session::get('registration_email') ?? Session::get('login_email')
        ]);
        
        if ($type === 'registration') {
            return $this->verifyRegistration($request);
        } elseif ($type === 'login') {
            return $this->verifyLogin($request);
        }

        return back()->withErrors(['otp_code' => 'Tipe verifikasi tidak valid.']);
    }

    private function verifyRegistration(OtpVerifyRequest $request)
    {
        $email = Session::get('registration_email');
        $otpCode = $request->otp_code;

        if (!$email) {
            return redirect()->route('register')
                ->withErrors(['error' => 'Sesi registrasi tidak ditemukan. Silakan daftar ulang.']);
        }

        // Cari OTP dengan kondisi yang tepat
        $otp = EmailOtp::where('email', $email)
                       ->where('otp_code', $otpCode)
                       ->where('is_verified', false)
                       ->where('expires_at', '>', now())
                       ->latest()
                       ->first();

        // Debug: log hasil pencarian
        Log::info('Registration OTP check:', [
            'email' => $email,
            'otp_code' => $otpCode,
            'found' => $otp ? 'yes' : 'no',
            'otp_data' => $otp ? $otp->toArray() : null
        ]);

        if (!$otp) {
            // Cek apakah OTP sudah kadaluwarsa
            $expiredOtp = EmailOtp::where('email', $email)
                                  ->where('otp_code', $otpCode)
                                  ->where('is_verified', false)
                                  ->latest()
                                  ->first();
            
            if ($expiredOtp && $expiredOtp->expires_at <= now()) {
                return back()->withErrors(['otp_code' => 'Kode OTP sudah kadaluwarsa. Silakan minta kode baru.']);
            }

            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid atau sudah kadaluwarsa.']);
        }

        // Tandai OTP sebagai terverifikasi
        $otp->update(['is_verified' => true]);

        // Ambil data dari session
        $userData = Session::get('registration_data');
        
        // Simpan user ke database
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'] ?? 'member'
        ]);

        // Bersihkan session
        Session::forget(['registration_data', 'registration_otp_code', 'registration_email']);

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }

    private function verifyLogin(OtpVerifyRequest $request)
    {
        $email = Session::get('login_email');
        $otpCode = $request->otp_code;

        if (!$email) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Sesi login tidak ditemukan. Silakan login ulang.']);
        }

        // Cari OTP dengan kondisi yang tepat
        $otp = LoginOtp::where('email', $email)
                       ->where('otp_code', $otpCode)
                       ->where('is_verified', false)
                       ->where('expires_at', '>', now())
                       ->latest()
                       ->first();

        // Debug: log hasil pencarian
        Log::info('Login OTP check:', [
            'email' => $email,
            'otp_code' => $otpCode,
            'found' => $otp ? 'yes' : 'no',
            'otp_data' => $otp ? $otp->toArray() : null
        ]);

        if (!$otp) {
            // Cek apakah OTP sudah kadaluwarsa
            $expiredOtp = LoginOtp::where('email', $email)
                                  ->where('otp_code', $otpCode)
                                  ->where('is_verified', false)
                                  ->latest()
                                  ->first();
            
            if ($expiredOtp && $expiredOtp->expires_at <= now()) {
                return back()->withErrors(['otp_code' => 'Kode OTP sudah kadaluwarsa. Silakan minta kode baru.']);
            }

            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid atau sudah kadaluwarsa.']);
        }

        // Tandai OTP sebagai terverifikasi
        $otp->update(['is_verified' => true]);

        // Login user
        $user = User::where('email', $email)->first();
        Auth::login($user);

        // Bersihkan session
        Session::forget(['login_email', 'login_otp_code']);

        // Redirect ke dashboard
        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang kembali!');
    }

    public function resendOtp(Request $request)
    {
        $type = $request->type;
        
        if ($type === 'registration') {
            return $this->resendRegistrationOtp();
        } elseif ($type === 'login') {
            return $this->resendLoginOtp();
        }

        return back()->withErrors(['error' => 'Tipe tidak valid.']);
    }

    private function resendRegistrationOtp()
    {
        $email = Session::get('registration_email');
        
        if (!$email) {
            return redirect()->route('register')
                ->withErrors(['error' => 'Sesi registrasi tidak ditemukan. Silakan daftar ulang.']);
        }

        // Generate OTP baru
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Hapus OTP lama
        EmailOtp::where('email', $email)->delete();
        
        // Buat OTP baru
        EmailOtp::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'is_verified' => false,
            'expires_at' => now()->addMinutes(10)
        ]);

        // Update session
        Session::put('registration_otp_code', $otpCode);

        // Kirim email
        Mail::to($email)->send(new \App\Mail\SendRegistrationOtp($otpCode, $email));

        return back()->with('message', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    private function resendLoginOtp()
    {
        $email = Session::get('login_email');
        
        if (!$email) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Sesi login tidak ditemukan. Silakan login ulang.']);
        }

        // Generate OTP baru
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Hapus OTP lama
        LoginOtp::where('email', $email)->delete();
        
        // Buat OTP baru
        LoginOtp::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'is_verified' => false,
            'expires_at' => now()->addMinutes(10)
        ]);

        // Update session
        Session::put('login_otp_code', $otpCode);

        // Kirim email
        Mail::to($email)->send(new \App\Mail\SendLoginOtp($otpCode, $email));

        return back()->with('message', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}