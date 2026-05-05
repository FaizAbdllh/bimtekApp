<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Baru</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0;
            font-size: 14px;
        }
        .content {
            margin-bottom: 20px;
        }
        .credentials {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials h3 {
            margin: 0 0 15px;
            color: #0369a1;
            font-size: 16px;
        }
        .credential-item {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        .credential-label {
            font-weight: 600;
            color: #374151;
            width: 120px;
        }
        .credential-value {
            background-color: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            font-family: monospace;
            flex: 1;
        }
        .warning {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning p {
            margin: 0;
            color: #991b1b;
            font-size: 14px;
        }
        .info {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .info p {
            margin: 0;
            color: #1e40af;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            margin-top: 20px;
        }
        .footer p {
            color: #9ca3af;
            font-size: 12px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SI Bimtek BBPMP Sumbar</h1>
            <p>Reset Password</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $user->name }}</strong>,</p>
            
            <p>Password akun Anda telah direset oleh Administrator. Berikut adalah kredensial baru untuk login:</p>

            <div class="credentials">
                <h3>Kredensial Login Baru</h3>
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password Baru:</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>

            <div class="warning">
                <p>⚠️ <strong>Penting:</strong> Segera ubah password Anda setelah login untuk keamanan akun.</p>
            </div>

            <div class="info">
                <p>ℹ️ Jika Anda tidak meminta reset password, silakan hubungi Administrator segera.</p>
            </div>

            <p style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Login ke Sistem</a>
            </p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem.</p>
            <p>BBPMP Provinsi Sumatera Barat</p>
            <p>© {{ date('Y') }} SI Bimtek BBPMP Sumbar</p>
        </div>
    </div>
</body>
</html>
