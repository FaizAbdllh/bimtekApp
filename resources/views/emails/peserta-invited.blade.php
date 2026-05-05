<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Bimtek dengan Verifikasi Dokumen</title>
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
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .bimtek-info {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .info-row {
            display: flex;
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #1e40af;
            min-width: 120px;
        }
        .info-value {
            color: #1e3a8a;
        }
        .alert {
            background: #fef08a;
            border-left: 4px solid #eab308;
            color: #713f12;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .btn:hover {
            background: #1e40af;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px 30px 30px;
            background: #ffffff;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 12px;
        }
        .steps {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .step {
            padding: 10px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        .step:last-child {
            border-bottom: none;
        }
        .step strong {
            color: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" style="max-height: 50px;">
            </div>
            <h1 style="margin: 10px 0 5px; color: white;">Undangan Bimtek + Verifikasi Dokumen</h1>
            <p style="color: #dbeafe; margin: 5px 0 0;">Balai Bimbingan dan Pelatihan Masyarakat Provinsi Sumatera Barat</p>
        </div>

        <div class="content" style="background: #f8f9fa; padding: 30px;">
        <p>Halo, <strong>{{ $peserta->name }}</strong>!</p>

        <p>Anda telah ditambahkan sebagai <strong>peserta</strong> pada kegiatan Bimbingan Teknis (Bimtek) berikut:</p>

        <div class="bimtek-info">
            <h3 style="margin-top: 0; color: #667eea;">{{ $bimtek->judul_final }}</h3>
            
            <div class="info-row">
                <span class="info-label">📅 Tanggal Mulai:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($bimtek->tanggal_mulai)->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">📅 Tanggal Selesai:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($bimtek->tanggal_selesai)->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">📍 Lokasi:</span>
                <span class="info-value">{{ $bimtek->lokasi_aktual }}</span>
            </div>
        </div>

        <div class="alert">
            <strong>⚠️ PENTING: Verifikasi Dokumen Diperlukan</strong><br>
            Bimtek ini memerlukan verifikasi dokumen sebelum Anda dapat mengikuti kegiatan. Silakan upload dokumen yang diperlukan melalui link di bawah ini.
        </div>

        @if($bimtek->file_surat_undangan_path)
        <div class="alert" style="background: #dcfce7; border-left-color: #16a34a; color: #14532d;">
            <strong>📎 Surat Undangan Terlampir</strong><br>
            Surat undangan resmi bimtek terlampir pada email ini dalam format PDF (satu file per bimtek).
        </div>
        @endif

        <div class="steps">
            <h4 style="margin-top: 0; color: #667eea;">Langkah-Langkah:</h4>
            <div class="step">
                <strong>1.</strong> Klik tombol "Upload Dokumen" di bawah
            </div>
            <div class="step">
                <strong>2.</strong> Upload dokumen yang diminta (KTP, Surat Tugas, dll)
            </div>
            <div class="step">
                <strong>3.</strong> Tunggu verifikasi dari panitia
            </div>
            <div class="step">
                <strong>4.</strong> Setelah disetujui, Anda dapat mengikuti bimtek
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $uploadUrl }}" class="btn">Upload Dokumen Sekarang</a>
        </div>

        <p style="margin-top: 30px; color: #555;">
            <em>Pastikan untuk mengupload dokumen sesegera mungkin agar verifikasi dapat diproses sebelum pelaksanaan bimtek.</em>
        </p>

        <p>Jika Anda memiliki pertanyaan, silakan hubungi panitia atau PIC yang bertanggung jawab.</p>

        <p style="margin-top: 20px;">
            Salam,<br>
            <strong>Tim BBPMP Sumatera Barat</strong>
        </p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis oleh Sistem Informasi Bimtek BBPMP Sumbar.</p>
        <p>Jangan balas email ini. Untuk bantuan, silakan hubungi administrator sistem.</p>
    </div>
</body>
</html>
