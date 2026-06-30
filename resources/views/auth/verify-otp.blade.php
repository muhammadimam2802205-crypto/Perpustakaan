<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
    <style>
        .container { max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { font-size: 14px; color: #666; margin-bottom: 20px; }
        .resend-link { margin-top: 15px; }
        .resend-link a { color: #007bff; text-decoration: none; }
        .resend-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verifikasi Kode OTP</h2>
        
        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <p class="info">
            Kode OTP telah dikirim ke <strong>{{ $email }}</strong>.
            @if($type === 'registration')
                Silakan verifikasi email Anda untuk menyelesaikan registrasi.
            @else
                Silakan masukkan kode OTP untuk menyelesaikan login.
            @endif
        </p>

        <form action="{{ route('verify.otp') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div class="form-group">
                <label for="otp_code">Kode OTP (6 digit)</label>
                <input type="text" id="otp_code" name="otp_code" maxlength="6" placeholder="Masukkan 6 digit kode OTP" required>
            </div>

            <button type="submit">Verifikasi</button>
        </form>

        <div class="resend-link">
            <form action="{{ route('resend.otp') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <button type="submit" style="background: none; color: #007bff; padding: 0; border: none; cursor: pointer; text-decoration: underline;">
                    Kirim Ulang OTP
                </button>
            </form>
        </div>
    </div>
</body>
</html>