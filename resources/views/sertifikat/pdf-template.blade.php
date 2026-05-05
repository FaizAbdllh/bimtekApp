<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $peserta->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .cert { border: 3px solid #1e3a8a; padding: 40px; text-align: center; }
        .header { border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 36px; color: #1e3a8a; font-weight: bold; margin: 10px 0; }
        .subtitle { font-size: 14px; color: #2563eb; font-weight: bold; }
        .name { font-size: 20px; color: #1e3a8a; font-weight: bold; text-decoration: underline; margin: 20px 0; }
        .info { font-size: 12px; margin: 10px 0; }
        .box { background: #f0f9ff; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0; text-align: left; }
        .sig { margin-top: 40px; }
        .footer { border-top: 1px dashed #2563eb; padding-top: 10px; margin-top: 20px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="cert">
        <div class="header">
            <div style="font-size: 12px; color: #1e3a8a; font-weight: bold;">BBPMP SUMATERA BARAT</div>
            <div class="title">SERTIFIKAT</div>
            <div class="subtitle">Bimbingan Teknis (BIMTEK)</div>
            <div style="font-size: 10px; color: #666;">Nomor: {{ $sertifikat->nomor_sertifikat }}</div>
        </div>
        
        <p style="font-size: 12px;">Dengan ini diberikan kepada</p>
        
        <div class="name">{{ strtoupper($peserta->name) }}</div>
        
        <div class="info">
            <div>NIP: {{ $peserta->nip ?? '-' }}</div>
            <div>Instansi: {{ $peserta->instansi ?? '-' }}</div>
        </div>
        
        <div class="box">
            <p style="font-size: 11px; font-weight: bold; margin-bottom: 10px;">Telah berhasil menyelesaikan Bimbingan Teknis:</p>
            <div style="font-size: 12px; font-weight: bold; color: #1e3a8a; margin-bottom: 8px;">{{ $bimtek->judul_final }}</div>
            <div style="font-size: 11px; color: #666;">
                <div style="margin: 5px 0;">Tanggal: 
                    @if($bimtek->tanggal_mulai_aktual && $bimtek->tanggal_selesai_aktual)
                        {{ $bimtek->tanggal_mulai_aktual->translatedFormat('d F Y') }} s.d. {{ $bimtek->tanggal_selesai_aktual->translatedFormat('d F Y') }}
                    @else
                        -
                    @endif
                </div>
                <div style="margin: 5px 0;">Lokasi: {{ $bimtek->lokasi_aktual ?? '-' }}</div>
            </div>
        </div>
        
        <p style="font-size: 12px;">dan telah memenuhi semua persyaratan kelulusan program.</p>
        
        <div class="sig">
            <p style="font-size: 11px; margin-bottom: 60px;">Padang, 
                @if($sertifikat->tanggal_terbit)
                    {{ $sertifikat->tanggal_terbit->translatedFormat('d F Y') }}
                @else
                    {{ now()->translatedFormat('d F Y') }}
                @endif
            </p>
            <p style="font-size: 11px; color: #1e3a8a; font-weight: bold;">Kepala BBPMP Sumatera Barat</p>
        </div>
        
        <div class="footer">
            <p>Sertifikat ini adalah bukti resmi penyelesaian program bimbingan teknis</p>
        </div>
    </div>
</body>
</html>
