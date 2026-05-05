<x-app-layout>
    <x-slot name="header">
        Daftar Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Bimtek</h2>
                <p class="mt-1 text-sm text-gray-600">Lihat semua kegiatan bimbingan teknis</p>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('bimtek.index') }}" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari judul atau lokasi..." 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Semua Status</option>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('bimtek.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
                    $stats = [
                        'persiapan' => App\Models\Bimtek::where('status_pelaksanaan', 'persiapan')->count(),
                        'berlangsung' => App\Models\Bimtek::where('status_pelaksanaan', 'berlangsung')->count(),
                        'selesai' => App\Models\Bimtek::where('status_pelaksanaan', 'selesai')->count(),
                        'total' => App\Models\Bimtek::count(),
                    ];
                @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Persiapan</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $stats['persiapan'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Berlangsung</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $stats['berlangsung'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100 text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Selesai</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $stats['selesai'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Total</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- List Bimtek --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($bimteks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($bimteks as $bimtek)
                                <div class="border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                                    <div class="p-4">
                                        {{-- Status Badge --}}
                                        <div class="flex justify-between items-start mb-3">
                                            @php
                                                $statusColors = [
                                                    'persiapan' => 'bg-yellow-100 text-yellow-800',
                                                    'berlangsung' => 'bg-blue-100 text-blue-800',
                                                    'selesai' => 'bg-green-100 text-green-800',
                                                    'dibatalkan' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$bimtek->status_pelaksanaan] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($bimtek->status_pelaksanaan) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ $bimtek->peserta->count() }} peserta
                                            </span>
                                        </div>

                                        {{-- Title --}}
                                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $bimtek->judul_final }}</h3>

                                        {{-- Info --}}
                                        <div class="space-y-1 text-sm text-gray-500 mb-4">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $bimtek->lokasi_aktual ?? 'Lokasi belum ditentukan' }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $bimtek->tanggal_mulai_aktual?->format('d M Y') ?? '-' }}
                                                @if($bimtek->tanggal_selesai_aktual && $bimtek->tanggal_mulai_aktual != $bimtek->tanggal_selesai_aktual)
                                                    - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                                @endif
                                            </div>
                                        </div>

                                        {{-- PIC --}}
                                        @if($bimtek->pic)
                                            <div class="text-xs text-gray-500 mb-3">
                                                <span class="font-medium">PIC:</span> {{ $bimtek->pic->name }}
                                            </div>
                                        @endif

                                        {{-- Action --}}
                                        <a href="{{ route('bimtek.show', $bimtek) }}" class="block w-full text-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm font-medium">
                                            Lihat Detail
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
                            <p class="mt-2 text-gray-500">Belum ada bimtek.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
