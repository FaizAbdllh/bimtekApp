<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Kredensial Akses Akun Baru</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc; }
        .container { background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { background: #1e3a8a; color: white; padding: 24px; text-align: center; }
        .header h1 { color: white; margin: 10px 0 5px; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { color: #dbeafe; margin: 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 24px; background-color: #ffffff; }
        
        .bimtek-info-box { background-color: #f0f9ff; border-left: 4px solid #2563eb; border-radius: 0 12px 12px 0; padding: 16px; margin: 20px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .bimtek-info-box h3 { margin: 0 0 6px 0; color: #1e40af; font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .credentials-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .credentials-box h3 { margin: 0 0 14px 0; color: #475569; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .cred-table { width: 100%; border-collapse: collapse; }
        .cred-table td { padding: 5px 0; vertical-align: middle; font-size: 13px; }
        .cred-label { font-weight: bold; color: #475569; width: 110px; }
        .cred-value { font-family: 'Courier New', Courier, monospace; font-weight: bold; color: #0f172a; background: #ffffff; padding: 4px 10px; border-radius: 6px; border: 1px solid #cbd5e1; }
        
        .alert-success { background-color: #f0fdf4; border-left: 4px solid #16a34a; border-top: 1px solid #bbf7d0; border-right: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0; border-radius: 0 8px 8px 0; padding: 14px; margin: 16px 0; font-size: 12px; color: #14532d; }
        .alert-warning { background-color: #fffbeb; border-left: 4px solid #d97706; border-top: 1px solid #fef3c7; border-right: 1px solid #fef3c7; border-bottom: 1px solid #fef3c7; border-radius: 0 8px 8px 0; padding: 14px; margin: 16px 0; font-size: 12px; color: #78350f; }
        
        .button { display: inline-block; background-color: #2563eb; color: #ffffff !important; padding: 11px 24px; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 10px; text-align: center; }
        .footer { text-align: center; padding: 20px 24px; background: #f1f5f9; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 11px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Atasan Penanda Surat Resmi Balai BBPMP --}}
        <div class="header">
            <div style="margin-bottom: 10px;">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" style="max-height: 45px; width: auto;">
            </div>
            <h1>Aktivasi Akun Sistem Informasi</h1>
            <p>Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
        </div>

        {{-- Isian Konten Surat Elektronik --}}
        <div class="content">
            <p style="margin-top: 0; font-size: 14px;">Yth. <strong>{{ $user->name }}</strong>,</p>
            
            {{-- Kondisi Percabangan A: Jika Akun Dibuat Berdasarkan Pendaftaran Kelas Bimtek --}}
            @if($bimtek)
                <p style="font-size: 13px; color: #334155;">Selamat, data diri Anda telah terdaftar secara resmi sebagai salah satu peserta dalam pelaksanaan agenda Kegiatan Bimbingan Teknis (Bimtek) berikut:</p>
                
                <div class="bimtek-info-box">
                    <h3>Informasi Pelaksanaan Agenda</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; font-weight: bold; color: #1e3a8a;">
                        {{ $bimtek->judul_final ?? $bimtek->judul_rencana ?? $bimtek->judul }}
                    </p>
                    
                    @if($bimtek->tanggal_mulai)
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #475569; font-weight: 600;">
                            📅 Tanggal: {{ \Carbon\Carbon::parse($bimtek->tanggal_mulai)->isoFormat('D MMMM YYYY') }}
                            @if($bimtek->tanggal_selesai && $bimtek->tanggal_selesai != $bimtek->tanggal_mulai)
                                s.d. {{ \Carbon\Carbon::parse($bimtek->tanggal_selesai)->isoFormat('D MMMM YYYY') }}
                            @endif
                        </p>
                    @endif
                    
                    @if($bimtek->lokasi_aktual || $bimtek->lokasi)
                        <p style="margin: 2px 0 0 0; font-size: 12px; color: #475569; font-weight: 600;">
                            📍 Lokasi: {{ $bimtek->lokasi_aktual ?? $bimtek->lokasi }}
                        </p>
                    @endif
                </div>

                {{-- Deteksi Fleksibel Penyematan Surat Undangan Berformat PDF --}}
                @if($bimtek->file_surat_final_path || $bimtek->file_surat_draft_path)
                    <div class="alert-success">
                        <strong>📎 Surat Undangan Terlampir:</strong><br>
                        <span style="display: block; margin-top: 2px; font-weight: 500; line-height: 1.4;">Surat pendelegasian resmi undangan peserta dari Pokja terkait telah disematkan otomatis pada berkas lampiran email ini.</span>
                    </div>
                @endif

            {{-- Kondisi Percabangan B: Jika Akun Dibuat Secara Manual Tanpa Relasi Kelas Terikat --}}
            @else
                <p style="font-size: 13px; color: #334155;">Akun operator/pengguna Anda telah sukses diterbitkan ke dalam pangkalan data Sistem Informasi Bimbingan Teknis BBPMP Provinsi Sumatera Barat.</p>
            @endif

            <p style="font-size: 13px; color: #334155;">Berikut adalah data kredensial akses masuk otentikasi akun personal Anda:</p>

            {{-- Kotak Detail Data Kredensial Akses Pengguna (Refaktorisasi Table Layout) --}}
            <div class="credentials-box">
                <h3>Kredensial Autentikasi Masuk</h3>
                <table class="cred-table">
                    <tr>
                        <td class="cred-label">Username (Email)</td>
                        <td style="width: 10px; color: #94a3b8;">:</td>
                        <td><span class="cred-value">{{ $user->email }}</span></td>
                    </tr>
                    <tr>
                        <td class="cred-label" style="padding-top: 14px;">Password Acak</td>
                        <td style="width: 10px; color: #94a3b8; padding-top: 14px;">:</td>
                        <td style="padding-top: 14px;"><span class="cred-value">{{ $password }}</span></td>
                    </tr>
                </table>
            </div>

            {{-- Kotak Peringatan Penting Penjaga Sandi --}}
            <div class="alert-warning">
                <strong>⚠️ Mandatori Tindakan Pengamanan:</strong> Demi menjaga kerahasiaan integritas data kepesertaan, Anda diwajibkan untuk segera memperbarui kata sandi bawaan di atas melalui menu <strong>Pengaturan Profil Akun</strong> sesaat setelah berhasil masuk ke dalam aplikasi untuk pertama kali.
            </div>

            {{-- Tombol Utama Panggil Aksi Menuju Ruang Login --}}
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ url('/login') }}" target="_blank" rel="noopener" class="button">Masuk ke Aplikasi</a>
            </div>
        </div>

        {{-- Signatur Kaki Dokumen Persuratan Elektronik Balai --}}
        <div class="footer">
            <p style="font-weight: bold; color: #475569; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Panitia Pelaksana Kegiatan Pokja</p>
            <p style="font-weight: 600; margin-top: 2px;">Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
            <p style="margin-top: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; font-style: italic;">Pesan ini diproduksi otomatis oleh komputer pangkalan data keamanan, mohon untuk tidak mengirimkan pesan balasan langsung.</p>
        </div>
    </div>
</body>
</html>