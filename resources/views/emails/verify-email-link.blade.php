<!DOCTYPE html>
<html lang="id" style="margin:0; padding:0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color:#f9fafb;">
<head>
    <meta charset="UTF-8" />
    <title>Verifikasi Email</title>
    <style>
        /* Reset */
        body, p, h1, a {
            margin: 0; padding: 0;
            text-decoration: none;
            color: #333333;
        }
        body {
            background-color: #f9fafb;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding: 40px 10px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.1);
            padding: 40px 30px;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #4a5568;
            margin-bottom: 24px;
        }
        .button {
            display: block;
            width: fit-content;
            margin: 0 auto 30px;
            background-color: #2563eb;
            color: white !important;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #1e40af;
        }
        a.url {
            word-break: break-all;
            color: #2563eb;
            font-size: 14px;
            display: block;
            text-align: center;
            margin-bottom: 16px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            margin-top: 20px;
        }
        @media only screen and (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 24px;
            }
            p {
                font-size: 15px;
            }
            .button {
                padding: 12px 24px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container" role="main" aria-label="Verifikasi Email">
        <h1>Verifikasi Email Anda</h1>
        <p>Halo,</p>
        <p>Terima kasih telah mendaftar! Silakan klik tombol di bawah untuk memverifikasi alamat email Anda dan mengaktifkan akun Anda.</p>
        <a href="{{ $url }}" class="button" target="_blank" rel="noopener">Verifikasi Email</a>
        <a href="{{ $url }}" class="url" target="_blank" rel="noopener">{{ $url }}</a>
        <p>Jika Anda tidak membuat akun ini, silakan abaikan email ini.</p>
        <div class="footer">
            &copy; {{ date('Y') }} LaporPak. Semua hak dilindungi.
        </div>
    </div>
</body>
</html>
