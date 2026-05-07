<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Verifikasi Email PadiCare</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 24px; background: #f8fafc;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h1 style="font-size: 20px; margin: 0 0 12px; color: #14532d;">Verifikasi Email PadiCare</h1>

        <p>Halo {{ $user->nama ?? $user->username }},</p>

        <p>
            Klik tombol di bawah untuk memverifikasi email akun Anda. Setelah email terverifikasi,
            Anda dapat menggunakan fitur reset password jika suatu saat lupa password.
        </p>

        <p style="margin: 24px 0;">
            <a href="{{ $verificationUrl }}" style="display: inline-block; background: #15803d; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: bold;">
                Verifikasi Email
            </a>
        </p>

        <p style="font-size: 13px; color: #64748b;">
            Jika tombol tidak bisa diklik, buka link berikut di browser:
            <br>
            <a href="{{ $verificationUrl }}" style="color: #15803d;">{{ $verificationUrl }}</a>
        </p>

        <p style="font-size: 13px; color: #64748b;">
            Abaikan email ini jika Anda tidak meminta verifikasi email.
        </p>
    </div>
</body>
</html>
