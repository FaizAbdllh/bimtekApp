<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.show', $bimtek) }}" class="hover:text-primary-600">{{ Str::limit($bimtek->judul_final, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek) }}" class="hover:text-primary-600">Absensi</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-900 font-medium">{{ Str::limit($sesi->nama_sesi, 20) }}</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                {{ $sesi->nama_sesi }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header with Actions --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-gray-600">{{ $bimtek->judul_final }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('bimtek.absensi.index', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    @if($canManage)
                        <a href="{{ route('bimtek.absensi.edit', [$bimtek, $sesi]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        {{-- Toggle Status --}}
                        <form action="{{ route('bimtek.absensi.toggle-status', [$bimtek, $sesi]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            @if($sesi->isOpen())
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-medium text-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Tutup Sesi
                                </button>
                            @else
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                    Buka Sesi
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Attendance List --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Peserta Hadir --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Daftar Hadir
                                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $sesi->absensiPesertas->count() }} peserta)</span>
                                </h3>
                            </div>
                        </div>

                        @if($sesi->absensiPesertas->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Hadir</th>
                                            @if($canManage)
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sesi->absensiPesertas->sortBy('created_at') as $index => $absensi)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-primary-600">{{ strtoupper(substr($absensi->peserta->name ?? 'U', 0, 1)) }}</span>
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-900">{{ $absensi->peserta->name ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $absensi->peserta->email ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $absensi->created_at->format('d/m/Y H:i:s') }}
                                                </td>
                                                @if($canManage)
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <form action="{{ route('bimtek.absensi.hapus-kehadiran', [$bimtek, $sesi, $absensi]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kehadiran peserta ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada kehadiran</h3>
                                <p class="text-gray-500">Belum ada peserta yang hadir pada sesi ini.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Peserta Belum Hadir (Only for PIC/Panitia) --}}
                    @if($canManage && $notAttendedPeserta->count() > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Belum Hadir
                                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $notAttendedPeserta->count() }} peserta)</span>
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($notAttendedPeserta as $index => $peserta)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-600">{{ strtoupper(substr($peserta->name, 0, 1)) }}</span>
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-900">{{ $peserta->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $peserta->email }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <form action="{{ route('bimtek.absensi.tambah-kehadiran', [$bimtek, $sesi]) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $peserta->id }}">
                                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                            Tambah Hadir
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column: Session Info --}}
                <div class="space-y-6">
                    {{-- Session Status --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Sesi</h3>
                        
                        @if($sesi->isOpen())
                            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        <p class="font-semibold text-green-900">Sesi Terbuka</p>
                                    </div>
                                    <p class="text-sm text-green-700">Peserta dapat melakukan absensi</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 mb-1">Sesi Ditutup</p>
                                    <p class="text-sm text-gray-600">Absensi tidak dapat dilakukan</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- QR Code Display (Only for PIC/Panitia) --}}
                    @if($canManage)
                        <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl shadow-sm border border-primary-200 p-6">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="inline-flex items-center justify-center w-10 h-10 bg-primary-600 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">QR Code Absensi</h3>
                                    <p class="text-sm text-gray-600">Tampilkan QR Code untuk peserta scan</p>
                                </div>
                            </div>

                            @if($sesi->isOpen())
                                <a href="{{ route('bimtek.absensi.show-qr', [$bimtek, $sesi]) }}" 
                                   target="_blank"
                                   class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium transition shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    Tampilkan QR Code
                                </a>
                                <p class="text-xs text-gray-600 mt-3 text-center">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    QR Code akan terbuka di tab baru. Tampilkan di layar/proyektor.
                                </p>
                            @else
                                <div class="w-full px-6 py-3 bg-gray-100 text-gray-500 rounded-lg font-medium text-center border border-gray-200">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Buka Sesi Untuk Generate QR
                                </div>
                                <p class="text-xs text-gray-500 mt-3 text-center">
                                    Klik tombol "Buka Sesi" di atas untuk mengaktifkan QR Code
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- Peserta Action (If peserta) --}}
                    @if($isPeserta)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Kehadiran Anda</h3>
                            
                            @if($hasAttended)
                                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-green-900 mb-1">Anda Sudah Hadir</p>
                                        <p class="text-sm text-green-700">Kehadiran Anda sudah tercatat dalam sistem</p>
                                    </div>
                                </div>
                            @else
                                <div>
                                    @if($bimtek->status_pelaksanaan !== 'berlangsung')
                                        {{-- Bimtek not active --}}
                                        <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 mb-1">Bimtek Tidak Aktif</p>
                                                <p class="text-sm text-gray-600">
                                                    @if($bimtek->status_pelaksanaan === 'persiapan')
                                                        Bimtek belum dimulai
                                                    @elseif($bimtek->status_pelaksanaan === 'selesai')
                                                        Bimtek sudah selesai
                                                    @else
                                                        Bimtek dibatalkan
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @elseif($sesi->isOpen())
                                        <div class="flex items-center gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-yellow-900 mb-1">Belum Hadir</p>
                                                <p class="text-sm text-yellow-700">Scan QR Code untuk mencatat kehadiran Anda</p>
                                            </div>
                                        </div>
                                        
                                        {{-- Scan QR Button (Primary) --}}
                                        <a href="{{ route('bimtek.absensi.scan-interface', [$bimtek, $sesi]) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            Scan QR Code
                                        </a>
                                    @else
                                        <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-red-900 mb-1">Sesi Sudah Ditutup</p>
                                                <p class="text-sm text-red-700">Hubungi panitia jika ada kendala dengan kehadiran Anda</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Session Info --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sesi</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Dibuat Oleh</p>
                                <p class="text-gray-900 font-medium">{{ $sesi->openedBy->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Waktu Dibuat</p>
                                <p class="text-gray-900">{{ $sesi->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Terakhir Diperbarui</p>
                                <p class="text-gray-900">{{ $sesi->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Kehadiran</h3>
                        
                        @php
                            $totalPeserta = $bimtek->peserta->count();
                            $hadir = $sesi->absensiPesertas->count();
                            $tidakHadir = $totalPeserta - $hadir;
                            $persentase = $totalPeserta > 0 ? round(($hadir / $totalPeserta) * 100, 1) : 0;
                        @endphp

                        <div class="space-y-4">
                            {{-- Progress Bar --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-500">Tingkat Kehadiran</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $persentase }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-primary-600 h-3 rounded-full" style="width: {{ $persentase }}%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div class="bg-green-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $hadir }}</p>
                                    <p class="text-xs text-green-700">Hadir</p>
                                </div>
                                <div class="bg-red-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-red-600">{{ $tidakHadir }}</p>
                                    <p class="text-xs text-red-700">Tidak Hadir</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Button --}}
                    @if($canManage)
                        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                            <h3 class="text-lg font-semibold text-red-600 mb-2">Zona Berbahaya</h3>
                            <p class="text-sm text-gray-500 mb-4">Menghapus sesi akan menghapus semua data kehadiran pada sesi ini.</p>
                            <form action="{{ route('bimtek.absensi.destroy', [$bimtek, $sesi]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus sesi absensi ini? Semua data kehadiran akan hilang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus Sesi
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
