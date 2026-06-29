<x-app-layout>
    <x-slot name="header">
        Persetujuan Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Pengajuan Bimtek (Kepala Balai)</h2>
                    <p class="mt-1 text-sm text-gray-600">Review, berikan disposisi, dan setujui pengajuan perencanaan bimbingan teknis</p>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('approval.kepala.index') }}" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="sr-only">Cari</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari judul kegiatan atau nama PIC pengaju..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-sm">
                            </div>
                        </div>
                        <div class="sm:w-48">
                            <label for="status" class="sr-only">Filter Status</label>
                            <select name="status" id="status" class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-sm bg-white">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-gray-700 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none transition">
                                Filter
                            </button>
                            @if (request('search') || request('status'))
                                <a href="{{ route('approval.kepala.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table List Usulan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Kegiatan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC Pengaju</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kegiatan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Alur</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pengajuans as $pengajuan)
                                {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
                                <tr class="hover:bg-gray-50 transition-colors {{ $pengajuan->status === 'diajukan' ? 'bg-yellow-50/60' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $pengajuan->judul_rencana }}</div>
                                        {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                                        <div class="text-xs text-gray-500 mt-1 font-medium">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                                        <div class="text-sm font-semibold text-gray-900">{{ $pengajuan->pic->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5 font-medium">Dibuat: {{ $pengajuan->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $pengajuan->tanggal_mulai_rencana?->format('d M Y') ?? '-' }}
                                        </div>
                                        @if($pengajuan->tanggal_selesai_rencana)
                                        <div class="text-xs text-gray-400 font-medium mt-0.5">
                                            s/d {{ $pengajuan->tanggal_selesai_rencana->format('d M Y') }}
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            // REFAKTORISASI: Menyelaraskan skema warna badge alur persetujuan baru
                                            $statusColors = [
                                                'diajukan' => 'bg-yellow-100 text-yellow-800',
                                                'disetujui_kepala' => 'bg-blue-100 text-blue-800',
                                                'disetujui_ppk' => 'bg-indigo-100 text-indigo-800',
                                                'disetujui_final' => 'bg-green-100 text-green-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                'perlu_revisi' => 'bg-orange-100 text-orange-800',
                                            ];
                                        @endphp
                                        {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$pengajuan->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusOptions[$pengajuan->status] ?? $pengajuan->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- REFAKTORISASI: Menyematkan ID eksplisit pada route param untuk jaminan kelancaran UUID --}}
                                        <a href="{{ route('approval.kepala.show', $pengajuan->id) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg transition text-xs shadow-sm">
                                            @if($pengajuan->status === 'diajukan')
                                                Review Usulan
                                            @else
                                                Lihat Ringkasan
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada berkas masuk</h3>
                                        <p class="mt-1 text-xs text-gray-500 font-medium">Saat ini belum ada pengajuan masuk yang membutuhkan peninjauan Kepala Balai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Nav --}}
                @if ($pengajuans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>