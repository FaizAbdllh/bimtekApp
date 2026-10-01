<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 leading-tight">
            Kebutuhan Fasilitas & Logistik
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        {{-- Header Halaman --}}
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800">Kebutuhan Fasilitas & Logistik</h3>
            <p class="mt-1 text-sm text-gray-500">Kelola pemenuhan prasarana, ruang aula, dan sarana lapangan untuk kegiatan bimtek</p>
        </div>

        {{-- Filter & Search Kartu --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('rt.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari judul kegiatan atau nama lokasi..." 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                    </div>
                    <div>
                        <select name="status" class="mt-1 block w-full md:w-48 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm bg-white">
                            <option value="">Semua Status RT</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('rt.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Statistics Quick Widgets (5 Kolom / 3 Kolom Kompak Standar) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Belum Dipenuhi</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-0.5">{{ $stats['belum_dipenuhi'] ?? 0 }}</p>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600 flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sebagian Dipenuhi</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-0.5">{{ $stats['sebagian_dipenuhi'] ?? 0 }}</p>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Telah Terpenuhi</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-0.5">{{ $stats['telah_dipenuhi'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- List Pengajuan / Tabel Kartu --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Penataan Logistik Masuk</h3>
                
                @if($pengajuans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Nama Kegiatan & Tempat</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">PIC Pengaju</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Tanggal Pelaksanaan</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Volume Logistik</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Status RT</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-gray-700 bg-white">
                                @foreach($pengajuans as $pengajuan)
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $pengajuan->judul_final ?? $pengajuan->judul_rencana }}</div>
                                            <div class="text-xs text-gray-500 mt-1">{{ $pengajuan->lokasi_aktual ?? $pengajuan->tempat_kegiatan_rencana ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-medium">
                                            {{ $pengajuan->pic->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            {{ $pengajuan->tanggal_mulai_aktual ? $pengajuan->tanggal_mulai_aktual->format('d/m/Y') : ($pengajuan->tanggal_mulai_rencana ? $pengajuan->tanggal_mulai_rencana->format('d/m/Y') : '-') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800">
                                                {{ $pengajuan->fasilitasLogistiks->count() }} Item
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $statusRtColors = [
                                                    'belum_dipenuhi' => 'bg-yellow-100 text-yellow-800',
                                                    'sebagian_dipenuhi' => 'bg-primary-100 text-primary-800',
                                                    'telah_dipenuhi' => 'bg-green-100 text-green-800',
                                                ];
                                                $statusRtLabels = [
                                                    'belum_dipenuhi' => 'Belum Dipenuhi',
                                                    'sebagian_dipenuhi' => 'Sebagian',
                                                    'telah_dipenuhi' => 'Terpenuhi',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusRtColors[$pengajuan->status_rt] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $statusRtLabels[$pengajuan->status_rt] ?? $pengajuan->status_rt }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('rt.show', $pengajuan->id) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 shadow-sm">
                                                Kelola Fasilitas
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Wrapper --}}
                    <div class="mt-6">
                        {{ $pengajuans->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mt-2 text-gray-500">Tidak ada daftar kebutuhan logistik sarana prasarana yang masuk.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>