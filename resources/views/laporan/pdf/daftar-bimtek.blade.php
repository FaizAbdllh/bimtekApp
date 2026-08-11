<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Bimtek Tahun {{ $tahun }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
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
            font-size: 9px;
        }
        table.data td.center {
            text-align: center;
        }
        .status-persiapan {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        .status-berlangsung {
            background-color: #cce5ff;
            color: #004085;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        .status-disetujui_final {
            background-color: #e2e3e5;
            color: #383d41;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .summary-row {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <h2>BALAI BESAR PENJAMINAN MUTU PENDIDIKAN SUMATERA BARAT</h2>
    </div>

    <div class="title">
        <h3>DAFTAR KEGIATAN BIMBINGAN TEKNIS</h3>
        <p>Tahun {{ $tahun }}{{ $status != 'semua' ? ' - Status: ' . ucfirst($status) : '' }}</p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Judul Bimtek</th>
                <th style="width: 90px;">Tanggal Mulai</th>
                <th style="width: 90px;">Tanggal Selesai</th>
                <th style="width: 100px;">Lokasi</th>
                <th style="width: 60px;">Peserta</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bimteks as $index => $bimtek)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                {{-- REFAKTORISASI: Fallback ke judul usulan awal jika nama kelas aktual belum diubah --}}
                <td>{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</td>
                
                {{-- REFAKTORISASI WAKTU: Menyelaraskan ke kolom tanggal_mulai_aktual & tanggal_selesai_aktual baru --}}
                <td class="center">
                    {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('d/m/Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('d/m/Y') : '-') }}
                </td>
                <td class="center">
                    {{ $bimtek->tanggal_selesai_aktual ? $bimtek->tanggal_selesai_aktual->format('d/m/Y') : ($bimtek->tanggal_selesai_rencana ? $bimtek->tanggal_selesai_rencana->format('d/m/Y') : '-') }}
                </td>

                {{-- REFAKTORISASI LOKASI: Mengubah lokasi_final menjadi lokasi_aktual --}}
                <td>{{ Str::limit($bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-', 25) }}</td>
                <td class="center">{{ $bimtek->peserta_count ?? $bimtek->peserta->count() }}</td>
                <td class="center">
                    {{-- REFAKTORISASI STATUS: Mengubah status_pelaksanaan menjadi status --}}
                    <span class="status-{{ $bimtek->status ?? 'persiapan' }}">{{ $bimtek->status == 'disetujui_final' ? 'Disetujui' : ucfirst($bimtek->status ?? '-') }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="center">Tidak ada data bimtek</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>RINGKASAN:</strong><br><br>
        <div class="summary-row"><strong>Total Kegiatan:</strong> {{ $bimteks->count() }}</div>
        {{-- REFAKTORISASI LOGIKA AGREGAT: Mengubah pencarian dari kolom status_pelaksanaan menjadi status --}}
        <div class="summary-row"><strong>Persiapan:</strong> {{ $bimteks->where('status', 'persiapan')->count() }}</div>
        <div class="summary-row"><strong>Berlangsung:</strong> {{ $bimteks->where('status', 'berlangsung')->count() }}</div>
        <div class="summary-row"><strong>Selesai:</strong> {{ $bimteks->where('status', 'selesai')->count() }}</div>
        <br>
        <div class="summary-row"><strong>Total Peserta:</strong> {{ $bimteks->sum(function($b) { return $b->peserta_count ?? $b->peserta->count(); }) }} orang</div>
    </div>

    <div class="footer">
        <div class="signature">
            <p>Padang, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>Kepala BBPMP Sumbar</p>
            <div class="signature-line"></div>
            <p><strong>____________________</strong></p>
            <small>NIP. ____________________</small>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>