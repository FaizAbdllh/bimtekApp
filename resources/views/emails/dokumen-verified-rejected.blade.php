<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Verifikasi Dokumen Persyaratan</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc; }
        .container { background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { background: #1e3a8a; color: white; padding: 24px; text-align: center; }
        .header h1 { color: white; margin: 10px 0 5px; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { color: #dbeafe; margin: 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 24px; background-color: #ffffff; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 8px; margin: 15px 0; font-size: 12px; font-weight: bold; color: white; tracking-letters: 0.5px; }
        .status-verified { background-color: #10b981; }
        .status-rejected { background-color: #ef4444; }
        .info-box { background: #f8fafc; padding: 16px; border-left: 4px solid #2563eb; border-radius: 0 8px 8px 0; margin: 16px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .info-box h3 { margin: 0 0 6px; color: #1e3a8a; font-size: 14px; font-weight: bold; }
        .info-box p { margin: 0; font-size: 12px; color: #475569; font-weight: 600; }
        .info-box ul, .content ol { margin: 8px 0; padding-left: 20px; font-size: 13px; color: #334155; }
        .info-box li, .content ol li { margin: 4px 0; }
        .cta-button { display: inline-block; background: #2563eb; color: white !important; padding: 10px 24px; text-decoration: none; border-radius: 8px; margin: 15px 0; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
        .footer { text-align: center; padding: 20px 24px; background: #f1f5f9; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 11px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Kertas Branding Lembaga --}}
        <div class="header">
            <div style="margin-bottom: 10px;">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" style="max-height: 45px; width: auto;">
            </div>
            <h1>Status Hasil Verifikasi Berkas</h1>
            <p>Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
        </div>

        {{-- Bodi Surat Pemberitahuan --}}
        <div class="content">
            <p style="margin-top: 0; font-size: 14px;">Yth. <strong>{{ $peserta->name ?? 'Peserta' }}</strong>,</p>
            
            {{-- KONDISI A: BERKAS DINYATAKAN LULUS/VERIFIED --}}
            @if($status === 'verified')
                <div class="status-badge status-verified">
                    ✓ DOKUMEN DISETUJUI
                </div>
                <p style="font-size: 13px; color: #334155;">Selamat, proses pemeriksaan dokumen persyaratan pendaftaran Anda untuk kegiatan bimbingan teknis di bawah ini dinyatakan <strong>Lengkap dan Memenuhi Syarat</strong>:</p>
                
                <div class="info-box">
                    {{-- REFAKTORISASI: Kestabilan properti fallback nama kelas bimtek --}}
                    <h3>{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</h3>
                    <p>Status Kelayakan: <span class="text-green-600">Dokumen Diverifikasi & Disetujui</span></p>
                </div>

                <p style="font-size: 13px; color: #334155; margin-bottom: 5px;"><strong>Anda kini memegang hak akses penuh untuk memanfaatkan fitur ruang digital kelas:</strong></p>
                <div class="info-box" style="border-left-color: #10b981; background-color: #f0fdf4;">
                    <ul style="list-style-type: none; padding-left: 5px; margin: 0; font-weight: 600; font-size: 12px; color: #166534; line-height: 1.8;">
                        <li>✓ Pengisian Presensi Absensi Harian Sesi</li>
                        <li>✓ Unduh Modul & Bahan Materi Pembelajaran</li>
                        <li>✓ Lembar Kerja Pengumpulan Tugas Jawaban</li>
                        <li>✓ Lembar Unduh Sertifikat Kelulusan Resmi</li>
                    </ul>
                </div>

                <div style="text-align: center; margin: 20px 0;">
                    <a href="{{ route('login') }}" target="_blank" rel="noopener" class="cta-button">Masuk ke Ruang Kelas</a>
                </div>

            {{-- KONDISI B: BERKAS DINYATAKAN REJECTED / PERLU KOREKSI --}}
            @else
                <div class="status-badge status-rejected">
                    ✗ DOKUMEN MEMERLUKAN PERBAIKAN
                </div>
                <p style="font-size: 13px; color: #334155;">Mohon perhatian, berdasarkan hasil kurasi pemeriksaan oleh tim penilai, dokumen persyaratan administrasi Anda untuk kegiatan berikut dinyatakan **Belum Memenuhi Syarat**:</p>
                
                <div class="info-box" style="border-left-color: #ef4444;">
                    {{-- REFAKTORISASI: Kestabilan properti fallback nama kelas bimtek --}}
                    <h3>{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</h3>
                    <p>Status Kelayakan: <span class="text-red-600">Perlu Perbaikan / Upload Ulang</span></p>
                    
                    @if(isset($rejectedDocuments) && $rejectedDocuments->count() > 0)
                        <p style="margin-top: 12px; margin-bottom: 6px; font-weight: bold; color: #1e293b;">Daftar komponen berkas yang ditolak:</p>
                        <ul style="padding-left: 18px; margin: 0;">
                            @foreach($rejectedDocuments as $doc)
                                <li style="margin-bottom: 8px; font-size: 12px; font-weight: 600; color: #1e293b;">
                                    {{ str_replace('_', ' ', ucwords($doc->jenis_dokumen)) }}
                                    @if(!empty($doc->catatan_verifikasi))
                                        <div style="margin-top: 2px; font-size: 11px; color: #ef4444; font-weight: 500; font-style: italic; background: #fff; padding: 4px 8px; border-radius: 4px; border: 1px solid #fee2e2;">
                                            📝 Catatan Koreksi: "{{ $doc->catatan_verifikasi }}"
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <p style="font-size: 13px; color: #334155; margin-bottom: 5px;"><strong>Langkah-langkah perbaikan yang harus Anda lakukan:</strong></p>
                <ol>
                    <li>Persiapkan lembar dokumen pengganti yang valid sesuai dengan catatan koreksi tim penilai di atas.</li>
                    <li>Masuk ke dalam platform menggunakan akun Anda, lalu menuju menu verifikasi dokumen.</li>
                    <li>Lakukan unggah ulang (*upload*) pada kolom komponen dokumen persyaratan terkait.</li>
                    <li>Tim pelaksana Pokja akan melakukan peninjauan ulang berkas dalam waktu maksimal 1x24 jam kerja.</li>
                </ol>

                <div style="text-align: center; margin: 20px 0;">
                    <a href="{{ route('login') }}" target="_blank" rel="noopener" class="cta-button" style="background: #ef4444;">Unggah Ulang Dokumen</a>
                </div>
            @endif

            {{-- Footer Tanda Tangan / Informasi Otomatisasi --}}
            <div class="footer">
                <p style="font-weight: bold; color: #475569; text-transform: uppercase;">Panitia Pelaksana Kegiatan Bimtek</p>
                <p style="font-weight: 600; margin-top: 2px;">Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
                <p style="margin-top: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; font-style: italic;">Surat pemberitahuan ini diproduksi secara otomatis oleh sistem informasi manajemen, mohon untuk tidak membalas pesan email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>