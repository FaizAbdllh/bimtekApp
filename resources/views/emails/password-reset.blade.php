<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Kredensial Kata Sandi Baru</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc; }
        .container { background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { background: #1e3a8a; color: white; padding: 24px; text-align: center; }
        .header h1 { color: white; margin: 10px 0 5px; font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { color: #dbeafe; margin: 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 24px; background-color: #ffffff; }
        .credentials-box { background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .credentials-box h3 { margin: 0 0 12px 0; color: #0369a1; font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .credential-item { margin-bottom: 8px; font-size: 13px; }
        .credential-item:last-child { margin-bottom: 0; }
        .credential-label { font-weight: bold; color: #475569; display: inline-block; width: 120px; }
        .credential-value { font-family: 'Courier New', Courier, monospace; font-weight: bold; color: #0f172a; background: #ffffff; padding: 4px 8px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .alert-box { background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px; margin: 16px 0; font-size: 12px; color: #991b1b; }
        .info-box { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px; margin: 16px 0; font-size: 12px; color: #166534; }
        .cta-button { display: inline-block; background-color: #2563eb; color: #ffffff !important; padding: 10px 24px; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin: 10px 0; text-align: center; }
        .footer { text-align: center; padding: 20px 24px; background: #f1f5f9; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 11px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Atasan Surat Elektronik BBPMP --}}
        <div class="header">
            <div style="margin-bottom: 8px;">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" style="max-height: 40px; width: auto;">
            </div>
            <h1>Reset Kata Sandi Pengguna</h1>
            <p>Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
        </div>

        {{-- Konten Utama Notifikasi --}}
        <div class="content">
            <p style="margin-top: 0; font-size: 14px;">Yth. <strong>{{ $user->name }}</strong>,</p>
            <p style="font-size: 13px; color: #334155;">Pemberitahuan resmi sistem, kata sandi log masuk (*login password*) kepemilikan akun Anda telah diatur ulang oleh Administrator IT institusi. Berikut adalah rincian data kredensial akses masuk yang baru:</p>

            {{-- Kotak Detail Komponen Kredensial Akses --}}
            <div class="credentials-box">
                <h3>Kredensial Otorisasi Baru</h3>
                <div class="credential-item">
                    <span class="credential-label">Alamat Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-item" style="margin-top: 10px;">
                    <span class="credential-label">Password Baru:</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>

            {{-- Box Warning Mandatori Tindakan Pengamanan --}}
            <div class="alert-box">
                <strong>⚠️ Peringatan Keamanan Penting:</strong> Demi menjaga kerahasiaan hak akses data kedinasan, Anda diwajibkan untuk segera memperbarui kembali kata sandi acak di atas melalui menu **Pengaturan Profil Akun** sesaat setelah berhasil masuk ke dalam sistem.
            </div>

            {{-- Box Info Tambahan --}}
            <div class="info-box">
                <strong>ℹ️ Catatan Audit:</strong> Apabila Anda merasa tidak pernah mengajukan permohonan pengaturan ulang (*reset password*) ini, mohon untuk segera melaporkannya kepada pihak Unit Kerja Admin IT BBPMP Sumbar guna mencegah adanya klaim akses ilegal.
            </div>

            {{-- Tombol Utama CTA Navigasi Sistem --}}
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ url('/login') }}" target="_blank" rel="noopener" class="cta-button">Masuk ke Aplikasi</a>
            </div>

            {{-- Penutup Signatur Surel --}}
            <div class="footer">
                <p style="font-weight: bold; color: #475569; text-transform: uppercase; font-size: 12px;">Tim Administrator Sistem Informasi</p>
                <p style="font-weight: 600; margin-top: 2px;">Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
                <p style="margin-top: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; font-style: italic;">Surat digital ini diproduksi otomatis oleh sistem manajemen keamanan pangkalan data, mohon untuk tidak mengirimkan balasan balik.</p>
                <p style="margin-top: 4px; color: #b45309; font-weight: bold;">© {{ date('Y') }} SI Bimtek BBPMP Sumbar - Hak Cipta Dilindungi</p>
            </div>
        </div>
    </div>
</body>
</html>