<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // store simple role string for ease of checks
            'role' => 'member',
            'is_verified' => false
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp_code' => $otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );

        try {
            Mail::send('emails.otp', ['otp' => $otp, 'name' => $request->name], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Verifikasi OTP - Perpustakaan');
            });
        } catch (\Exception $e) {
            Log::error('Mail Error: ' . $e->getMessage());
        }

        session(['register_email' => $request->email]);

        return redirect()->route('otp.verify.form')
                        ->with('success', 'Silahkan verifikasi OTP yang telah dikirim ke email Anda.');
    }

    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        $email = session('register_email');
        if (!$email) {
            return redirect()->route('register')->with('error', 'Sesi registrasi berakhir.');
        }

        $otpVerification = EmailOtp::where('email', $email)
                                        ->where('otp_code', $request->otp)
                                        ->where('expired_at', '>', Carbon::now())
                                        ->first();

        if (!$otpVerification) {
            return back()->with('error', 'OTP tidak valid atau telah kadaluarsa.');
        }

        $otpVerification->update(['is_verified' => true]);
        User::where('email', $email)->update(['is_verified' => true]);

        session()->forget('register_email');

        return redirect()->route('login')->with('success', 'Verifikasi berhasil! Silahkan login.');
    }

    public function resendOtp()
    {
        $email = session('register_email');
        if (!$email) {
            return redirect()->route('register')->with('error', 'Sesi registrasi berakhir.');
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        EmailOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp_code' => $otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );

        $user = User::where('email', $email)->first();
        
        Mail::send('emails.otp', ['otp' => $otp, 'name' => $user->name], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Verifikasi OTP - Perpustakaan');
        });

        return back()->with('success', 'OTP baru telah dikirim ke email Anda.');
    }
}