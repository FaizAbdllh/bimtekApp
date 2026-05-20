<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kredensial Akun</title>
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
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header-logo {
            margin-bottom: 15px;
        }
        .header-logo img {
            max-height: 50px;
        }
        .header h1 {
            color: white;
            margin: 10px 0 5px;
            font-size: 24px;
        }
        .header p {
            color: #dbeafe;
            margin: 5px 0 0;
            font-size: 14px;
        }
        .main-content {
            padding: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .credentials {
            background-color: #f0f9ff;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials h3 {
            margin: 0 0 15px;
            color: #1e40af;
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
            width: 100px;
        }
        .credential-value {
            background-color: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            font-family: monospace;
            flex: 1;
        }
        .bimtek-info {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .bimtek-info h3 {
            margin: 0 0 10px;
            color: #1e40af;
            font-size: 14px;
        }
        .bimtek-info p {
            margin: 5px 0;
            color: #1e3a8a;
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
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #1e40af;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #eab308;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            padding: 20px 30px 30px;
            background: #ffffff;
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
            <div class="header-logo">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" style="max-height: 50px;">
            </div>
            <h1>SI Bimtek BBPMP Sumbar</h1>
            <p>Sistem Informasi Bimbingan Teknis</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $user->name }}</strong>,</p>
            
            @if($bimtek)
                <p>Anda telah terdaftar sebagai peserta dalam kegiatan bimbingan teknis berikut:</p>
                
                <div class="bimtek-info">
                    <h3>Informasi Bimtek</h3>
                    <p><strong>{{ $bimtek->judul_final }}</strong></p>
                    @if($bimtek->tanggal_mulai)
                        <p>Tanggal: {{ \Carbon\Carbon::parse($bimtek->tanggal_mulai)->format('d F Y') }}
                        @if($bimtek->tanggal_selesai && $bimtek->tanggal_selesai != $bimtek->tanggal_mulai)
                            - {{ \Carbon\Carbon::parse($bimtek->tanggal_selesai)->format('d F Y') }}
                        @endif
                        </p>
                    @endif
                    @if($bimtek->lokasi)
                        <p>Lokasi: {{ $bimtek->lokasi }}</p>
                    @endif
                </div>
            @else
                <p>Akun Anda telah dibuat di SI Bimtek BBPMP Sumbar.</p>
            @endif

            <p>Berikut adalah kredensial untuk login ke sistem:</p>

            @if($bimtek && ($bimtek->file_surat_final_path || $bimtek->file_surat_draft_path))
                <div class="warning" style="background-color: #dcfce7; border-left-color: #16a34a;">
                    <p style="color: #14532d;">📎 <strong>Surat undangan resmi bimtek terlampir</strong> pada email ini dalam format PDF.</p>
                </div>
            @endif

            <div class="credentials">
                <h3>Kredensial Login</h3>
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>

            <div class="warning">
                <p>⚠️ <strong>Penting:</strong> Segera ubah password Anda setelah login pertama kali untuk keamanan akun.</p>
            </div>

            <p style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Login ke Sistem</a>
            </p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem Informasi Bimtek BBPMP Sumbar.</p>
            <p>Jangan balas email ini. Untuk bantuan, silakan hubungi administrator sistem.</p>
        </div>
    </div>
</body>
</html>
