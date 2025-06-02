<!DOCTYPE html>
<html lang="id" style="margin:0; padding:0; background-color:#f0f2f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Anda</title>
    <style>
        /* Reset */
        body, p, h1, a, table, td, th {
            margin: 0; padding: 0;
            text-decoration: none;
            color: #333333;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            box-sizing: border-box;
        }
        body {
            background-color: #f0f2f5; /* Light gray background for the whole page */
            padding: 20px;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff; /* White background for the content area */
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            overflow: hidden; /* Ensures border-radius applies to children like header */
        }
        .email-header {
            background-color: #ffffff; /* White header */
            padding: 30px 30px 20px; /* More padding at the top */
            text-align: center; /* Center logo */
        }
        .email-header img {
            max-width: 150px; /* Adjust logo size as needed */
            height: auto;
        }
        .email-body {
            padding: 20px 30px 30px; /* Consistent padding */
            color: #374151; /* Darker gray for text */
            font-size: 16px;
        }
        .email-body h1 {
            font-size: 22px; /* Slightly smaller H1 */
            font-weight: 600;
            color: #1f2937; /* Even darker for headings */
            margin-bottom: 20px;
            text-align: left; /* Align heading to the left as per image */
        }
        .email-body p {
            margin-bottom: 18px;
            color: #4b5563; /* Slightly lighter text color */
        }
        .button-container {
            text-align: left; /* Align button to the left */
            margin-bottom: 25px;
            margin-top: 10px;
        }
        .button {
            display: inline-block; /* Changed from block to inline-block */
            background-color: #0D6EFD; /* Blue button color from image */
            color: #ffffff !important; /* White text for button */
            font-weight: 500;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        .button:hover {
            background-color: #0a58ca; /* Darker blue on hover */
        }
        .link-info {
            font-size: 12px;
            color: #6b7280; /* Lighter gray for less important text */
            margin-top: 30px;
            line-height: 1.5;
        }
        .link-info a {
            color: #0D6EFD; /* Blue for the link */
            word-break: break-all;
            text-decoration: underline;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af; /* Even lighter gray for footer */
            padding: 20px 30px;
            background-color: #f9fafb; /* Slightly off-white for footer background */
            border-top: 1px solid #e5e7eb; /* Light border */
        }

        /* Responsive adjustments */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-header, .email-body, .email-footer {
                padding: 20px;
            }
            .email-body h1 {
                font-size: 20px;
            }
            .email-body p {
                font-size: 15px;
            }
            .button {
                padding: 12px 20px;
                font-size: 15px;
                width: 100%; /* Make button full width on small screens */
                text-align: center;
            }
            .button-container{
                text-align: center; /* Center button on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <img src="https://laporpak.com/images/logo.svg" alt="LaporPak Logo">
        </div>
        <div class="email-body">
            <h1>Halo {{ $name ?? 'Pengguna' }},</h1>
            <p>Tekan tombol dibawah ini untuk verifikasi email anda.</p>
            <div class="button-container">
                <a href="{{ $url }}" class="button" target="_blank" rel="noopener">Verifikasi Alamat Email</a>
            </div>
            <p>Jika Anda tidak membuat akun, tidak ada tindakan lebih lanjut yang diperlukan.</p>
            <p>Salam,<br>Tim LaporPak.com</p>

            <div class="link-info">
                Jika Anda kesulitan mengklik tombol "Verifikasi Alamat Email", salin dan tempel URL di bawah ini ke browser web Anda:
                <br>
                <a href="{{ $url }}" target="_blank" rel="noopener">{{ $url }}</a>
            </div>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} LaporPak.com. Semua hak dilindungi.
        </div>
    </div>
</body>
</html>
