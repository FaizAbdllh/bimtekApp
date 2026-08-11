<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Aktivasi Akun Pelatihan Bimtek</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f8fafc; }
        .wrapper { width: 100%; padding: 20px 0; background-color: #f8fafc; }
        .card { max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { background-color: #1e3a8a; padding: 24px; text-align: center; color: #ffffff; }
        .content { padding: 24px; }
        .button-container { text-align: center; margin: 24px 0; }
        .btn { display: inline-block; padding: 11px 24px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; font-weight: bold; border-radius: 8px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .token-info { background-color: #f0f9ff; border-left: 4px solid #2563eb; padding: 12px; margin: 20px 0; font-size: 12px; color: #1e3a8a; border-radius: 0 8px 8px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            
            {{-- Bagian Atasan Email Branding BBPMP --}}
            <div class="header">
                <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.85;">Balai Besar Penjaminan Mutu Pendidikan</div>
                <div style="font-size: 15px; font-weight: bold; text-transform: uppercase; margin-top: 2px; letter-spacing: 1px;">BBPMP Provinsi Sumatera Barat</div>
            </div>

            {{-- Isi Pesan Surel --}}
            <div class="content">
                <p style="margin-top: 0; font-size: 14px; color: #0f172a;">Yth. <strong>{{ $user->name }}</strong>,</p>
                <p style="font-size: 14px; color: #334155; user-select: none;">Anda telah diundang secara resmi oleh pihak lembaga panitia kerja untuk mengikuti program bimbingan teknis peningkatan mutu pendidikan berikut:</p>
                
                {{-- Detail Box Nama Kelas Bimtek Terkait --}}
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 14px; border-radius: 8px; margin: 16px 0;">
                    <span style="display: block; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; margin-bottom: 2px;">Nama Agenda Kegiatan:</span>
                    {{-- REFAKTORISASI: Implementasi jaring pengaman fallback property --}}
                    <span style="font-size: 14px; font-weight: bold; color: #1e3a8a;">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span>
                </div>

                <p style="font-size: 14px; color: #334155;">Mohon untuk merampungkan proses aktivasi akun kepesertaan mandiri Anda dengan mengklik tombol otorisasi di bawah ini guna menetapkan kata sandi login personal:</p>
                
                {{-- Tombol Utama CTA --}}
                <div class="button-container">
                    <a href="{{ url('/activate/'.$token) }}" target="_blank" rel="noopener" class="btn">Aktifkan Akun Saya</a>
                </div>

                {{-- Tautan Cadangan (Jika Tombol Clipped/Block) --}}
                <p style="font-size: 12px; color: #64748b; line-height: 1.5; word-break: break-all;">
                    Jika tombol di atas mengalami kendala akses, Anda dapat menyalin rute URL tautan cadangan berikut langsung menuju bilah alamat browser Anda:<br>
                    <a href="{{ url('/activate/'.$token) }}" target="_blank" rel="noopener" style="color: #2563eb; text-decoration: underline;">{{ url('/activate/'.$token) }}</a>
                </p>

                {{-- Kotak Peringatan Tingkat Keamanan Token --}}
                <div class="token-info">
                    <strong>Pemberitahuan Sistem:</strong> Kode token otorisasi aktivasi ini bersifat rahasia, hanya dapat dipergunakan sebanyak 1 (satu) kali transaksi konfirmasi, dan memiliki masa kedaluwarsa otomatis selama 7 hari kalender terhitung sejak email ini diproduksi.
                </div>

                <p style="font-size: 13px; color: #64748b; margin-bottom: 0; font-style: italic;">
                    *Apabila Anda merasa tidak pernah mengikuti atau mendaftarkan diri pada program kegiatan kedinasan ini, harap mengabaikan pesan surat elektronik otomatis ini.
                </p>
            </div>

            {{-- Kaki Email / Signatur Formal Lembaga --}}
            <div class="footer">
                <p style="margin: 0; font-weight: bold; color: #475569; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Panitia Pelaksana Kegiatan Bimtek</p>
                <p style="margin: 3px 0 0 0; font-size: 11px; font-weight: font-semibold;">Balai Besar Penjaminan Mutu Pendidikan (BBPMP) Provinsi Sumatera Barat</p>
                <p style="margin: 12px 0 0 0; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; font-style: italic;">Pesan ini dikirimkan secara otomatis oleh sistem aplikasi manajemen internal, mohon untuk tidak membalas surel ini.</p>
            </div>
        </div>
    </div>
</body>
</html>