<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    {{-- REFAKTORISASI: Menjamin kestabilan UUID dan menyematkan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-800 font-bold">Kelola Absensi</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Pusat Kendali Presensi Kelas
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $modePelaksanaan = $bimtek->mode_pelaksanaan_code;
            @endphp
            
            {{-- Atasan Panel Aksi Lembar Utama --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Detail
                    </a>
                    <div class="mt-3 text-sm">
                        {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                        <p class="text-gray-900 font-semibold">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Metode Pelaksanaan: <span class="text-primary-600 font-bold uppercase">{{ $bimtek->mode_pelaksanaan_label }}</span></p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if($canManage)
                        <a href="{{ route('bimtek.absensi.rekap', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Rekapitulasi Kehadiran
                        </a>
                    @endif
                    @if($canManage)
                        <a href="{{ route('bimtek.absensi.create', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Sesi Absensi
                        </a>
                    @endif
                </div>
            </div>

            {{-- Jaring Pengaman Proteksi Kelulusan Administrasi Dokumen Peserta --}}
            @if(!$isVerified)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-16 bg-gray-50 rounded-full mb-4 border border-gray-100 text-gray-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Gerbang Presensi Terkunci</h3>
                    <p class="text-xs text-gray-400 max-w-sm mx-auto font-medium leading-relaxed">
                        Mohon maaf, Anda wajib merampungkan tahapan unggah dokumen penugasan resmi (Surat Tugas/SPPD) dan menunggu konfirmasi sah panitia untuk dapat mengisi daftar kehadiran sesi kelas.
                    </p>
                </div>
            @else
            
            {{-- Pemberitahuan Ruang Rapat Virtual (Online / Hybrid Only) --}}
            @if(in_array($modePelaksanaan, ['online', 'hybrid']))
                <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50/50 p-4 text-xs font-semibold text-blue-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shadow-inner shadow-blue-50">
                    <p class="leading-relaxed">Sistem mendeteksi kelas berjalan dalam mode {{ ucfirst($modePelaksanaan) }}. Anda dapat mengisi lembar kehadiran daring dengan mengunggah tangkapan layar bukti kepesertaan.</p>
                    @if($bimtek->virtual_meeting_url)
                        <a href="{{ $bimtek->virtual_meeting_url }}" target="_blank" rel="noopener" class="shrink-0 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] uppercase font-bold tracking-wide transition shadow-sm">Buka Link Rapat Zoom</a>
                    @endif
                </div>
            @endif

            {{-- Quick Info Statistics Widgets --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 text-sm">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jadwal Sesi</p>
                            <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ $totalSesi }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Gerbang Sesi Aktif</p>
                            <p class="text-2xl font-bold text-green-600 mt-0.5">{{ $bimtek->sesiAbsensis->where('status', 'terbuka')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota Terdaftar</p>
                            <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ $totalPeserta }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table Card Wrapper --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Sesi Rekaman Absensi</h3>
                </div>

                @if($bimtek->sesiAbsensis->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-center w-14">No</th>
                                    <th class="px-6 py-3 text-left">Nama Label Sesi</th>
                                    <th class="px-6 py-3 text-center w-32">Status Gerbang</th>
                                    <th class="px-6 py-3 text-center">Partisipasi Terisi</th>
                                    <th class="px-6 py-3 text-left">Oleh Operator</th>
                                    <th class="px-6 py-3 text-left">Waktu Rilis</th>
                                    @if($isPeserta)
                                        <th class="px-6 py-3 text-center w-36">Status Kehadiran Anda</th>
                                    @endif
                                    <th class="px-6 py-3 text-right pr-6 w-44">Tindakan Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                @foreach($bimtek->sesiAbsensis as $index => $sesi)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 font-bold text-gray-900">
                                            {{ $sesi->nama_sesi }}
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if($sesi->isOpen())
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                                    Terbuka
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                                    Ditutup
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center font-semibold text-gray-800">
                                            <span class="text-primary-600">{{ $sesi->absensi_pesertas_count }}</span>
                                            <span class="text-gray-400 font-medium">/ {{ $totalPeserta }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium whitespace-nowrap">
                                            {{ $sesi->openedBy->name ?? 'Sistem' }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-400 font-medium text-xs whitespace-nowrap">
                                            {{ $sesi->created_at->format('d M Y, H:i') }} WIB
                                        </td>
                                        @if($isPeserta)
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if(in_array($sesi->id, $userAttendances))
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                        ✓ Hadir Sah
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2.5 py-0.5 rounded-md text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                                                        Belum Hadir
                                                    </span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 text-right pr-6 whitespace-nowrap align-middle">
                                            <div class="flex items-center justify-end gap-1.5 font-bold text-xs">
                                                {{-- Alur Aksi Khusus Aktor Peserta --}}
                                                @if($isPeserta && $sesi->isOpen() && !in_array($sesi->id, $userAttendances))
                                                    @if(in_array($modePelaksanaan, ['offline', 'hybrid']))
                                                        {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                        <a href="{{ route('bimtek.absensi.scan-interface', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center px-2.5 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-sm">
                                                            Scan QR
                                                        </a>
                                                    @endif
                                                    @if(in_array($modePelaksanaan, ['online', 'hybrid']))
                                                        {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                        <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm">
                                                            Kirim Bukti
                                                        </a>
                                                    @endif
                                                @endif

                                                {{-- Tombol Detail Record --}}
                                                {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm">
                                                    Detail
                                                </a>

                                                {{-- Opsi Toggle Gerbang (Aktor Panitia Pokja Only) --}}
                                                @if($canManage)
                                                    {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute --}}
                                                    <form action="{{ route('bimtek.absensi.toggle-status', [$bimtek->id, $sesi->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        @if($sesi->isOpen())
                                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition shadow-sm">
                                                                Kunci Gerbang
                                                            </button>
                                                        @else
                                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm">
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
                    <div class="p-12 text-center text-gray-400 font-medium">
                        <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="text-base font-bold text-gray-900 mb-0.5">Belum Ada Lembar Presensi</h3>
                        <p class="text-xs text-gray-400 mb-4 max-w-xs mx-auto">Sesi pencatatan absensi belum dirilis oleh tim penanggung jawab untuk penugasan ini.</p>
                        @if($canManage)
                            {{-- REFAKTORISASI: Penyelarasan model ID rute buat sesi --}}
                            <a href="{{ route('bimtek.absensi.create', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-bold text-xs uppercase tracking-wide rounded-xl hover:bg-primary-700 transition shadow-sm">
                                Rilis Sesi Pertama
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>