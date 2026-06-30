<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OtpVerifyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'otp_code' => 'required|string|size:6',
            'type' => 'required|in:registration,login'
        ];
    }

    public function messages()
    {
        return [
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.',
            'type.required' => 'Tipe verifikasi tidak valid.',
        ];
    }
}