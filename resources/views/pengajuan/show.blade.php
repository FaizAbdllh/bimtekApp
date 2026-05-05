<x-app-layout>
    <x-slot name="header">
        Detail Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('pengajuan.index') }}" class="text-gray-500 hover:text-primary-600">
                            Pengajuan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-700 font-medium">Detail</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner --}}
            @php
                $statusColors = [
                    'draft' => 'bg-gray-100 border-gray-400 text-gray-800',
                    'diajukan' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
                    'disetujui_kepala' => 'bg-blue-100 border-blue-400 text-blue-800',
                    'disetujui_ppk' => 'bg-indigo-100 border-indigo-400 text-indigo-800',
                    'disetujui_final' => 'bg-green-100 border-green-400 text-green-800',
                    'ditolak' => 'bg-red-100 border-red-400 text-red-800',
                    'perlu_revisi' => 'bg-orange-100 border-orange-400 text-orange-800',
                ];
                $statusLabels = [
                    'draft' => 'Draft - Belum Diajukan',
                    'diajukan' => 'Menunggu Persetujuan Kepala',
                    'disetujui_kepala' => 'Disetujui Kepala - Menunggu PPK',
                    'disetujui_ppk' => 'Disetujui PPK',
                    'disetujui_final' => 'Disetujui Final - Bimtek Dibuat',
                    'ditolak' => 'Ditolak',
                    'perlu_revisi' => 'Perlu Revisi',
                ];
                $verificationLabel = $pengajuan->butuh_verifikasi_dokumen
                    ? 'Verifikasi Dokumen Aktif'
                    : 'Verifikasi Dokumen Nonaktif';
                $verificationClass = $pengajuan->butuh_verifikasi_dokumen
                    ? 'bg-amber-100 border-amber-400 text-amber-800'
                    : 'bg-gray-100 border-gray-400 text-gray-700';
            @endphp
            <div class="mb-6 p-4 rounded-lg border-l-4 {{ $statusColors[$pengajuan->status_pengajuan] ?? 'bg-gray-100 border-gray-400 text-gray-800' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Status: {{ $statusLabels[$pengajuan->status_pengajuan] ?? $pengajuan->status_pengajuan }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Jenis Kegiatan</p>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Internal' : 'Eksternal' }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Peserta dari BBPMP' : 'Peserta dari luar instansi' }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Verifikasi Peserta</p>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $verificationClass }}">
                        {{ $verificationLabel }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $pengajuan->butuh_verifikasi_dokumen ? 'Peserta wajib mengunggah dokumen persyaratan.' : 'Peserta dapat langsung mengikuti alur bimtek.' }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Periode Kegiatan</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">
                        {{ $pengajuan->tanggal_mulai_rencana?->format('d M Y') ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-500">s/d {{ $pengajuan->tanggal_selesai_rencana?->format('d M Y') ?? '-' }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Lokasi Kegiatan</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 line-clamp-3">{{ $pengajuan->tempat_kegiatan ?? '-' }}</p>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Header --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $pengajuan->judul_rencana }}</h2>
                            <p class="mt-1 text-sm text-gray-500">Diajukan oleh {{ $pengajuan->user->name }} pada {{ $pengajuan->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Detail Info --}}
                <div class="p-6 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tempat Kegiatan</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tempat_kegiatan ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Sumber Pembiayaan</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->sumber_pembiayaan ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tanggal Mulai</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tanggal_mulai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tanggal Selesai</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tanggal_selesai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Deskripsi Kegiatan</h4>
                            <div class="mt-2 p-4 bg-white rounded-xl border border-gray-200">
                                <p class="text-gray-900 whitespace-pre-line">{{ $pengajuan->deskripsi_rencana ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700">Informasi Verifikasi Dokumen</h4>
                                <p class="text-xs text-gray-500 mt-1">Menentukan apakah peserta wajib unggah dokumen persyaratan.</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $verificationClass }}">
                                {{ $verificationLabel }}
                            </span>
                        </div>
                        <div class="p-4 bg-white rounded-xl border border-gray-200">
                            <p class="text-sm text-gray-700">
                                @if($pengajuan->butuh_verifikasi_dokumen)
                                    Peserta wajib mengunggah dan memverifikasi dokumen persyaratan sebelum dapat mengikuti absensi, tugas, dan sertifikat.
                                @else
                                    Verifikasi dokumen tidak diperlukan untuk pengajuan ini.
                                @endif
                            </p>
                            @if($pengajuan->butuh_verifikasi_dokumen && $pengajuan->jenis_dokumen_wajib)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($pengajuan->jenis_dokumen_wajib as $jenisDokumen)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 border border-gray-200 text-gray-700">
                                            {{ $jenisDokumen }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Rincian Kebutuhan Anggaran --}}
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700">Rincian Kebutuhan Anggaran (RAB)</h4>
                                <p class="text-xs text-gray-500 mt-1">Daftar kebutuhan, validasi SBM, dan total anggaran.</p>
                            </div>
                            <span class="text-xs font-medium text-primary-700 bg-primary-100 px-3 py-1 rounded-full">Total Rp {{ number_format($pengajuan->total_anggaran, 0, ',', '.') }}</span>
                        </div>
                        @if($pengajuan->kebutuhanAnggarans->count() > 0)
                            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-medium text-gray-600 uppercase">No</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-600 uppercase">Uraian Kebutuhan</th>
                                            <th class="px-3 py-2 text-center font-medium text-gray-600 uppercase">Volume</th>
                                            <th class="px-3 py-2 text-right font-medium text-gray-600 uppercase">Harga Satuan</th>
                                            <th class="px-3 py-2 text-right font-medium text-gray-600 uppercase">Total</th>
                                            <th class="px-3 py-2 text-center font-medium text-gray-600 uppercase">Status SBM</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->kebutuhanAnggarans as $index => $item)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-3 py-2 text-gray-900">
                                                <div class="font-medium">{{ $item->nama_item }}</div>
                                                <div class="text-gray-500 mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                        @switch($item->kategori)
                                                            @case('honor') bg-purple-100 text-purple-800 @break
                                                            @case('transportasi') bg-blue-100 text-blue-800 @break
                                                            @case('akomodasi') bg-indigo-100 text-indigo-800 @break
                                                            @case('konsumsi') bg-yellow-100 text-yellow-800 @break
                                                            @case('atk') bg-green-100 text-green-800 @break
                                                            @case('sewa') bg-pink-100 text-pink-800 @break
                                                            @default bg-gray-100 text-gray-800
                                                        @endswitch
                                                    ">
                                                        {{ ucfirst($item->kategori) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-gray-900 text-center">
                                                <div class="text-sm">
                                                    {{ $item->volume_1 }} {{ $item->satuan_primary }}
                                                    @if($item->volume_2 > 1 || $item->satuan_secondary)
                                                        × {{ $item->volume_2 }} {{ $item->satuan_secondary }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-gray-900 text-right">
                                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                                @if($item->harga_satuan_sbm && $item->harga_satuan != $item->harga_satuan_sbm)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        SBM: Rp {{ number_format($item->harga_satuan_sbm, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 font-medium text-gray-900 text-right">
                                                Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                @if($item->status_validasi)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        @switch($item->status_validasi)
                                                            @case('sesuai_sbm') bg-green-100 text-green-800 @break
                                                            @case('deviasi_minor') bg-blue-100 text-blue-800 @break
                                                            @case('deviasi_major') bg-yellow-100 text-yellow-800 @break
                                                            @case('deviasi_signifikan') bg-orange-100 text-orange-800 @break
                                                            @case('non_sbm') bg-gray-100 text-gray-800 @break
                                                            @default bg-gray-100 text-gray-800
                                                        @endswitch
                                                    ">
                                                        @switch($item->status_validasi)
                                                            @case('sesuai_sbm') ✓ Sesuai SBM @break
                                                            @case('deviasi_minor') Deviasi Minor @break
                                                            @case('deviasi_major') ⚠ Deviasi Major @break
                                                            @case('deviasi_signifikan') ⚠ Deviasi Signifikan @break
                                                            @case('non_sbm') Non-SBM @break
                                                            @default - @break
                                                        @endswitch
                                                        @if($item->persentase_deviasi)
                                                            <span class="ml-1">({{ number_format($item->persentase_deviasi, 1) }}%)</span>
                                                        @endif
                                                    </span>
                                                    @if($item->justifikasi_deviasi)
                                                        <div class="text-xs text-gray-600 mt-1 text-left">
                                                            <strong>Justifikasi:</strong> {{ $item->justifikasi_deviasi }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-100">
                                        <tr>
                                            <td colspan="4" class="px-3 py-3 font-bold text-gray-900 text-right">Total Anggaran</td>
                                            <td class="px-3 py-3 font-bold text-primary-700 text-right">Rp {{ number_format($pengajuan->total_anggaran, 0, ',', '.') }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- Summary by Category --}}
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                @php
                                    $categories = $pengajuan->kebutuhanAnggarans->groupBy('kategori');
                                @endphp
                                @foreach($categories as $kategori => $items)
                                <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                                    <p class="text-xs text-gray-500 uppercase">{{ ucfirst($kategori) }}</p>
                                    <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($items->sum('total_biaya'), 0, ',', '.') }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-white border border-gray-200 rounded-xl text-center">
                                <p class="text-gray-500">Belum ada rincian anggaran</p>
                            </div>
                        @endif
                    </div>

                    {{-- Kebutuhan Fasilitas & Logistik --}}
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700">Kebutuhan Fasilitas & Logistik</h4>
                                <p class="text-xs text-gray-500 mt-1">Catatan untuk Koordinator RT dan pelaksana lapangan.</p>
                            </div>
                            <span class="text-xs text-primary-700 bg-primary-100 px-3 py-1 rounded-full">Koordinator RT</span>
                        </div>
                        
                        @if($pengajuan->fasilitasLogistiks->count() > 0 || $pengajuan->catatan_logistik)
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Daftar Fasilitas --}}
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-3">Daftar Permintaan Fasilitas</p>
                                    @if($pengajuan->fasilitasLogistiks->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($pengajuan->fasilitasLogistiks as $fasilitas)
                                        <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-gray-200 shadow-sm">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 rounded-full bg-primary-100 text-primary-700 text-sm font-medium mr-3 px-2">
                                                    {{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? 'unit' }}
                                                </span>
                                                <span class="text-sm text-gray-900">{{ $fasilitas->nama_fasilitas }}</span>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @switch($fasilitas->status)
                                                    @case('diminta') bg-yellow-100 text-yellow-800 @break
                                                    @case('tersedia') bg-green-100 text-green-800 @break
                                                    @case('tidak_tersedia') bg-red-100 text-red-800 @break
                                                @endswitch
                                            ">
                                                {{ ucfirst(str_replace('_', ' ', $fasilitas->status)) }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <p class="text-sm text-gray-500">Tidak ada permintaan fasilitas</p>
                                    @endif
                                </div>

                                {{-- Instruksi Logistik --}}
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-3">Instruksi Penataan / Catatan</p>
                                    @if($pengajuan->catatan_logistik)
                                    <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $pengajuan->catatan_logistik }}</p>
                                    </div>
                                    @else
                                    <p class="text-sm text-gray-500">Tidak ada instruksi khusus</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-white border border-gray-200 rounded-xl text-center">
                                <p class="text-gray-500">Belum ada permintaan fasilitas & logistik</p>
                            </div>
                        @endif
                    </div>

                    {{-- Catatan dari Kepala --}}
                    @if($pengajuan->catatan_kepala)
                    <div class="rounded-2xl border border-gray-200 bg-blue-50/60 p-5">
                        <h4 class="text-sm font-semibold text-blue-800 mb-2">Catatan dari Kepala</h4>
                        <div class="p-4 bg-white border border-blue-200 rounded-xl">
                            <p class="text-gray-800">{{ $pengajuan->catatan_kepala }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Catatan dari PPK --}}
                    @if($pengajuan->catatan_ppk)
                    <div class="rounded-2xl border border-gray-200 bg-indigo-50/60 p-5">
                        <h4 class="text-sm font-semibold text-indigo-800 mb-2">Catatan dari PPK</h4>
                        <div class="p-4 bg-white border border-indigo-200 rounded-xl">
                            <p class="text-gray-800">{{ $pengajuan->catatan_ppk }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Catatan dari Koordinator RT --}}
                    @if($pengajuan->catatan_rt)
                    <div class="rounded-2xl border border-gray-200 bg-purple-50/60 p-5">
                        <h4 class="text-sm font-semibold text-purple-800 mb-2">Catatan dari Koordinator Rumah Tangga</h4>
                        <div class="p-4 bg-white border border-purple-200 rounded-xl">
                            <p class="text-gray-800">{{ $pengajuan->catatan_rt }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Link ke Bimtek jika sudah dibuat --}}
                    @if($pengajuan->bimtek)
                    <div class="rounded-2xl border border-gray-200 bg-green-50/60 p-5">
                        <div class="p-4 bg-white border border-green-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-green-800 font-medium">Bimtek telah dibuat dari pengajuan ini</span>
                                </div>
                                <a href="{{ route('bimtek.show', $pengajuan->bimtek) }}" class="text-green-700 hover:text-green-900 font-medium text-sm">
                                    Lihat Bimtek →
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">Aksi pengajuan</span>
                            <span class="mx-2">•</span>
                            <span>Status: {{ $statusLabels[$pengajuan->status_pengajuan] ?? $pengajuan->status_pengajuan }}</span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium text-sm hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </a>

                            @if(Auth::id() === $pengajuan->user_id && in_array($pengajuan->status_pengajuan, ['draft', 'diajukan', 'perlu_revisi']))
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <a href="{{ route('pengajuan.edit', $pengajuan) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-amber-600 transition shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('pengajuan.destroy', $pengajuan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 transition shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
