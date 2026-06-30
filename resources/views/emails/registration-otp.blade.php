<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email - OTP</title>
</head>
<body>
    <h2>Verifikasi Email Anda</h2>
    <p>Halo,</p>
    <p>Anda telah melakukan pendaftaran di aplikasi kami. Untuk memverifikasi email Anda, gunakan kode OTP berikut:</p>
    
    <h1 style="background: #f4f4f4; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 10px;">
        <?php echo $otpCode; ?>
    </h1>
    
    <p>Kode ini berlaku selama 10 menit.</p>
    <p>Email: <?php echo $email; ?></p>
    <p>Jika Anda tidak melakukan pendaftaran, abaikan email ini.</p>
</body>
</html>