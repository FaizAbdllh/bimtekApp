<x-app-layout>
    <x-slot name="header">
        Daftar Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Bimtek</h2>
                <p class="mt-1 text-sm text-gray-600">Lihat semua daftar kelas dan pelaksanaan kegiatan bimbingan teknis aktif</p>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('bimtek.index') }}" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari judul bimtek atau lokasi pelaksanaan..." 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5">
                        </div>
                        <div>
                            <select name="status" class="w-full md:w-48 rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 bg-white">
                                <option value="">Semua Status</option>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-gray-700 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('bimtek.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @php
                    // REFAKTORISASI: Menyelaraskan query statistik langsung ke nama kolom baru 'status'
                    $stats = [
                        'persiapan' => App\Models\Bimtek::where('status', 'persiapan')->count(),
                        'berlangsung' => App\Models\Bimtek::where('status', 'berlangsung')->count(),
                        'selesai' => App\Models\Bimtek::where('status', 'selesai')->count(),
                        'total' => App\Models\Bimtek::whereIn('status', ['disetujui_final', 'persiapan', 'berlangsung', 'selesai'])->count(),
                    ];
                @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-yellow-100 text-yellow-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Persiapan</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $stats['persiapan'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-100 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Berlangsung</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $stats['berlangsung'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-green-100 text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Selesai</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $stats['selesai'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-purple-100 text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Bimtek</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- List Bimtek --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    @if($bimteks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($bimteks as $bimtek)
                                <div class="border border-gray-200 rounded-2xl hover:shadow-md transition-shadow bg-white overflow-hidden">
                                    <div class="p-5">
                                        {{-- Status Badge --}}
                                        <div class="flex justify-between items-center mb-3">
                                            @php
                                                // REFAKTORISASI: Menyesuaikan status_pelaksanaan menjadi status terpadu baru
                                                $statusColors = [
                                                    'disetujui_final' => 'bg-gray-100 text-gray-800',
                                                    'persiapan' => 'bg-yellow-100 text-yellow-800',
                                                    'berlangsung' => 'bg-blue-100 text-blue-800',
                                                    'selesai' => 'bg-green-100 text-green-800',
                                                    'dibatalkan' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusLabels = [
                                                    'disetujui_final' => 'Disetujui',
                                                    'persiapan' => 'Persiapan',
                                                    'berlangsung' => 'Berlangsung',
                                                    'selesai' => 'Selesai',
                                                    'dibatalkan' => 'Batal',
                                                ];
                                            @endphp
                                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $statusColors[$bimtek->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $statusLabels[$bimtek->status] ?? ucfirst($bimtek->status) }}
                                            </span>
                                            <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-md">
                                                {{ $bimtek->peserta->count() }} Peserta
                                            </span>
                                        </div>

                                        {{-- Title --}}
                                        {{-- REFAKTORISASI: Tambahkan fallback aman jika judul_final belum diisi oleh panitia --}}
                                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 h-12 leading-snug">
                                            {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                                        </h3>

                                        {{-- Info Lokasi & Waktu --}}
                                        <div class="space-y-2 text-sm text-gray-500 mb-4 pt-1">
                                            {{-- REFAKTORISASI LOKASI: Menggunakan lokasi_aktual, jika kosong fallback ke rencana awal --}}
                                            <div class="flex items-start">
                                                <svg class="w-4 h-4 mr-2 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate">{{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? 'Lokasi belum ditentukan' }}</span>
                                            </div>
                                            {{-- REFAKTORISASI WAKTU: Menggunakan tanggal_mulai_aktual, jika kosong fallback ke rencana awal --}}
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>
                                                    @if($bimtek->tanggal_mulai_aktual)
                                                        {{ $bimtek->tanggal_mulai_aktual->format('d M Y') }}
                                                        @if($bimtek->tanggal_selesai_aktual && $bimtek->tanggal_mulai_aktual != $bimtek->tanggal_selesai_aktual)
                                                            - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                                        @endif
                                                    @else
                                                        {{ $bimtek->tanggal_mulai_rencana?->format('d M Y') ?? '-' }}
                                                        @if($bimtek->tanggal_selesai_rencana && $bimtek->tanggal_mulai_rencana != $bimtek->tanggal_selesai_rencana)
                                                            - {{ $bimtek->tanggal_selesai_rencana->format('d M Y') }}
                                                        @endif
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        {{-- PIC Penanggung Jawab --}}
                                        @if($bimtek->pic)
                                            <div class="text-xs text-gray-500 mb-4 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100">
                                                <span class="font-bold text-gray-600">PIC Kelas:</span> {{ $bimtek->pic->name }}
                                            </div>
                                        @endif

                                        {{-- Action Button --}}
                                        <a href="{{ route('bimtek.show', $bimtek->id) }}" class="block w-full text-center px-4 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition text-sm font-semibold shadow-sm">
                                            Masuk Ruang Kelas
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $bimteks->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="mt-2 text-base font-medium text-gray-500">Tidak ada data pelaksanaan bimbingan teknis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>