<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Login</title>
</head>
<body>
    <h2>Kode OTP Login</h2>
    <p>Halo,</p>
    <p>Anda melakukan percobaan login ke aplikasi kami. Gunakan kode OTP berikut untuk menyelesaikan login:</p>
    
    <h1 style="background: #f4f4f4; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 10px;">
        <?php echo $otpCode; ?>
    </h1>
    
    <p>Kode ini berlaku selama 10 menit.</p>
    <p>Email: <?php echo $email; ?></p>
    <p>Jika Anda tidak melakukan percobaan login, abaikan email ini.</p>
</body>
</html>