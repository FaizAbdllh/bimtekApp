<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Penambahan Peserta Bimtek</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc; }
        .container { background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { background: #1e3a8a; color: white; padding: 24px; text-align: center; }
        .header h1 { color: white; margin: 10px 0 5px; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { color: #dbeafe; margin: 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 24px; background-color: #ffffff; }
        .bimtek-info-box { background: #f8fafc; padding: 20px; border-left: 4px solid #2563eb; border-radius: 0 12px 12px 0; margin: 20px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .info-table td { padding: 6px 0; vertical-align: top; font-size: 13px; }
        .info-label { font-weight: bold; color: #475569; width: 130px; }
        .info-value { color: #0f172a; font-weight: 600; }
        .btn { display: inline-block; background: #2563eb; color: white !important; padding: 11px 24px; text-decoration: none; border-radius: 8px; margin: 15px 0; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
        .attachment-box { background: #f0fdf4; border-left: 4px solid #16a34a; color: #14532d; padding: 14px; border-radius: 0 8px 8px 0; margin: 20px 0; font-size: 12px; border-top: 1px solid #bbf7d0; border-right: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0; }
        .footer { text-align: center; padding: 20px 24px; background: #f1f5f9; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 11px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Atasan Surat Resmi BBPMP --}}
        <div class="header">
            <div style="margin-bottom: 10px;">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" style="max-height: 45px; width: auto;">
            </div>
            <h1>Notifikasi Kepesertaan Bimtek</h1>
            <p>Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
        </div>

        {{-- Bodi Isi Surel Pemberitahuan --}}
        <div class="content">
            <p style="margin-top: 0; font-size: 14px;">Halo, <strong>{{ $peserta->name }}</strong>!</p>
            <p style="font-size: 13px; color: #334155;">Pemberitahuan resmi sistem, Anda telah sukses ditambahkan sebagai <strong>Peserta Terdaftar</strong> dalam pelaksanaan agenda kegiatan Bimbingan Teknis (Bimtek) internal berikut ini:</p>

            {{-- Kartu Rincian Metadata Informasi Jadwal Pelaksanaan Bimtek --}}
            <div class="bimtek-info-box">
                {{-- REFAKTORISASI: Jaring pengaman properti nama agenda kegiatan --}}
                <h3 style="margin: 0 0 10px 0; color: #1e3a8a; font-size: 15px; font-weight: bold; leading-relaxed;">
                    {{ $bimtek->judul_final ?? $bimtek->judul_rencana ?? $bimtek->judul }}
                </h3>
                
                {{-- REFAKTORISASI: Konversi dari Flexbox ke Table Element demi Rendering Outlook Client --}}
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
                    @if($bimtek->deskripsi)
                        <tr>
                            <td class="info-label" style="padding-top: 10px;">📝 Catatan/Deskripsi</td>
                            <td style="width: 10px; color: #94a3b8; padding-top: 10px;">:</td>
                            <td style="color: #475569; font-weight: 500; padding-top: 10px; font-size: 12px; line-height: 1.5;">{{ $bimtek->deskripsi }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <p style="font-size: 13px; color: #334155;">Silakan masuk ke dalam platform sistem untuk meninjau rincian jadwal berkala harian sesi, mengunduh lembar modul materi ajar, serta menyelesaikan instruksi pengumpulan tugas kerja mandiri:</p>

            {{-- Deteksi Fleksibel Penyematan Lampiran Surat Undangan Kedinasan --}}
            @if($bimtek->file_surat_final_path || $bimtek->file_surat_draft_path)
                <div class="attachment-box">
                    <strong>📎 Dokumen Surat Undangan Terlampir:</strong><br>
                    <span style="font-weight: 500; margin-top: 2px; display: block; line-height: 1.4;">Surat undangan kedinasan resmi untuk permohonan dispensasi kegiatan Anda telah disematkan otomatis pada lampiran surel ini dalam format dokumen PDF.</span>
                </div>
            @endif

            {{-- Blok Tombol Panggil Aksi Akses Masuk --}}
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ $loginUrl }}" target="_blank" rel="noopener" class="btn">Login ke Sistem</a>
            </div>

            <p style="margin-top: 25px; font-size: 12px; color: #475569; font-style: italic; leading-relaxed;">
                *Mohon untuk mempersiapkan diri dan hadir tepat waktu mengikuti seluruh rangkaian agenda sesi kelas sesuai jadwal pendelegasian Pokja. Jika terdapat kendala substansial, silakan hubungi narahubung PIC pelaksana kerja yang tertera pada surat undangan.
            </p>

            {{-- Penutup Salam Kedinasan --}}
            <div class="footer" style="margin-top: 30px;">
                <p style="font-weight: bold; color: #475569; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Panitia Pelaksana Kegiatan Bimtek</p>
                <p style="font-weight: 600; margin-top: 2px;">Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
                <p style="margin-top: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; font-style: italic;">Email konfirmasi registrasi ini diproduksi otomatis oleh sistem manajemen pangkalan data kelas, mohon untuk tidak membalas surel ini.</p>
            </div>
        </div>
    </div>
</body>
</html>