<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Verifikasi Dokumen</title>
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
        .content {
            background-color: #f8f9fa;
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
            color: white;
        }
        .status-verified {
            background-color: #10b981;
        }
        .status-rejected {
            background-color: #ef4444;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin: 0 0 10px;
            color: #1e40af;
        }
        .info-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .info-box li {
            margin: 5px 0;
            color: #1e3a8a;
        }
        .cta-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .cta-button:hover {
            background: #1e40af;
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
            <h1>Status Verifikasi Dokumen</h1>
            <p>Balai Bimbingan dan Pelatihan Masyarakat Provinsi Sumatera Barat</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $peserta->name ?? 'Peserta' }}</strong>,</p>
            
            @if($status === 'verified')
                <div class="status-badge status-verified">
                    ✓ DOKUMEN TERVERIFIKASI
                </div>
                <p>Selamat! Semua dokumen persyaratan Anda untuk Bimtek berikut telah diverifikasi dan disetujui:</p>
                
                <div class="info-box">
                    <h3>{{ $bimtek->judul_final ?? $bimtek->judul }}</h3>
                    <p><strong>Status:</strong> Dokumen Diterima</p>
                </div>

                <p><strong>Anda sekarang dapat mengakses seluruh fitur bimtek:</strong></p>
                <div class="info-box">
                    <ul>
                        <li>📋 Absensi</li>
                        <li>📚 Materi Pembelajaran</li>
                        <li>✍️ Tugas</li>
                        <li>🏆 Sertifikat</li>
                    </ul>
                </div>

                <p style="text-align: center;">
                    <a href="{{ route('login') }}" class="cta-button">Login ke Sistem</a>
                </p>

            @else
                <div class="status-badge status-rejected">
                    ✗ DOKUMEN DITOLAK
                </div>
                <p>Dokumen persyaratan Anda untuk Bimtek berikut memerlukan perbaikan:</p>
                
                <div class="info-box">
                    <h3>{{ $bimtek->judul_final ?? $bimtek->judul }}</h3>
                    <p><strong>Status:</strong> Memerlukan Perbaikan</p>
                    
                    @if(isset($rejectedDocuments) && $rejectedDocuments->count() > 0)
                        <p style="margin-top: 15px; margin-bottom: 10px;"><strong>Dokumen yang ditolak:</strong></p>
                        <ul>
                            @foreach($rejectedDocuments as $doc)
                                <li>{{ str_replace('_', ' ', ucwords($doc->jenis_dokumen)) }}
                                    @if(!empty($doc->catatan_verifikasi))
                                        <br><small style="color: #ef4444;">📝 {{ $doc->catatan_verifikasi }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <p><strong>Langkah selanjutnya:</strong></p>
                <ol style="margin-left: 20px; color: #1e3a8a;">
                    <li>Cek catatan verifikasi di sistem untuk detail perbaikan yang diperlukan</li>
                    <li>Perbaiki dan persiapkan dokumen sesuai catatan</li>
                    <li>Upload ulang dokumen yang sudah diperbaiki</li>
                    <li>Tim verifikasi akan memeriksa kembali dalam waktu 1x24 jam</li>
                </ol>

                <p style="text-align: center;">
                    <a href="{{ route('login') }}" class="cta-button">Upload Ulang Dokumen</a>
                </p>
            @endif

            <div class="footer">
                <p>Email ini dikirim secara otomatis oleh Sistem Informasi Bimtek BBPMP Sumbar.</p>
                <p>Jangan balas email ini. Untuk bantuan, silakan hubungi panitia atau administrator sistem.</p>
            </div>
        </div>
    </div>
</body>
</html>
