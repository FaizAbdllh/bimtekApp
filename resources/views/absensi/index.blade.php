<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.show', $bimtek) }}" class="hover:text-primary-600">{{ Str::limit($bimtek->judul_final, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-900 font-medium">Kelola Absensi</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Kelola Absensi
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $modePelaksanaan = $bimtek->mode_pelaksanaan_code;
            @endphp
            {{-- Page Header with Actions --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('bimtek.show', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <div class="mt-2">
                        <p class="text-gray-600">{{ $bimtek->judul_final }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if($canManage)
                        <a href="{{ route('bimtek.absensi.rekap', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Rekap
                        </a>
                    @endif
                    @if($canManage)
                        <a href="{{ route('bimtek.absensi.create', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Sesi
                        </a>
                    @endif
                </div>
            </div>

            {{-- Statistics Cards --}}
            @if(!$isVerified)
                {{-- Locked State for Unverified Peserta --}}
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                    <p class="text-sm text-gray-600 max-w-md mx-auto">
                        Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses absensi.
                    </p>
                </div>
            @else
            @if(in_array($modePelaksanaan, ['online', 'hybrid']))
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    Mode {{ ucfirst($modePelaksanaan) }} aktif. Peserta dapat melakukan presensi online langsung dari tombol <span class="font-semibold">Hadir Online</span>.
                    @if($bimtek->virtual_meeting_url)
                        <a href="{{ $bimtek->virtual_meeting_url }}" target="_blank" rel="noopener" class="font-semibold underline ml-1">Buka Ruang Virtual</a>
                    @endif
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Sesi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalSesi }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sesi Terbuka</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bimtek->sesiAbsensis->where('status', 'terbuka')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Peserta</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalPeserta }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sesi Absensi List --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Sesi Absensi</h3>
                </div>

                @if($bimtek->sesiAbsensis->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Sesi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    @if($isPeserta)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Anda</th>
                                    @endif
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bimtek->sesiAbsensis as $index => $sesi)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $sesi->nama_sesi }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($sesi->isOpen())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                                    Terbuka
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Ditutup
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900 font-medium">{{ $sesi->absensi_pesertas_count }}</span>
                                            <span class="text-sm text-gray-500">/ {{ $totalPeserta }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sesi->openedBy->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sesi->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        @if($isPeserta)
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(in_array($sesi->id, $userAttendances))
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Hadir
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Belum Hadir
                                                    </span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                {{-- Peserta: Scan QR --}}
                                                @if($isPeserta && $sesi->isOpen() && !in_array($sesi->id, $userAttendances))
                                                    @if(in_array($modePelaksanaan, ['offline', 'hybrid']))
                                                        <a href="{{ route('bimtek.absensi.scan-interface', [$bimtek, $sesi]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                                            </svg>
                                                            Scan QR
                                                        </a>
                                                    @endif
                                                    @if(in_array($modePelaksanaan, ['online', 'hybrid']))
                                                        <a href="{{ route('bimtek.absensi.show', [$bimtek, $sesi]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M4 12l1.41-1.41a2 2 0 012.83 0L9 11m0 0l3.59-3.59a2 2 0 012.82 0L20 12m-11-1v6"/>
                                                            </svg>
                                                            Upload Bukti
                                                        </a>
                                                    @endif
                                                @endif

                                                {{-- Detail Button --}}
                                                <a href="{{ route('bimtek.absensi.show', [$bimtek, $sesi]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-xs font-medium transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Detail
                                                </a>

                                                {{-- Toggle Status --}}
                                                @if($canManage)
                                                    <form action="{{ route('bimtek.absensi.toggle-status', [$bimtek, $sesi]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        @if($sesi->isOpen())
                                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-xs font-medium transition">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                                </svg>
                                                                Tutup
                                                            </button>
                                                        @else
                                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium transition">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                                </svg>
                                                                Buka
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
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada sesi absensi</h3>
                        <p class="text-gray-500 mb-4">Sesi absensi belum dibuat untuk bimtek ini.</p>
                        @if($canManage)
                            <a href="{{ route('bimtek.absensi.create', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Buat Sesi Absensi
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
