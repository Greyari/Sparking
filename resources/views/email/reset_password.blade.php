<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Kata Sandi Anda - SPARKING</title>
</head>
<body style="font-family: 'Arial', sans-serif; background: #f5f7fa; margin: 0; padding: 0;">
    <div style="padding: 1cm;">

        <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 30px;">
                <h2 style="color: #2d3748; margin-top: 0;">Halo, {{ $nama }}!</h2>
                <p style="color: #4a5568; line-height: 1.6;">Kami menerima permintaan untuk mengatur ulang kata sandi akun <strong style="color: #667eea;">SPARKING</strong> Anda.</p>
                <p style="color: #4a5568; line-height: 1.6;">Untuk melanjutkan, silakan klik tombol di bawah ini untuk mengganti kata sandi Anda:</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $resetUrl }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px 28px; text-decoration: none; border-radius: 50px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                        Reset Kata Sandi Sekarang
                    </a>
                </div>

                <p style="color: #718096; font-size: 14px; line-height: 1.6;">Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:<br>
                <a href="{{ $resetUrl }}" style="color: #667eea; word-break: break-word;">{{ $resetUrl }}</a></p>

                <p style="color: #718096; font-size: 14px; line-height: 1.6;">Jika Anda tidak meminta pengaturan ulang kata sandi, Anda dapat mengabaikan email ini dengan aman.</p>

                <div style="border-top: 1px solid #e2e8f0; margin-top: 30px; padding-top: 20px;">
                    <p style="color: #4a5568; margin-bottom: 5px;">Salam hangat,</p>
                    <p style="color: #667eea; font-weight: bold; margin: 0;">Tim SPARKING</p>
                </div>
            </div>
        </div>

        <div style="max-width: 600px; margin: 20px auto; text-align: center; color: #718096; font-size: 12px;">
            <p>© 2026 SPARKING</p>
        </div>
    </div>
</body>
</html>
