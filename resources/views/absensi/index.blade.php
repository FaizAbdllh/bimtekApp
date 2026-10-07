```blade
<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium">
                    <li>
                        <a href="{{ route('bimtek.index') }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">
                            Bimtek
                        </a>
                    </li>

                    <li>
                        <span class="mx-1 text-gray-400">/</span>
                    </li>

                    {{-- REFAKTORISASI: Menjamin kestabilan UUID dan menyematkan fallback judul rencana --}}
                    <li>
                        <a href="{{ route('bimtek.show', $bimtek->id) }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">
                            {{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}
                        </a>
                    </li>

                    <li>
                        <span class="mx-1 text-gray-400">/</span>
                    </li>

                    <li class="text-gray-700">
                        Kelola Absensi
                    </li>
                </ol>
            </nav>

            <h2 class="text-xl font-bold text-gray-800">
                Pusat Kendali Presensi Kelas
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @php
            $modePelaksanaan = $bimtek->mode_pelaksanaan_code;
        @endphp

        {{-- Header & Action --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('bimtek.show', $bimtek->id) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Detail
                </a>

                <div class="mt-3 text-sm">
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="font-medium text-gray-900">
                        {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Metode Pelaksanaan:
                        <span class="font-semibold text-primary-600">
                            {{ $bimtek->mode_pelaksanaan_label }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($canManage)
                    <a href="{{ route('bimtek.absensi.rekap', $bimtek->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Rekapitulasi Kehadiran
                    </a>
                @endif

                @if($canManage)
                    <a href="{{ route('bimtek.absensi.create', $bimtek->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Sesi Absensi
                    </a>
                @endif
            </div>
        </div>

        {{-- Jaring Pengaman Proteksi Kelulusan Administrasi Dokumen Peserta --}}
        @if(!$isVerified)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="text-center py-8 px-6">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>

                    <h3 class="mt-2 text-base font-bold text-gray-800">
                        Gerbang Presensi Terkunci
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                        Mohon maaf, Anda wajib merampungkan tahapan unggah dokumen penugasan resmi
                        (Surat Tugas/SPPD) dan menunggu konfirmasi sah panitia untuk dapat mengisi
                        daftar kehadiran sesi kelas.
                    </p>
                </div>
            </div>
        @else

            {{-- Pemberitahuan Ruang Rapat Virtual (Online / Hybrid Only) --}}
            @if(in_array($modePelaksanaan, ['online', 'hybrid']))
                <div class="mb-6 p-3 rounded-lg border border-primary-200 bg-primary-50 text-sm text-primary-800">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0 text-primary-600"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 3a9 9 0 100 18 9 9 0 000-18z" />
                            </svg>

                            <p class="leading-relaxed">
                                Sistem mendeteksi kelas berjalan dalam mode
                                {{ ucfirst($modePelaksanaan) }}.
                                Anda dapat mengisi lembar kehadiran daring dengan mengunggah
                                tangkapan layar bukti kepesertaan.
                            </p>
                        </div>

                        @if($bimtek->virtual_meeting_url)
                            <a href="{{ $bimtek->virtual_meeting_url }}"
                                target="_blank"
                                rel="noopener"
                                class="shrink-0 inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg border border-primary-200 bg-white text-primary-700 hover:bg-primary-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4m-2-10h6m0 0v6m0-6L10 14" />
                                </svg>
                                Buka Link Rapat Zoom
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Quick Info Statistics Widgets --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                {{-- Total Sesi --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6 flex items-center">
                        <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>

                        <div class="ml-4">
                            <p class="text-sm text-gray-500">
                                Total Jadwal Sesi
                            </p>

                            <p class="text-2xl font-semibold text-gray-800">
                                {{ $totalSesi }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Sesi Aktif --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6 flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <div class="ml-4">
                            <p class="text-sm text-gray-500">
                                Gerbang Sesi Aktif
                            </p>

                            <p class="text-2xl font-semibold text-gray-800">
                                {{ $bimtek->sesiAbsensis->where('status', 'terbuka')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Total Peserta --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6 flex items-center">
                        <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>

                        <div class="ml-4">
                            <p class="text-sm text-gray-500">
                                Total Anggota Terdaftar
                            </p>

                            <p class="text-2xl font-semibold text-gray-800">
                                {{ $totalPeserta }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table Card Wrapper --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">

                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Sesi Rekaman Absensi
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Kelola sesi presensi, status gerbang, dan kehadiran peserta pada setiap sesi.
                    </p>
                </div>

                @if($bimtek->sesiAbsensis->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr class="border-b border-gray-200">
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 w-14">
                                        No
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Nama Label Sesi
                                    </th>

                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 w-32">
                                        Status Gerbang
                                    </th>

                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Partisipasi Terisi
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Oleh Operator
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Waktu Rilis
                                    </th>

                                    @if($isPeserta)
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 w-36">
                                            Status Kehadiran Anda
                                        </th>
                                    @endif

                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 pr-6 w-44">
                                        Tindakan Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @foreach($bimtek->sesiAbsensis as $index => $sesi)
                                    <tr class="hover:bg-gray-100 transition-colors">

                                        <td class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{ $index + 1 }}
                                        </td>

                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $sesi->nama_sesi }}
                                        </td>

                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if($sesi->isOpen())
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    Terbuka
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    Ditutup
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-800">
                                            <span class="text-primary-600">
                                                {{ $sesi->absensi_pesertas_count }}
                                            </span>

                                            <span class="text-gray-500 font-medium">
                                                / {{ $totalPeserta }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sesi->openedBy->name ?? 'Sistem' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $sesi->created_at->format('d M Y, H:i') }} WIB
                                        </td>

                                        @if($isPeserta)
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if(in_array($sesi->id, $userAttendances))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        Hadir
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                        Belum Hadir
                                                    </span>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="px-6 py-4 text-right pr-6 whitespace-nowrap align-middle">
                                            <div class="flex items-center justify-end gap-1">

                                                {{-- Alur Aksi Khusus Aktor Peserta --}}
                                                @if($isPeserta && $sesi->isOpen() && !in_array($sesi->id, $userAttendances))

                                                    @if(in_array($modePelaksanaan, ['offline', 'hybrid']))
                                                        {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                        <a href="{{ route('bimtek.absensi.scan-interface', [$bimtek->id, $sesi->id]) }}"
                                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                            Scan QR
                                                        </a>
                                                    @endif

                                                    @if(in_array($modePelaksanaan, ['online', 'hybrid']))
                                                        {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                        <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}"
                                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                            Kirim Bukti
                                                        </a>
                                                    @endif

                                                @endif

                                                {{-- Tombol Detail Record --}}
                                                {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                    Detail
                                                </a>

                                                {{-- Opsi Toggle Gerbang (Aktor Panitia Pokja Only) --}}
                                                @if($canManage)
                                                    {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                    <form action="{{ route('bimtek.absensi.toggle-status', [$bimtek->id, $sesi->id]) }}"
                                                        method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')

                                                        @if($sesi->isOpen())
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                                Kunci Gerbang
                                                            </button>
                                                        @else
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                                Buka Gerbang
                                                            </button>
                                                        @endif
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Komponen Layar Kosong --}}
                    <div class="text-center py-8 px-6">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>

                        <h3 class="mt-2 text-base font-bold text-gray-800">
                            Belum Ada Lembar Presensi
                        </h3>

                        <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                            Sesi pencatatan absensi belum dirilis oleh tim penanggung jawab
                            untuk penugasan ini.
                        </p>

                        @if($canManage)
                            {{-- REFAKTORISASI: Penyelarasan model ID rute buat sesi --}}
                            <div class="mt-4">
                                <a href="{{ route('bimtek.absensi.create', $bimtek->id) }}"
                                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Rilis Sesi Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        @endif
    </div>
</x-app-layout>
```
