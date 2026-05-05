<x-app-layout>
    <x-slot name="header">
        Review Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('approval.kepala.index') }}" class="text-gray-500 hover:text-primary-600">
                            Persetujuan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-700 font-medium">Review</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner --}}
            @php
                $statusColors = [
                    'diajukan' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
                    'disetujui_kepala' => 'bg-green-100 border-green-400 text-green-800',
                    'ditolak' => 'bg-red-100 border-red-400 text-red-800',
                    'perlu_revisi' => 'bg-orange-100 border-orange-400 text-orange-800',
                ];
                $statusLabels = [
                    'diajukan' => 'Menunggu Persetujuan Anda',
                    'disetujui_kepala' => 'Telah Anda Setujui',
                    'ditolak' => 'Telah Anda Tolak',
                    'perlu_revisi' => 'Dikembalikan untuk Revisi',
                ];
            @endphp
            <div class="mb-6 p-4 rounded-lg border-l-4 {{ $statusColors[$pengajuan->status_pengajuan] ?? 'bg-gray-100 border-gray-400 text-gray-800' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Status: {{ $statusLabels[$pengajuan->status_pengajuan] ?? $pengajuan->status_pengajuan }}</span>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Header --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $pengajuan->judul_rencana }}</h2>
                            <p class="mt-1 text-sm text-gray-500">Diajukan oleh {{ $pengajuan->user->name }} pada {{ $pengajuan->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($pengajuan->jenis_kegiatan) }}
                        </span>
                    </div>
                </div>

                {{-- Detail Info --}}
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tempat Kegiatan</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tempat_kegiatan ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Sumber Pembiayaan</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->sumber_pembiayaan ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tanggal Mulai</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tanggal_mulai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tanggal Selesai</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tanggal_selesai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Deskripsi Kegiatan</h4>
                        <div class="mt-1 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-900 whitespace-pre-line">{{ $pengajuan->deskripsi_rencana ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Rincian Anggaran Biaya (RAB) --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rincian Anggaran Biaya (RAB)
                        </h4>
                        @if($pengajuan->kebutuhanAnggarans->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Uraian Kebutuhan</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Kategori</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Total Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->kebutuhanAnggarans as $index => $anggaran)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $anggaran->nama_item }}</td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    @php
                                                        $kategoriColors = [
                                                            'Akomodasi' => 'bg-purple-100 text-purple-800',
                                                            'Konsumsi' => 'bg-green-100 text-green-800',
                                                            'Transportasi' => 'bg-blue-100 text-blue-800',
                                                            'Honorarium' => 'bg-yellow-100 text-yellow-800',
                                                            'ATK' => 'bg-pink-100 text-pink-800',
                                                            'Lainnya' => 'bg-gray-100 text-gray-800',
                                                        ];
                                                        $colorClass = $kategoriColors[$anggaran->kategori] ?? 'bg-gray-100 text-gray-800';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                                        {{ $anggaran->kategori }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 font-medium text-right">Rp {{ number_format($anggaran->total_biaya, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-green-50">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-800 text-right">Total Anggaran</td>
                                            <td class="px-4 py-3 text-base font-bold text-green-700 text-right">Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya'), 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-yellow-700 text-sm">Tidak ada rincian anggaran yang diajukan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Fasilitas & Logistik --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Kebutuhan Fasilitas & Logistik
                        </h4>
                        @if($pengajuan->fasilitasLogistiks->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Fasilitas</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Jumlah</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->fasilitasLogistiks as $index => $fasilitas)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $fasilitas->nama_fasilitas }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? '' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ $fasilitas->keterangan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-gray-500 text-sm">Tidak ada kebutuhan fasilitas & logistik.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Catatan sebelumnya jika ada --}}
                    @if($pengajuan->catatan_kepala && $pengajuan->status_pengajuan !== 'diajukan')
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Catatan Anda Sebelumnya</h4>
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-gray-800">{{ $pengajuan->catatan_kepala }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Action Forms --}}
                @if($pengajuan->status_pengajuan === 'diajukan')
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Tindakan</h4>
                    
                    <div x-data="{ action: null }" class="space-y-4">
                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-3">
                            <button @click="action = 'approve'" type="button" 
                                class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm transition"
                                :class="action === 'approve' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Setujui
                            </button>
                            <button @click="action = 'revisi'" type="button"
                                class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm transition"
                                :class="action === 'revisi' ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-700 hover:bg-orange-200'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Minta Revisi
                            </button>
                            <button @click="action = 'reject'" type="button"
                                class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm transition"
                                :class="action === 'reject' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak
                            </button>
                        </div>

                        {{-- Approve Form --}}
                        <form x-show="action === 'approve'" x-cloak action="{{ route('approval.kepala.approve', $pengajuan) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="catatan_approve" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                <textarea name="catatan" id="catatan_approve" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Konfirmasi Setujui
                            </button>
                        </form>

                        {{-- Revisi Form --}}
                        <form x-show="action === 'revisi'" x-cloak action="{{ route('approval.kepala.revisi', $pengajuan) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="catatan_revisi" class="block text-sm font-medium text-gray-700">Catatan Revisi <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_revisi" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Jelaskan apa yang perlu direvisi..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg font-semibold text-sm hover:bg-orange-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Kirim Permintaan Revisi
                            </button>
                        </form>

                        {{-- Reject Form --}}
                        <form x-show="action === 'reject'" x-cloak action="{{ route('approval.kepala.reject', $pengajuan) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="catatan_reject" class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_reject" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm hover:bg-red-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Konfirmasi Tolak
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('approval.kepala.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
