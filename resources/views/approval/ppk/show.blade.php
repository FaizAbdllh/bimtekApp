<x-app-layout>
    <x-slot name="header">
        Review Anggaran
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('approval.ppk.index') }}" class="text-gray-500 hover:text-primary-600">
                            Persetujuan Anggaran
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
                    'disetujui_kepala' => 'bg-blue-100 border-blue-400 text-blue-800',
                    'disetujui_ppk' => 'bg-indigo-100 border-indigo-400 text-indigo-800',
                    'disetujui_final' => 'bg-green-100 border-green-400 text-green-800',
                ];
                $statusLabels = [
                    'disetujui_kepala' => 'Menunggu Persetujuan Anggaran',
                    'disetujui_ppk' => 'Telah Disetujui PPK',
                    'disetujui_final' => 'Disetujui Final - Bimtek Dibuat',
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

                    {{-- Catatan Kepala --}}
                    @if($pengajuan->catatan_kepala)
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Catatan dari Kepala</h4>
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-gray-800">{{ $pengajuan->catatan_kepala }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Rincian Anggaran Biaya (RAB) - WAJIB untuk PPK --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rincian Anggaran Biaya (RAB)
                        </h4>
                        @if($pengajuan->kebutuhanAnggarans->count() > 0)
                            {{-- SBM Compliance Alert --}}
                            @php
                                $needsApproval = $pengajuan->kebutuhanAnggarans->filter(fn($item) => $item->needsApproval());
                            @endphp
                            @if($needsApproval->count() > 0)
                                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800">Perhatian: Deviasi SBM Terdeteksi</p>
                                            <p class="text-xs text-yellow-700 mt-1">Terdapat {{ $needsApproval->count() }} item dengan deviasi harga ≥10% dari SBM. Harap review dengan teliti.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase w-10">No</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Uraian</th>
                                            <th class="px-3 py-2 text-center font-semibold text-gray-600 uppercase w-28">Volume</th>
                                            <th class="px-3 py-2 text-right font-semibold text-gray-600 uppercase w-32">Harga Sat.</th>
                                            <th class="px-3 py-2 text-right font-semibold text-gray-600 uppercase w-32">Total</th>
                                            <th class="px-3 py-2 text-center font-semibold text-gray-600 uppercase w-36">Status SBM</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->kebutuhanAnggarans as $index => $anggaran)
                                            <tr class="hover:bg-gray-50 {{ $anggaran->needsApproval() ? 'bg-yellow-50' : '' }}">
                                                <td class="px-3 py-2 text-gray-600 text-center">{{ $index + 1 }}</td>
                                                <td class="px-3 py-2">
                                                    <div class="font-medium text-gray-900">{{ $anggaran->nama_item }}</div>
                                                    @php
                                                        $kategoriColors = [
                                                            'honor' => 'bg-purple-100 text-purple-800',
                                                            'transportasi' => 'bg-blue-100 text-blue-800',
                                                            'akomodasi' => 'bg-indigo-100 text-indigo-800',
                                                            'konsumsi' => 'bg-yellow-100 text-yellow-800',
                                                            'atk' => 'bg-green-100 text-green-800',
                                                            'sewa' => 'bg-pink-100 text-pink-800',
                                                        ];
                                                        $colorClass = $kategoriColors[$anggaran->kategori] ?? 'bg-gray-100 text-gray-800';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colorClass }} mt-1">
                                                        {{ ucfirst($anggaran->kategori) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <div class="text-sm text-gray-900">
                                                        {{ $anggaran->volume_1 }} {{ $anggaran->satuan_primary }}
                                                        @if($anggaran->volume_2 > 1 || $anggaran->satuan_secondary)
                                                            <br>× {{ $anggaran->volume_2 }} {{ $anggaran->satuan_secondary }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 text-right">
                                                    <div class="text-sm text-gray-900">Rp {{ number_format($anggaran->harga_satuan, 0, ',', '.') }}</div>
                                                    @if($anggaran->harga_satuan_sbm && $anggaran->harga_satuan != $anggaran->harga_satuan_sbm)
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            SBM: Rp {{ number_format($anggaran->harga_satuan_sbm, 0, ',', '.') }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-gray-900 font-medium text-right">
                                                    Rp {{ number_format($anggaran->total_biaya, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    @if($anggaran->status_validasi)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                            @switch($anggaran->status_validasi)
                                                                @case('sesuai_sbm') bg-green-100 text-green-800 @break
                                                                @case('deviasi_minor') bg-blue-100 text-blue-800 @break
                                                                @case('deviasi_major') bg-yellow-100 text-yellow-800 @break
                                                                @case('deviasi_signifikan') bg-orange-100 text-orange-800 @break
                                                                @case('non_sbm') bg-gray-100 text-gray-800 @break
                                                            @endswitch
                                                        ">
                                                            @switch($anggaran->status_validasi)
                                                                @case('sesuai_sbm') ✓ Sesuai @break
                                                                @case('deviasi_minor') Minor @break
                                                                @case('deviasi_major') ⚠ Major @break
                                                                @case('deviasi_signifikan') ⚠ Signifikan @break
                                                                @case('non_sbm') Non-SBM @break
                                                            @endswitch
                                                            @if($anggaran->persentase_deviasi)
                                                                ({{ number_format($anggaran->persentase_deviasi, 1) }}%)
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-green-50">
                                        <tr>
                                            <td colspan="4" class="px-3 py-3 font-bold text-gray-800 text-right">Total Anggaran</td>
                                            <td class="px-3 py-3 font-bold text-green-700 text-right">Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya'), 0, ',', '.') }}</td>
                                            <td></td>
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

                    {{-- Ringkasan Fasilitas & Logistik (detail akan ditangani RT) --}}
                    @if($pengajuan->fasilitasLogistiks->count() > 0)
                    <div class="border-t border-gray-200 pt-6">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Kebutuhan Fasilitas & Logistik: <span class="font-bold">{{ $pengajuan->fasilitasLogistiks->count() }} item</span></p>
                                    <p class="text-xs text-blue-600 mt-0.5">* Detail fasilitas akan ditangani oleh Koordinator RT setelah persetujuan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Catatan PPK --}}
                    @if($pengajuan->catatan_ppk)
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Catatan PPK</h4>
                        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                            <p class="text-gray-800">{{ $pengajuan->catatan_ppk }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Action Forms --}}
                @if($pengajuan->status_pengajuan === 'disetujui_kepala')
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
                                Setujui Anggaran
                            </button>
                            <button @click="action = 'revisi'" type="button"
                                class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm transition"
                                :class="action === 'revisi' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Minta Revisi ke Pengaju
                            </button>
                            <button @click="action = 'reject'" type="button"
                                class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm transition"
                                :class="action === 'reject' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Pengajuan
                            </button>
                        </div>

                        {{-- Approve Form --}}
                        <form x-show="action === 'approve'" x-cloak action="{{ route('approval.ppk.approve', $pengajuan) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800">
                                    <strong>Perhatian:</strong> Dengan menyetujui anggaran ini, kegiatan Bimtek akan otomatis dibuat dan siap dikelola.
                                </p>
                            </div>
                            <div>
                                <label for="catatan_approve" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                <textarea name="catatan" id="catatan_approve" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Konfirmasi Setujui Anggaran
                            </button>
                        </form>

                        {{-- Revisi Form --}}
                        <form x-show="action === 'revisi'" x-cloak action="{{ route('approval.ppk.revisi', $pengajuan) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">
                                    <strong>Info:</strong> Pengajuan akan dikembalikan ke pengaju untuk direvisi. Setelah direvisi, pengajuan akan langsung kembali ke PPK untuk ditinjau kembali (tidak perlu melalui Kepala lagi karena sudah disetujui sebelumnya).
                                </p>
                            </div>
                            <div>
                                <label for="catatan_revisi" class="block text-sm font-medium text-gray-700">Catatan Revisi <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_revisi" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Jelaskan apa yang perlu direvisi (contoh: anggaran terlalu besar, kurangi volume, dll)..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg font-semibold text-sm hover:bg-orange-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Konfirmasi Minta Revisi
                            </button>
                        </form>

                        {{-- Reject Form --}}
                        <form x-show="action === 'reject'" x-cloak action="{{ route('approval.ppk.reject', $pengajuan) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-800">
                                    <strong>Perhatian:</strong> Pengajuan akan ditolak secara permanen. Pengaju harus membuat pengajuan baru jika ingin mengajukan kembali.
                                </p>
                            </div>
                            <div>
                                <label for="catatan_reject" class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_reject" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Jelaskan alasan penolakan pengajuan..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm hover:bg-red-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Konfirmasi Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('approval.ppk.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
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
