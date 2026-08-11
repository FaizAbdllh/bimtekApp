<x-app-layout>
    <x-slot name="header">
        Kebutuhan Fasilitas & Logistik
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Kebutuhan Fasilitas & Logistik</h2>
                <p class="mt-1 text-sm text-gray-600">Kelola pemenuhan prasarana, ruang aula, dan sarana lapangan untuk kegiatan bimtek</p>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('rt.index') }}" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari judul kegiatan atau nama lokasi..." 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5">
                        </div>
                        <div>
                            <select name="status" class="w-full md:w-48 rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 bg-white">
                                <option value="">Semua Status RT</option>
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
                                <a href="{{ route('rt.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Statistics Quick Widgets --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Belum Dipenuhi</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $stats['belum_dipenuhi'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sebagian Dipenuhi</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $stats['sebagian_dipenuhi'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Telah Terpenuhi</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $stats['telah_dipenuhi'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- List Pengajuan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-4">Daftar Penataan Logistik Masuk</h3>
                    
                    @if($pengajuans->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase">
                                    <tr>
                                        <th class="px-6 py-3 text-left tracking-wider">Nama Kegiatan & Tempat</th>
                                        <th class="px-6 py-3 text-left tracking-wider">PIC Pengaju</th>
                                        <th class="px-6 py-3 text-center tracking-wider">Tanggal Pelaksanaan</th>
                                        <th class="px-6 py-3 text-center tracking-wider">Volume Logistik</th>
                                        <th class="px-6 py-3 text-center tracking-wider">Status RT</th>
                                        <th class="px-6 py-3 text-center tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                    @foreach($pengajuans as $pengajuan)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ $pengajuan->judul_final ?? $pengajuan->judul_rencana }}</div>
                                                {{-- REFAKTORISASI LOKASI: Menggunakan lokasi_aktual dengan fallback tempat_kegiatan_rencana --}}
                                                <div class="text-xs text-gray-400 mt-1 font-medium">{{ $pengajuan->lokasi_aktual ?? $pengajuan->tempat_kegiatan_rencana ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">
                                                {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                                                {{ $pengajuan->pic->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center font-medium text-gray-900">
                                                {{ $pengajuan->tanggal_mulai_aktual ? $pengajuan->tanggal_mulai_aktual->format('d/m/Y') : ($pengajuan->tanggal_mulai_rencana ? $pengajuan->tanggal_mulai_rencana->format('d/m/Y') : '-') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-md text-xs font-bold">
                                                    {{ $pengajuan->fasilitasLogistiks->count() }} Item Permintaan
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    $statusRtColors = [
                                                        'belum_dipenuhi' => 'bg-yellow-100 text-yellow-800',
                                                        'sebagian_dipenuhi' => 'bg-blue-100 text-blue-800',
                                                        'telah_dipenuhi' => 'bg-green-100 text-green-800',
                                                    ];
                                                    $statusRtLabels = [
                                                        'belum_dipenuhi' => 'Belum Dipenuhi',
                                                        'sebagian_dipenuhi' => 'Sebagian',
                                                        'telah_dipenuhi' => 'Terpenuhi',
                                                    ];
                                                @endphp
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusRtColors[$pengajuan->status_rt] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusRtLabels[$pengajuan->status_rt] ?? $pengajuan->status_rt }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                {{-- REFAKTORISASI: Menyematkan properti ID eksplisit untuk kestabilan UUID --}}
                                                <a href="{{ route('rt.show', $pengajuan->id) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white text-xs font-bold rounded-lg hover:bg-primary-700 transition shadow-sm">
                                                    Kelola Fasilitas
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $pengajuans->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="mt-4 text-sm font-semibold text-gray-500">Tidak ada daftar kebutuhan logistik sarana prasarana yang masuk.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>