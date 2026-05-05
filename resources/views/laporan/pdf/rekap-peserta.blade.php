<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Peserta - {{ $bimtek->judul_final }}</title>
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
    <div class="header">
        <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <h2>BALAI BESAR PENJAMINAN MUTU PENDIDIKAN SUMATERA BARAT</h2>
    </div>

    <div class="title">
        <h3>REKAP DAFTAR PESERTA</h3>
        <p>{{ $bimtek->judul_final }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Tanggal Pelaksanaan</td>
            <td>: {{ $bimtek->tanggal_mulai_final ? $bimtek->tanggal_mulai_final->locale('id')->isoFormat('D MMMM Y') : '-' }} - {{ $bimtek->tanggal_selesai_final ? $bimtek->tanggal_selesai_final->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>: {{ $bimtek->lokasi_final ?? '-' }}</td>
        </tr>
        <tr>
            <td>Narasumber</td>
            <td>: {{ is_array($bimtek->narasumber ?? null)
                ? ($bimtek->narasumber['name'] ?? $bimtek->narasumber['nama'] ?? '-')
                : (($bimtek->narasumber->name ?? $bimtek->narasumber->nama ?? $bimtek->narasumber ?? '-') ?? '-') }}</td>
        </tr>
        <tr>
            <td>Jumlah Peserta</td>
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
                <td>{{ $p->name }}</td>
                <td class="center">{{ $p->nip ?? '-' }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->asal_instansi ?? '-' }}</td>
                <td class="center">{{ $p->no_telepon ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center">Tidak ada peserta terdaftar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>Total Peserta:</strong> {{ $peserta->count() }} orang
    </div>

    <div class="footer">
        <div class="signature">
            <p>Padang, {{ $tanggal_cetak }}</p>
            <p>Koordinator RT Bimtek</p>
            <div class="signature-line"></div>
            <p><strong>{{ $pic ? $pic->name : '____________________' }}</strong></p>
            <small>NIP. {{ $pic ? $pic->nip ?? '-' : '____________________' }}</small>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
