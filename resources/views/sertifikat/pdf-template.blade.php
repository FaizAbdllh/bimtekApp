<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $peserta->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 30px; color: #333; background-color: #fff; }
        .cert { border: 4px double #1e3a8a; padding: 45px; text-align: center; position: relative; }
        .header { border-bottom: 3px double #1e3a8a; padding-bottom: 15px; margin-bottom: 25px; }
        .title { font-size: 38px; color: #1e3a8a; font-weight: bold; margin: 10px 0; tracking-letters: 2px; }
        .subtitle { font-size: 14px; color: #2563eb; font-weight: bold; uppercase; tracking-letters: 1px; }
        .name { font-size: 24px; color: #1e3a8a; font-weight: bold; text-decoration: underline; margin: 25px 0; uppercase; }
        .info { font-size: 13px; margin: 15px 0; line-height: 1.6; }
        .box { background: #f0f9ff; border-left: 4px solid #2563eb; padding: 18px; margin: 25px 0; text-align: left; }
        .sig { margin-top: 50px; float: right; text-align: center; width: 250px; }
        .footer { clear: both; border-top: 1px dashed #cbd5e1; padding-top: 12px; margin-top: 40px; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <div class="cert">
        {{-- KOP Kementerian / Lembaga --}}
        <div class="header">
            <div style="font-size: 11px; color: #64748b; font-weight: bold; uppercase; letter-spacing: 1px;">Balai Besar Penjaminan Mutu Pendidikan</div>
            <div style="font-size: 14px; color: #1e3a8a; font-weight: bold; uppercase; margin-top: 2px; letter-spacing: 1px;">BBPMP Provinsi Sumatera Barat</div>
            <div class="title">SERTIFIKAT</div>
            <div class="subtitle">Bimbingan Teknis (BIMTEK)</div>
            <div style="font-size: 11px; color: #475569; font-weight: font-semibold; margin-top: 5px;">Nomor: {{ $sertifikat->nomor_sertifikat }}</div>
        </div>
        
        <p style="font-size: 13px; font-style: italic; color: #475569;">Dengan ini menerangkan bahwa:</p>
        
        {{-- Nama Komplit Beserta Gelar Akademik --}}
        <div class="name">{{ strtoupper($peserta->name) }}</div>
        
        {{-- Informasi Keanggotaan --}}
        <div class="info">
            <table style="margin: 0 auto; text-align: left; font-size: 13px;">
                <tr>
                    <td style="padding-right: 10px; color: #64748b; font-weight: bold;">NIP / NIK</td>
                    <td>: {{ $peserta->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 10px; color: #64748b; font-weight: bold;">Instansi</td>
                    {{-- REFAKTORISASI: Fallback properti asal_instansi untuk form publik --}}
                    <td>: {{ $peserta->asal_instansi ?? $peserta->instansi ?? '-' }}</td>
                </tr>
            </table>
        </div>
        
        {{-- Kotak Detail Kompetensi Kelulusan Pelaksanaan --}}
        <div class="box">
            <p style="font-size: 12px; font-weight: bold; color: #0f172a; margin: 0 0 8px 0;">Telah berhasil menyelesaikan dan dinyatakan LULUS pada agenda:</p>
            {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
            <div style="font-size: 14px; font-weight: bold; color: #1e3a8a; margin-bottom: 10px;">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</div>
            
            <div style="font-size: 11px; color: #475569; line-height: 1.5;">
                {{-- REFAKTORISASI: Fallback komplit range tanggal rencana awal --}}
                <div style="margin: 3px 0;">
                    <strong>Waktu Pelaksanaan:</strong> 
                    @if($bimtek->tanggal_mulai_aktual && $bimtek->tanggal_selesai_aktual)
                        {{ $bimtek->tanggal_mulai_aktual->translatedFormat('d F Y') }} s.d. {{ $bimtek->tanggal_selesai_aktual->translatedFormat('d F Y') }}
                    @else
                        {{ $bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->translatedFormat('d F Y') : '-' }} s.d. {{ $bimtek->tanggal_selesai_rencana ? $bimtek->tanggal_selesai_rencana->translatedFormat('d F Y') : '-' }}
                    @endif
                </div>
                {{-- REFAKTORISASI: Fallback penunjuk tempat kegiatan perencanaan --}}
                <div style="margin: 3px 0;"><strong>Tempat / Lokasi:</strong> {{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</div>
            </div>
        </div>
        
        <p style="font-size: 13px; color: #334155; margin-top: 20px;">Serta yang bersangkutan telah memenuhi seluruh indikator penilaian evaluasi kelulusan program.</p>
        
        {{-- Blok Tanda Tangan Kepala Balai --}}
        <div class="sig">
            <p style="font-size: 12px; color: #334155; margin-bottom: 75px;">
                Padang, 
                @if($sertifikat->tanggal_terbit)
                    {{ $sertifikat->tanggal_terbit->translatedFormat('d F Y') }}
                @else
                    {{ now()->translatedFormat('d F Y') }}
                @endif
            </p>
            <p style="font-size: 12px; color: #1e3a8a; font-weight: bold; margin: 0;">Kepala BBPMP Provinsi Sumatera Barat</p>
        </div>
        
        {{-- Kaki Dokumen Kedinasan --}}
        <div class="footer">
            <p>Dokumen ini diterbitkan secara sah oleh sistem informasi manajemen penjaminan mutu pendidikan BBPMP Sumatera Barat.</p>
        </div>
    </div>
</body>
</html>