<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Peserta - {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
        }
        .header img {
            height: 60px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 12px;
            margin: 0;
            font-weight: normal;
            color: #555;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            font-size: 14px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .title p {
            margin: 0;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.data th, table.data td {
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: left;
        }
        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        table.data td.center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }
        .signature small {
            color: #666;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .page-number:before {
            content: "Halaman " counter(page);
        }
    </style>
</head>
<body>
    @php
        // REFAKTORISASI NARASUMBER: Mengurai data array JSON daftar_pemateri dari database baru
        $pemateriList = '-';
        if (is_array($bimtek->daftar_pemateri) && count($bimtek->daftar_pemateri) > 0) {
            $names = [];
            foreach($bimtek->daftar_pemateri as $pm) {
                if(!empty($pm['nama'])) {
                    $names[] = $pm['nama'];
                }
            }
            if(count($names) > 0) {
                $pemateriList = implode(', ', $names);
            }
        }
    @endphp

    <div class="header">
        <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <h2>BALAI BESAR PENJAMINAN MUTU PENDIDIKAN SUMATERA BARAT</h2>
    </div>

    <div class="title">
        <h3>REKAP DAFTAR PESERTA BIMTEK</h3>
        {{-- REFAKTORISASI: Fallback judul usulan kegiatan perencanaan jika judul_final belum diisi --}}
        <p>{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Tanggal Pelaksanaan</td>
            {{-- REFAKTORISASI WAKTU: Mengalihkan dari kolom _final lama ke kolom aktual baru dengan fallback --}}
            <td>: 
                {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->locale('id')->isoFormat('D MMMM Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->locale('id')->isoFormat('D MMMM Y') : '-') }} 
                - 
                {{ $bimtek->tanggal_selesai_aktual ? $bimtek->tanggal_selesai_aktual->locale('id')->isoFormat('D MMMM Y') : ($bimtek->tanggal_selesai_rencana ? $bimtek->tanggal_selesai_rencana->locale('id')->isoFormat('D MMMM Y') : '-') }}
            </td>
        </tr>
        <tr>
            <td>Tempat / Lokasi</td>
            {{-- REFAKTORISASI LOKASI: Mengalihkan ke properti lokasi_aktual baru --}}
            <td>: {{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</td>
        </tr>
        <tr>
            <td>Narasumber</td>
            <td>: {{ $pemateriList }}</td>
        </tr>
        <tr>
            <td>Jumlah Peserta Terdaftar</td>
            <td>: {{ $peserta->count() }} orang</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Peserta</th>
                <th>NIP</th>
                <th>Email</th>
                <th>Instansi</th>
                <th>No. Telepon</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peserta as $index => $p)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td><strong>{{ $p->name }}</strong></td>
                <td class="center">{{ $p->nip ?? '-' }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->asal_instansi ?? '-' }}</td>
                <td class="center">{{ $p->no_telepon ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center">Tidak ada peserta terdaftar dalam bimbingan teknis ini</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>Total Peserta Kelas:</strong> {{ $peserta->count() }} orang
    </div>

    <div class="footer">
        <div class="signature">
            <p>Padang, {{ $tanggal_cetak ?? now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>Koordinator / PIC Bimtek</p>
            <div class="signature-line"></div>
            {{-- REFAKTORISASI SIGNATURE: Menyelaraskan output penanggung jawab kedinasan --}}
            <p><strong>{{ $bimtek->pic ? $bimtek->pic->name : ($pic->name ?? '____________________') }}</strong></p>
            <small>NIP. {{ $bimtek->pic ? ($bimtek->pic->nip ?? '-') : ($pic->nip ?? '____________________') }}</small>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>