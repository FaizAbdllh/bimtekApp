<x-app-layout>
    <x-slot name="header">
        Pengajuan Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header dengan Tombol Tambah --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Daftar Pengajuan</h2>
                    <p class="mt-1 text-sm text-gray-600">Kelola pengajuan kegiatan bimbingan teknis dan status verifikasinya</p>
                </div>
                @if(Auth::user()->isPegawaiInternal())
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('pengajuan.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Ajukan Bimtek
                    </a>
                </div>
                @endif
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('pengajuan.index') }}" class="flex flex-col lg:flex-row gap-4 lg:items-center">
                        <div class="flex-1">
                            <label for="search" class="sr-only">Cari</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari judul atau tempat kegiatan..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="lg:w-56">
                            <label for="status" class="sr-only">Filter Status</label>
                            <select name="status" id="status" class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:w-44">
                            <label for="jenis" class="sr-only">Filter Jenis</label>
                            <select name="jenis" id="jenis" class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="">Semua Jenis</option>
                                <option value="internal" {{ request('jenis') == 'internal' ? 'selected' : '' }}>Internal</option>
                                <option value="eksternal" {{ request('jenis') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                            </select>
                        </div>
                        <div class="flex gap-2 lg:self-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gray-700 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                            @if (request('search') || request('status') || request('jenis'))
                                <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-semibold text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Kegiatan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC Pengaju</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis & Verifikasi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Rencana</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Alur</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pengajuans as $pengajuan)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $pengajuan->judul_rencana }}</div>
                                        {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                                        <div class="text-sm text-gray-500 mt-1">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                                        <div class="text-sm text-gray-900 font-medium">{{ $pengajuan->pic->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-fit {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Internal' : 'Eksternal' }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-fit {{ $pengajuan->butuh_verifikasi_dokumen ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $pengajuan->butuh_verifikasi_dokumen ? 'Verifikasi Aktif' : 'Verifikasi Nonaktif' }}
                                            </span>
                                        </div>
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
                                            // REFAKTORISASI: Memperbarui peta status dengan memetakan 'draft' lama menjadi 'draft_pic'
                                            $statusColors = [
                                                'draft_pic' => 'bg-gray-100 text-gray-800',
                                                'diajukan' => 'bg-yellow-100 text-yellow-800',
                                                'disetujui_kepala' => 'bg-blue-100 text-blue-800',
                                                'disetujui_ppk' => 'bg-indigo-100 text-indigo-800',
                                                'disetujui_final' => 'bg-green-100 text-green-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                'perlu_revisi' => 'bg-orange-100 text-orange-800',
                                            ];
                                            $statusLabels = [
                                                'draft_pic' => 'Draft',
                                                'diajukan' => 'Diajukan',
                                                'disetujui_kepala' => 'Disetujui Kepala',
                                                'disetujui_ppk' => 'Disetujui PPK',
                                                'disetujui_final' => 'Disetujui Final',
                                                'ditolak' => 'Ditolak',
                                                'perlu_revisi' => 'Perlu Revisi',
                                            ];
                                        @endphp
                                        {{-- REFAKTORISASI: Mengubah properti status_pengajuan menjadi status --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$pengajuan->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$pengajuan->status] ?? $pengajuan->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('pengajuan.show', $pengajuan->id) }}" class="text-primary-600 hover:text-primary-900" title="Lihat Detail">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            {{-- REFAKTORISASI: Menyelaraskan user_id menjadi pic_user_id, status_pengajuan menjadi status, serta key 'draft_pic' --}}
                                            @if((Auth::id() === $pengajuan->pic_user_id || Auth::user()->isAdminIt()) && in_array($pengajuan->status, ['draft_pic', 'diajukan', 'perlu_revisi']))
                                            <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('pengajuan.destroy', $pengajuan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengajuan</h3>
                                        <p class="mt-1 text-sm text-gray-500">Mulai dengan mengajukan kegiatan bimtek baru.</p>
                                        @if(Auth::user()->isPegawaiInternal())
                                        <div class="mt-4">
                                            <a href="{{ route('pengajuan.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-700">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Ajukan Bimtek
                                            </a>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($pengajuans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>