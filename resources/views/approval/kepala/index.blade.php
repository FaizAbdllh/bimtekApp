<x-app-layout>
    <x-slot name="header">
        Persetujuan Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Pengajuan Bimtek</h2>
                    <p class="mt-1 text-sm text-gray-600">Review dan setujui pengajuan kegiatan bimbingan teknis</p>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
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
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari judul atau nama pengaju..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="sm:w-48">
                            <label for="status" class="sr-only">Filter Status</label>
                            <select name="status" id="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                Filter
                            </button>
                            @if (request('search') || request('status'))
                                <a href="{{ route('approval.kepala.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-300 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Kegiatan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pengajuans as $pengajuan)
                                <tr class="hover:bg-gray-50 {{ $pengajuan->status_pengajuan === 'diajukan' ? 'bg-yellow-50' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $pengajuan->judul_rencana }}</div>
                                        <div class="text-sm text-gray-500">{{ $pengajuan->tempat_kegiatan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pengajuan->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $pengajuan->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $pengajuan->tanggal_mulai_rencana?->format('d M Y') ?? '-' }}
                                        </div>
                                        @if($pengajuan->tanggal_selesai_rencana)
                                        <div class="text-sm text-gray-500">
                                            s/d {{ $pengajuan->tanggal_selesai_rencana->format('d M Y') }}
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'diajukan' => 'bg-yellow-100 text-yellow-800',
                                                'disetujui_kepala' => 'bg-green-100 text-green-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                'perlu_revisi' => 'bg-orange-100 text-orange-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$pengajuan->status_pengajuan] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusOptions[$pengajuan->status_pengajuan] ?? $pengajuan->status_pengajuan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('approval.kepala.show', $pengajuan) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm">
                                            @if($pengajuan->status_pengajuan === 'diajukan')
                                                Review
                                            @else
                                                Lihat
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
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengajuan</h3>
                                        <p class="mt-1 text-sm text-gray-500">Belum ada pengajuan yang perlu direview.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pengajuans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
