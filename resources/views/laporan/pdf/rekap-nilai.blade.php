<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai - {{ $bimtek->judul_final }}</title>
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
        table.data td.excellent {
            background-color: #d4edda;
            color: #155724;
        }
        table.data td.good {
            background-color: #cce5ff;
            color: #004085;
        }
        table.data td.average {
            background-color: #fff3cd;
            color: #856404;
        }
        table.data td.poor {
            background-color: #f8d7da;
            color: #721c24;
        }
        table.data td.pending {
            background-color: #e2e3e5;
            color: #383d41;
            font-style: italic;
        }
        .legend {
            margin-top: 10px;
            font-size: 8px;
        }
        .legend span {
            margin-right: 10px;
            padding: 2px 6px;
        }
        .legend .excellent { background-color: #d4edda; }
        .legend .good { background-color: #cce5ff; }
        .legend .average { background-color: #fff3cd; }
        .legend .poor { background-color: #f8d7da; }
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
        <h3>REKAP NILAI TUGAS PESERTA</h3>
        <p>{{ $bimtek->judul_final }}</p>
    </div>

    <div class="info">
        <span><strong>Tanggal:</strong> {{ $bimtek->tanggal_mulai_final ? $bimtek->tanggal_mulai_final->locale('id')->isoFormat('D MMMM Y') : '-' }} - {{ $bimtek->tanggal_selesai_final ? $bimtek->tanggal_selesai_final->locale('id')->isoFormat('D MMMM Y') : '-' }}</span>
        <span><strong>Narasumber:</strong> {{ is_array($bimtek->narasumber ?? null)
            ? ($bimtek->narasumber['name'] ?? $bimtek->narasumber['nama'] ?? '-')
            : (($bimtek->narasumber->name ?? $bimtek->narasumber->nama ?? $bimtek->narasumber ?? '-') ?? '-') }}</span>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 150px;">Nama Peserta</th>
                <th>Email</th>
                @foreach($tugasList as $tugas)
                <th style="width: 80px;">{{ Str::limit($tugas->judul, 20) }}</th>
                @endforeach
                <th style="width: 60px;">Rata-rata</th>
                <th style="width: 50px;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($rekapNilai as $userId => $data)
            @php
                $rataRata = $data['rata_rata'];
                $predikat = '-';
                if ($rataRata !== null) {
                    if ($rataRata >= 85) $predikat = 'Sangat Baik';
                    elseif ($rataRata >= 70) $predikat = 'Baik';
                    elseif ($rataRata >= 55) $predikat = 'Cukup';
                    else $predikat = 'Kurang';
                }
            @endphp
            <tr>
                <td>{{ $no++ }}</td>
                <td class="left">{{ $data['user']->name }}</td>
                <td class="left">{{ $data['user']->email }}</td>
                @foreach($tugasList as $tugas)
                @php
                    $nilai = $data['nilai'][$tugas->id] ?? null;
                    $nilaiClass = '';
                    if ($nilai !== null) {
                        if ($nilai >= 85) $nilaiClass = 'excellent';
                        elseif ($nilai >= 70) $nilaiClass = 'good';
                        elseif ($nilai >= 55) $nilaiClass = 'average';
                        else $nilaiClass = 'poor';
                    } else {
                        $nilaiClass = 'pending';
                    }
                @endphp
                <td class="{{ $nilaiClass }}">{{ $nilai ?? '-' }}</td>
                @endforeach
                <td><strong>{{ $rataRata ?? '-' }}</strong></td>
                <td>{{ $predikat }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 5 + $tugasList->count() }}">Tidak ada data nilai</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="legend">
        <strong>Keterangan Nilai:</strong>
        <span class="excellent">85-100 = Sangat Baik</span>
        <span class="good">70-84 = Baik</span>
        <span class="average">55-69 = Cukup</span>
        <span class="poor">&lt;55 = Kurang</span>
    </div>

    <div class="summary">
        <strong>Total Peserta:</strong> {{ count($rekapNilai) }} orang | 
        <strong>Total Tugas:</strong> {{ $tugasList->count() }} tugas
    </div>

    <div class="footer">
        <div class="signature">
            <p>Padang, {{ $tanggal_cetak }}</p>
            <p>Narasumber</p>
            <div class="signature-line"></div>
            <p><strong>{{ is_array($bimtek->narasumber ?? null)
                ? ($bimtek->narasumber['name'] ?? $bimtek->narasumber['nama'] ?? '____________________')
                : (($bimtek->narasumber->name ?? $bimtek->narasumber->nama ?? $bimtek->narasumber ?? '____________________') ?? '____________________') }}</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
