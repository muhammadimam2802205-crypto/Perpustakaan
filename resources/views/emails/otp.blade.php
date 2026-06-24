<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #007bff;
            margin: 0;
        }
        .otp-code {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 36px;
            letter-spacing: 10px;
            font-weight: bold;
            color: #007bff;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info {
            color: #6c757d;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            .otp-code {
                font-size: 28px;
                letter-spacing: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📚 Perpustakaan</h2>
            <p>Verifikasi Akun</p>
        </div>
        
        <p>Halo <strong>{{ $name }}</strong>,</p>
        
        <p>Terima kasih telah mendaftar. Gunakan kode OTP berikut untuk memverifikasi akun Anda:</p>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p>Kode OTP ini berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.</p>
        
        <div class="info">
            <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
            <p>&copy; {{ date('Y') }} Perpustakaan. All rights reserved.</p>
        </div>
    </div>
</body>
</html>