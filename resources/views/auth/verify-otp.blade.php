@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-shield-alt"></i> Verifikasi OTP</h3>
                    <small>Masukkan kode OTP yang dikirim ke email</small>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <form method="POST" action="{{ route('otp.verify') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="otp" class="form-label">Kode OTP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror" 
                                       name="otp" placeholder="Masukkan 6 digit kode" maxlength="6" required>
                            </div>
                            @error('otp')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-check"></i> Verifikasi
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-2">Tidak menerima kode?</p>
                        <form method="POST" action="{{ route('otp.resend') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none">
                                <i class="fas fa-redo"></i> Kirim Ulang OTP
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    input#otp {
        font-size: 24px;
        letter-spacing: 10px;
        text-align: center;
        font-weight: bold;
    }
    @media (max-width: 576px) {
        input#otp {
            font-size: 18px;
            letter-spacing: 5px;
        }
    }
</style>
@endsection