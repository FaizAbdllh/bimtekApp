<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kegiatan - {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
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
            margin: 25px 0;
        }
        .title h3 {
            font-size: 16px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .title p {
            margin: 0;
            color: #666;
        }
        .section {
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #333;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 180px;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th, table.data td {
            border: 1px solid #333;
            padding: 6px 5px;
            text-align: left;
        }
        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        table.data td.center {
            text-align: center;
        }
        .stat-box {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f0f0f0;
            margin: 5px 10px 5px 0;
            border-radius: 5px;
            text-align: center;
        }
        .stat-box .number {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-container {
            width: 100%;
            margin-top: 30px;
        }
        .signature-left {
            float: left;
            width: 45%;
            text-align: center;
        }
        .signature-right {
            float: right;
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }
        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <h2>BALAI BESAR PENJAMINAN MUTU PENDIDIKAN SUMATERA BARAT</h2>
    </div>

    <div class="title">
        <h3>LAPORAN KEGIATAN BIMBINGAN TEKNIS</h3>
        {{-- REFAKTORISASI: Menambahkan jaring pengaman fallback jika judul aktual belum diisi --}}
        <p>{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
    </div>

    {{-- Informasi Umum --}}
    <div class="section">
        <div class="section-title">I. Informasi Umum Kegiatan</div>
        <table class="info-table">
            <tr>
                <td>Nama Kegiatan</td>
                <td>: {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</td>
            </tr>
            <tr>
                <td>Tanggal Pelaksanaan</td>
                {{-- REFAKTORISASI WAKTU: Mengalihkan dari tanggal_mulai_final lama ke kolom aktual baru dengan fallback rencana awal --}}
                <td>: 
                    {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->locale('id')->isoFormat('D MMMM Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->locale('id')->isoFormat('D MMMM Y') : '-') }} 
                    s/d 
                    {{ $bimtek->tanggal_selesai_aktual ? $bimtek->tanggal_selesai_aktual->locale('id')->isoFormat('D MMMM Y') : ($bimtek->tanggal_selesai_rencana ? $bimtek->tanggal_selesai_rencana->locale('id')->isoFormat('D MMMM Y') : '-') }}
                </td>
            </tr>
            <tr>
                <td>Tempat / Lokasi Aktual</td>
                {{-- REFAKTORISASI LOKASI: Mengalihkan dari lokasi_final ke lokasi_aktual baru --}}
                <td>: {{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</td>
            </tr>
            <tr>
                <td>Narasumber / Pemateri</td>
                {{-- REFAKTORISASI NARASUMBER: Mengurai array JSON daftar_pemateri baru menjadi string list terpisah koma --}}
                @php
                    $pemateriList = '-';
                    if (is_array($bimtek->daftar_pemateri) && count($bimtek->daftar_pemateri) > 0) {
                        $names = [];
                        foreach($bimtek->daftar_pemateri as $pm) {
                            if(!empty($pm['nama'])) {
                                $names[] = $pm['nama'] . (!empty($pm['asal_instansi']) ? ' ('.$pm['asal_instansi'].')' : '');
                            }
                        }
                        if(count($names) > 0) {
                            $pemateriList = implode(', ', $names);
                        }
                    }
                @endphp
                <td>: {{ $pemateriList }}</td>
            </tr>
            <tr>
                <td>Koordinator / PIC Pelaksana</td>
                <td>: {{ $pic ? $pic->name : ($bimtek->pic->name ?? '-') }}</td>
            </tr>
            <tr>
                <td>Status Akhir Kelas</td>
                {{-- REFAKTORISASI STATUS: Menyelaraskan ke kolom status tunggal baru --}}
                <td>: {{ $bimtek->status === 'disetujui_final' ? 'Disetujui (Siap)' : ucfirst($bimtek->status ?? '-') }}</td>
            </tr>
        </table>
    </div>

    {{-- Statistik --}}
    <div class="section">
        <div class="section-title">II. Statistik Kegiatan</div>
        <div class="stat-box">
            <div class="number">{{ $totalPeserta }}</div>
            <div class="label">Total Peserta</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $totalSesi }}</div>
            <div class="label">Sesi Absensi</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $totalTugas }}</div>
            <div class="label">Total Tugas</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $totalMateri }}</div>
            <div class="label">Total Materi</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $avgKehadiran }}%</div>
            <div class="label">Rata-rata Kehadiran</div>
        </div>
    </div>

    {{-- Daftar Peserta --}}
    <div class="section">
        <div class="section-title">III. Daftar Peserta Terdaftar</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Nama Lengkap Peserta</th>
                    <th>NIP / Identitas</th>
                    <th>Asal Instansi Sekolah / Lembaga</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peserta as $index => $p)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td class="center">{{ $p->nip ?? '-' }}</td>
                    <td>{{ $p->asal_instansi ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="center">Tidak ada peserta yang terdaftar dalam kelas ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Daftar Panitia Kerja Pokja --}}
    @if(isset($panitia) && $panitia->count() > 0)
    <div class="section">
        <div class="section-title">IV. Daftar Jajaran Panitia Tim Kerja</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Nama Lengkap Panitia</th>
                    <th>NIP</th>
                    <th>Fungsi Penugasan Kedinasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($panitia as $index => $p)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $p->name }}</td>
                    <td class="center">{{ $p->nip ?? '-' }}</td>
                    {{-- REFAKTORISASI: Membaca penugasan panitia kedinasan dari kolom pivot bimtek_panitias --}}
                    <td>{{ $p->pivot->fungsi_panitia ?? 'Anggota Pelaksana' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Penutup --}}
    <div class="footer">
        <div class="section-title">Penutup</div>
        <p style="text-align: justify;">Demikian draf dokumen laporan komplit pelaksanaan kegiatan Bimbingan Teknis ini disusun dengan sebenar-benarnya sebagai berkas pertanggungjawaban administratif dan bukti akuntabilitas realisasi anggaran belanja unit kerja.</p>
        
        <div class="signature-container">
            <div class="signature-left">
                <p>Mengetahui,</p>
                <p>Kepala BBPMP Sumbar</p>
                <div class="signature-line"></div>
                <p><strong>____________________</strong></p>
                <small>NIP. ____________________</small>
            </div>
            <div class="signature-right">
                <p>Padang, {{ $tanggal_cetak ?? now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p>Koordinator / PIC Bimtek</p>
                <div class="signature-line"></div>
                @php
                    $picName = $pic ? $pic->name : ($bimtek->pic->name ?? '____________________');
                    $picNip = $pic ? ($pic->nip ?? '-') : ($bimtek->pic->nip ?? '____________________');
                @endphp
                <p><strong>{{ $picName }}</strong></p>
                <small>NIP. {{ $picNip }}</small>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</body>
</html>