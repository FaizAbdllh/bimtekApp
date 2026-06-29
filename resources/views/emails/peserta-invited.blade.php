<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Bimtek Kedinasan dan Verifikasi Dokumen Persyaratan</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc; }
        .container { background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { background: #1e3a8a; color: white; padding: 24px; text-align: center; }
        .header h1 { color: white; margin: 10px 0 5px; font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { color: #dbeafe; margin: 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 24px; background-color: #ffffff; }
        
        .bimtek-info-box { background-color: #f8fafc; border-left: 4px solid #2563eb; border-radius: 0 12px 12px 0; padding: 18px; margin: 18px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 5px 0; vertical-align: top; font-size: 13px; }
        .info-label { font-weight: bold; color: #475569; width: 120px; }
        .info-value { color: #0f172a; font-weight: 600; }
        
        .alert-warning { background-color: #fffbeb; border-left: 4px solid #d97706; border-top: 1px solid #fef3c7; border-right: 1px solid #fef3c7; border-bottom: 1px solid #fef3c7; border-radius: 0 12px 12px 0; padding: 16px; margin: 18px 0; font-size: 13px; color: #78350f; }
        .alert-success { background-color: #f0fdf4; border-left: 4px solid #16a34a; border-top: 1px solid #bbf7d0; border-right: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0; border-radius: 0 12px 12px 0; padding: 14px; margin: 18px 0; font-size: 12px; color: #14532d; }
        
        .steps-box { background: #ffffff; padding: 16px; border: 1px solid #e2e8f0; border-radius: 12px; margin: 20px 0; }
        .step-item { padding: 10px 0; border-bottom: 1px dashed #e2e8f0; font-size: 13px; color: #334155; }
        .step-item:last-child { border-bottom: none; padding-bottom: 0; }
        .step-item p { margin: 2px 0 0 18px; font-size: 12px; color: #64748b; font-weight: 500; }
        .step-item strong { color: #1e3a8a; font-weight: bold; }
        
        .btn { display: inline-block; background: #2563eb; color: white !important; padding: 11px 24px; text-decoration: none; border-radius: 8px; margin: 15px 0; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
        .footer { text-align: center; padding: 20px 24px; background: #f1f5f9; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 11px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Atasan Surat Resmi Pembuka --}}
        <div class="header">
            <div style="margin-bottom: 10px;">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" style="max-height: 45px; width: auto;">
            </div>
            <h1>Undangan Bimtek & Verifikasi Berkas</h1>
            <p>Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
        </div>

        {{-- Isi Kandungan Pesan Surel --}}
        <div class="content">
            <p style="margin-top: 0; font-size: 14px;">Yth. Bapak/Ibu <strong>{{ $peserta->name }}</strong>,</p>
            <p style="font-size: 13px; color: #334155;">Pemberitahuan resmi instansi, Anda telah masuk dalam daftar pendelegasian <strong>Calon Peserta</strong> untuk mengikuti program Bimbingan Teknis (Bimtek) peningkatan mutu kedinasan berikut:</p>

            {{-- Kartu Rincian Informasi Kelas Bimtek --}}
            <div class="bimtek-info-box">
                {{-- REFAKTORISASI: Jaring pengaman komplit nama kelas bimtek terkait --}}
                <h3 style="margin: 0 0 10px 0; color: #1e3a8a; font-size: 14px; font-weight: bold; line-height: 1.4;">
                    {{ $bimtek->judul_final ?? $bimtek->judul_rencana ?? $bimtek->judul }}
                </h3>
                
                {{-- REFAKTORISASI: Konversi flexbox ke table murni demi kestabilan rendering email --}}
                <table class="info-table">
                    <tr>
                        <td class="info-label">📅 Tanggal Mulai</td>
                        <td style="width: 10px; color: #94a3b8;">:</td>
                        <td class="info-value">{{ \Carbon\Carbon::parse($bimtek->tanggal_mulai)->isoFormat('dddd, D MMMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">📅 Tanggal Selesai</td>
                        <td style="width: 10px; color: #94a3b8;">:</td>
                        <td class="info-value">{{ \Carbon\Carbon::parse($bimtek->tanggal_selesai)->isoFormat('dddd, D MMMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">📍 Lokasi Kegiatan</td>
                        <td style="width: 10px; color: #94a3b8;">:</td>
                        {{-- REFAKTORISASI: Jaring pengaman properti lokasi penugasan --}}
                        <td class="info-value">{{ $bimtek->lokasi_aktual ?? $bimtek->lokasi_rencana ?? $bimtek->lokasi ?? 'Ditentukan Panitia' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Banner Notifikasi Mandatori Verifikasi Administrasi --}}
            <div class="alert-warning">
                <strong>⚠️ PENTING: Prasyarat Verifikasi Berkas Persyaratan</strong><br>
                <span style="display: block; margin-top: 2px; font-weight: 500; line-height: 1.4;">Sebelum diperkenankan memasuki ruang kelas dan melakukan presensi kehadiran harian, Anda diwajibkan untuk mengirimkan dokumen portofolio persyaratan kelayakan administrasi terlebih dahulu melalui tautan di bawah ini.</span>
            </div>

            {{-- Deteksi Fleksibel Penyematan Lampiran Berkas PDF Surat Undangan Kedinasan Pokja --}}
            @if($bimtek->file_surat_final_path || $bimtek->file_surat_draft_path)
                <div class="alert-success">
                    <strong>📎 Surat Undangan Resmi Terlampir:</strong><br>
                    <span style="display: block; margin-top: 2px; font-weight: 500; line-height: 1.4;">Arsip surat panggilan undangan resmi instansi telah disematkan otomatis pada lampiran surat elektronik ini dalam format PDF asli.</span>
                </div>
            @endif

            {{-- Panel Panduan Langkah Penyelesaian Tugas --}}
            <div class="steps-box">
                <h4 style="margin: 0 0 8px 0; color: #0f172a; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Alur Prosedur Pengisian Berkas:</h4>
                
                <div class="step-item">
                    <strong>1. Akses Portal Unggah Berkas</strong>
                    <p>Klik tautan tombol biru "Unggah Dokumen Syarat" di bagian bawah pesan ini.</p>
                </div>
                <div class="step-item">
                    <strong>2. Masukkan Dokumen Persyaratan Valid</strong>
                    <p>Isi lampiran berkas yang diminta, seperti Kartu Identitas (KTP) dan Surat Tugas Delegasi Resmi dari instansi/sekolah asal Anda.</p>
                </div>
                <div class="step-item">
                    <strong>3. Proses Kurasi Penilaian Pokja</strong>
                    <p>Tunggu tim penilai sekretariat melakukan pemeriksaan tingkat kelayakan kesesuaian berkas data Anda.</p>
                </div>
                <div class="step-item">
                    <strong>4. Akses Hak Fitur Terbuka</strong>
                    <p>Setelah berkas disetujui, akun Anda otomatis aktif dan dapat langsung mengunduh modul serta mengisi absensi sesi.</p>
                </div>
            </div>

            {{-- Tombol Utama CTA Navigasi Unggah Dokumen --}}
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ $uploadUrl }}" target="_blank" rel="noopener" class="btn">Unggah Dokumen Syarat</a>
            </div>

            <p style="margin-top: 25px; font-size: 12px; color: #64748b; font-style: italic; line-height: 1.5;">
                *Mohon untuk segera merampungkan proses pemenuhan dokumen persyaratan sesegera mungkin guna menjamin kelancaran validasi kelayakan keanggotaan sebelum pelaksanaan kelas dimulai.
            </p>

            {{-- Penutup Salam Kedinasan Pokja --}}
            <div class="footer" style="margin-top: 30px;">
                <p style="font-weight: bold; color: #475569; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Panitia Kerja Pelaksana Kegiatan</p>
                <p style="font-weight: 600; margin-top: 2px;">Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
                <p style="margin-top: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; font-style: italic;">Pesan notifikasi sistem ini diproduksi secara otomatis oleh pangkalan data persuratan, mohon untuk tidak mengirimkan balasan langsung ke alamat surel ini.</p>
            </div>
        </div>
    </div>
</body>
</html>