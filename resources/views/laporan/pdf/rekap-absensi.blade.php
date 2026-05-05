<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi - {{ $bimtek->judul_final }}</title>
    <style>
        @page {
            margin: 1.5cm 1cm;
            size: landscape;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 12px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 10px;
            margin: 0;
            font-weight: normal;
            color: #555;
        }
        .title {
            text-align: center;
            margin: 15px 0;
        }
        .title h3 {
            font-size: 12px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        .title p {
            margin: 0;
            color: #666;
            font-size: 10px;
        }
        .info {
            margin-bottom: 10px;
        }
        .info span {
            margin-right: 30px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th, table.data td {
            border: 1px solid #333;
            padding: 5px 4px;
            text-align: center;
        }
        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }
        table.data td.left {
            text-align: left;
        }
        table.data td.hadir {
            background-color: #d4edda;
            color: #155724;
        }
        table.data td.alpha {
            background-color: #f8d7da;
            color: #721c24;
        }
        .legend {
            margin-top: 10px;
            font-size: 8px;
        }
        .legend span {
            margin-right: 15px;
            padding: 2px 8px;
        }
        .legend .hadir { background-color: #d4edda; }
        .legend .alpha { background-color: #f8d7da; }
        .footer {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature {
            float: right;
            width: 200px;
            text-align: center;
            font-size: 9px;
        }
        .signature-line {
            margin-top: 40px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }
        .summary {
            margin-top: 10px;
            padding: 8px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <h2>BALAI BESAR PENJAMINAN MUTU PENDIDIKAN SUMATERA BARAT</h2>
    </div>

    <div class="title">
        <h3>REKAP ABSENSI PESERTA</h3>
        <p>{{ $bimtek->judul_final }}</p>
    </div>

    <div class="info">
        <span><strong>Tanggal:</strong> {{ $bimtek->tanggal_mulai_final ? $bimtek->tanggal_mulai_final->locale('id')->isoFormat('D MMMM Y') : '-' }} - {{ $bimtek->tanggal_selesai_final ? $bimtek->tanggal_selesai_final->locale('id')->isoFormat('D MMMM Y') : '-' }}</span>
        <span><strong>Tempat:</strong> {{ $bimtek->lokasi_final ?? '-' }}</span>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 150px;">Nama Peserta</th>
                <th>Email</th>
                @foreach($sesiAbsensis as $sesi)
                <th style="width: 70px;">{{ $sesi->nama_sesi }}<br><small>{{ optional($sesi->tanggal ?? $sesi->created_at)->format('d/m') ?? '-' }}</small></th>
                @endforeach
                <th style="width: 50px;">Total Hadir</th>
                <th style="width: 40px;">%</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($rekapAbsensi as $userId => $data)
            <tr>
                <td>{{ $no++ }}</td>
                <td class="left">{{ $data['user']->name }}</td>
                <td class="left">{{ $data['user']->email }}</td>
                @foreach($sesiAbsensis as $sesi)
                @php $hadir = $data['kehadiran'][$sesi->id] ?? false; @endphp
                <td class="{{ $hadir ? 'hadir' : 'alpha' }}">{{ $hadir ? 'H' : '-' }}</td>
                @endforeach
                <td><strong>{{ $data['total_hadir'] }}/{{ $totalSesi }}</strong></td>
                <td>{{ $totalSesi > 0 ? round(($data['total_hadir'] / $totalSesi) * 100) : 0 }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 5 + $sesiAbsensis->count() }}">Tidak ada data absensi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="legend">
        <strong>Keterangan:</strong>
        <span class="hadir">H = Hadir</span>
        <span class="alpha">- = Tidak Hadir</span>
    </div>

    <div class="summary">
        <strong>Total Peserta:</strong> {{ count($rekapAbsensi) }} orang | 
        <strong>Total Sesi:</strong> {{ $totalSesi }} sesi
    </div>

    <div class="footer">
        <div class="signature">
            <p>Padang, {{ $tanggal_cetak }}</p>
            <p>Koordinator RT Bimtek</p>
            <div class="signature-line"></div>
            <p><strong>{{ $bimtek->pic ? $bimtek->pic->name : '____________________' }}</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
