<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Reset Password - LaporPak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0; padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }
        .btn-primary {
            background-color: #0d6efd;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .footer {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <p>Halo {{ $name }},</p>
        <p>Kami menerima permintaan untuk mereset password akun Anda.</p>
        <p>Silakan klik tombol di bawah ini untuk membuat password baru. Link ini berlaku selama 10 menit.</p>

        <a href="{{ $resetLink }}" class="btn-primary" target="_blank">Reset Password</a>

        <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>

        <p class="footer">Salam hangat,<br>LaporPak Team</p>
    </div>
</body>
</html>
