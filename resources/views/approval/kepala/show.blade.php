<x-app-layout>
    <x-slot name="header">
        Review Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('approval.kepala.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium transition-colors">
                            Persetujuan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-medium">Review</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner --}}
            @php
                $statusColors = [
                    'diajukan' => 'bg-yellow-50 border-yellow-100 text-yellow-800',
                    'disetujui_kepala' => 'bg-green-50 border-green-100 text-green-800',
                    'disetujui_ppk' => 'bg-green-50 border-green-100 text-green-800',
                    'disetujui_final' => 'bg-green-50 border-green-100 text-green-800',
                    'ditolak' => 'bg-red-50 border-red-100 text-red-800',
                    'perlu_revisi' => 'bg-orange-50 border-orange-100 text-orange-800',
                ];
                $statusLabels = [
                    'diajukan' => 'Menunggu Persetujuan / Disposisi Anda',
                    'disetujui_kepala' => 'Telah Anda Setujui - Menunggu Peninjauan PPK',
                    'disetujui_ppk' => 'Telah Disetujui PPK',
                    'disetujui_final' => 'Telah Disetujui Final',
                    'ditolak' => 'Telah Anda Tolak',
                    'perlu_revisi' => 'Telah Anda Kembalikan untuk Revisi Berkas',
                ];
            @endphp
            {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
            <div class="mb-6 p-3 rounded-lg border text-sm flex items-start {{ $statusColors[$pengajuan->status] ?? 'bg-gray-50 border-gray-200 text-gray-800' }}">
                <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <span class="font-semibold">Status Alur Birokrasi:</span> {{ $statusLabels[$pengajuan->status] ?? $pengajuan->status }}
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                {{-- Card Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-primary-800">{{ $pengajuan->judul_rencana }}</h2>
                            {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                            <p class="mt-1 text-sm text-gray-500">Diajukan oleh <span class="text-gray-700 font-semibold">{{ $pengajuan->pic->name ?? '-' }}</span> pada {{ $pengajuan->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-semibold w-fit {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                            Target Sasaran: {{ ucfirst($pengajuan->jenis_kegiatan) }}
                        </span>
                    </div>
                </div>

                {{-- Detail Informasi Pokok Perencanaan --}}
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rencana Lokasi Kegiatan</h4>
                            {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber Pembiayaan / DIPA</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->sumber_pembiayaan ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rencana Tanggal Mulai</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tanggal_mulai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rencana Tanggal Selesai</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tanggal_selesai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-2">Latar Belakang & Deskripsi Singkat Kegiatan</h4>
                        <div class="p-4 bg-white rounded-lg border border-gray-100 text-sm leading-relaxed text-gray-700 whitespace-pre-line">
                            {{ $pengajuan->deskripsi_rencana ?? 'Tidak ada deskripsi.' }}
                        </div>
                    </div>

                    {{-- Lembar Telaah Komponen Anggaran Biaya (RAB) --}}
                    @if($pengajuan->kebutuhanAnggarans->count() > 0)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-4">Rincian Komponen Anggaran Belanja (RAB SBM)</h4>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Komponen Belanja</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Vol 1</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan 1</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Vol 2</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan 2</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pengajuan->kebutuhanAnggarans as $i => $anggaran)
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $anggaran->nama_item }}</td>
                                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-900">{{ $anggaran->volume_1 ?? '-' }}</td>
                                        {{-- REFAKTORISASI: Mengubah satuan_primary menjadi satuan_1 --}}
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $anggaran->satuan_1 ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-900">{{ $anggaran->volume_2 ?? '-' }}</td>
                                        {{-- REFAKTORISASI: Mengubah satuan_secondary menjadi satuan_2 --}}
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $anggaran->satuan_2 ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">Rp {{ number_format($anggaran->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Rp {{ number_format($anggaran->total_biaya ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 text-right min-w-[240px]">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Usulan Anggaran Pagu</p>
                                <p class="text-2xl font-semibold text-primary-600">Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya'), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Matriks Kebutuhan Logistik Lapangan Rumah Tangga --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-4">Daftar Permintaan Sarana Fasilitas / Logistik Rumah Tangga</h4>
                        @if($pengajuan->fasilitasLogistiks->count() > 0)
                            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Komponen Prasarana</th>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Jumlah Permintaan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->fasilitasLogistiks as $index => $fasilitas)
                                            <tr class="hover:bg-gray-100 transition-colors">
                                                <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $fasilitas->nama_fasilitas }}</td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">{{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? 'Unit' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 bg-white border border-gray-100 rounded-lg text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="mt-2 text-gray-500">Tidak melampirkan permintaan fasilitas sarana gedung Rumah Tangga.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Tinjauan Riwayat Catatan Disposisi Sebelumnya --}}
                    {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
                    @if($pengajuan->catatan_kepala && $pengajuan->status !== 'diajukan')
                    <div class="rounded-lg border border-primary-200 bg-primary-50 p-5">
                        <h4 class="text-base font-bold text-primary-800 mb-2">Catatan Disposisi Anda Sebelumnya</h4>
                        <div class="p-4 bg-white border border-primary-100 rounded-lg text-sm leading-relaxed text-gray-700">
                            {{ $pengajuan->catatan_kepala }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Form Lembar Tindakan Disposisi Kepala Balai (Hanya muncul jika status 'diajukan') --}}
                {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status dan menyematkan ID eksplisit pada route --}}
                @if($pengajuan->status === 'diajukan')
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-4">Lembar Disposisi Kepala Balai</h4>
                    
                    <div x-data="{ action: null }" class="space-y-5">
                        {{-- Opsi Tombol Tindakan BPMN State --}}
                        <div class="flex flex-wrap gap-3">
                            <button @click="action = 'approve'" type="button" 
                                class="inline-flex items-center px-4 py-2 border text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'approve' ? 'bg-green-700 border-transparent text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Setujui Usulan
                            </button>
                            <button @click="action = 'revisi'" type="button"
                                class="inline-flex items-center px-4 py-2 border text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'revisi' ? 'bg-orange-700 border-transparent text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Kembalikan (Revisi)
                            </button>
                            <button @click="action = 'reject'" type="button"
                                class="inline-flex items-center px-4 py-2 border text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'reject' ? 'bg-red-600 border-transparent text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Usulan
                            </button>
                        </div>

                        {{-- Form Submit - Setuju --}}
                        <form x-show="action === 'approve'" x-cloak action="{{ route('approval.kepala.approve', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-5">
                            @csrf
                            <div>
                                <label for="catatan_approve" class="block text-sm font-semibold text-gray-700">Catatan Disposisi Tambahan (Opsional)</label>
                                <textarea name="catatan" id="catatan_approve" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Tambahkan instruksi strategis khusus kepanitiaan jika diperlukan..."></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Sahkan Disposisi Setuju
                            </button>
                        </form>

                        {{-- Form Submit - Minta Revisi --}}
                        <form x-show="action === 'revisi'" x-cloak action="{{ route('approval.kepala.revisi', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-5">
                            @csrf
                            <div>
                                <label for="catatan_revisi" class="block text-sm font-semibold text-gray-700">Rincian Instruksi Koreksi Dokumen <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_revisi" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Jelaskan secara rinci komponen materi atau logistik apa yang perlu diperbaiki PIC..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Kembalikan Berkas ke PIC
                            </button>
                        </form>

                        {{-- Form Submit - Tolak Usulan --}}
                        <form x-show="action === 'reject'" x-cloak action="{{ route('approval.kepala.reject', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-5">
                            @csrf
                            <div>
                                <label for="catatan_reject" class="block text-sm font-semibold text-gray-700">Alasan Penolakan Permanen Usulan Kegiatan <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_reject" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Tuliskan alasan penolakan rasional berdasarkan hasil evaluasi DIPA instansi..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Tolak Usulan Kegiatan Permanen
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('approval.kepala.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar Peninjauan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout><x-app-layout>
    <x-slot name="header">
        Review Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('approval.kepala.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium transition-colors">
                            Persetujuan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-medium">Review</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner --}}
            @php
                $statusColors = [
                    'diajukan' => 'bg-yellow-50 border-yellow-100 text-yellow-800',
                    'disetujui_kepala' => 'bg-green-50 border-green-100 text-green-800',
                    'disetujui_ppk' => 'bg-green-50 border-green-100 text-green-800',
                    'disetujui_final' => 'bg-green-50 border-green-100 text-green-800',
                    'ditolak' => 'bg-red-50 border-red-100 text-red-800',
                    'perlu_revisi' => 'bg-orange-50 border-orange-100 text-orange-800',
                ];
                $statusLabels = [
                    'diajukan' => 'Menunggu Persetujuan / Disposisi Anda',
                    'disetujui_kepala' => 'Telah Anda Setujui - Menunggu Peninjauan PPK',
                    'disetujui_ppk' => 'Telah Disetujui PPK',
                    'disetujui_final' => 'Telah Disetujui Final',
                    'ditolak' => 'Telah Anda Tolak',
                    'perlu_revisi' => 'Telah Anda Kembalikan untuk Revisi Berkas',
                ];
            @endphp
            {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
            <div class="mb-6 p-3 rounded-lg border text-sm flex items-start {{ $statusColors[$pengajuan->status] ?? 'bg-gray-50 border-gray-200 text-gray-800' }}">
                <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <span class="font-semibold">Status Alur Birokrasi:</span> {{ $statusLabels[$pengajuan->status] ?? $pengajuan->status }}
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                {{-- Card Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-primary-800">{{ $pengajuan->judul_rencana }}</h2>
                            {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                            <p class="mt-1 text-sm text-gray-500">Diajukan oleh <span class="text-gray-700 font-semibold">{{ $pengajuan->pic->name ?? '-' }}</span> pada {{ $pengajuan->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-semibold w-fit {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                            Target Sasaran: {{ ucfirst($pengajuan->jenis_kegiatan) }}
                        </span>
                    </div>
                </div>

                {{-- Detail Informasi Pokok Perencanaan --}}
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rencana Lokasi Kegiatan</h4>
                            {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber Pembiayaan / DIPA</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->sumber_pembiayaan ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rencana Tanggal Mulai</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tanggal_mulai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rencana Tanggal Selesai</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tanggal_selesai_rencana?->format('d F Y') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-2">Latar Belakang & Deskripsi Singkat Kegiatan</h4>
                        <div class="p-4 bg-white rounded-lg border border-gray-100 text-sm leading-relaxed text-gray-700 whitespace-pre-line">
                            {{ $pengajuan->deskripsi_rencana ?? 'Tidak ada deskripsi.' }}
                        </div>
                    </div>

                    {{-- Lembar Telaah Komponen Anggaran Biaya (RAB) --}}
                    @if($pengajuan->kebutuhanAnggarans->count() > 0)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-4">Rincian Komponen Anggaran Belanja (RAB SBM)</h4>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Komponen Belanja</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Vol 1</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan 1</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Vol 2</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan 2</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pengajuan->kebutuhanAnggarans as $i => $anggaran)
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $anggaran->nama_item }}</td>
                                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-900">{{ $anggaran->volume_1 ?? '-' }}</td>
                                        {{-- REFAKTORISASI: Mengubah satuan_primary menjadi satuan_1 --}}
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $anggaran->satuan_1 ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-900">{{ $anggaran->volume_2 ?? '-' }}</td>
                                        {{-- REFAKTORISASI: Mengubah satuan_secondary menjadi satuan_2 --}}
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $anggaran->satuan_2 ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">Rp {{ number_format($anggaran->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Rp {{ number_format($anggaran->total_biaya ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 text-right min-w-[240px]">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Usulan Anggaran Pagu</p>
                                <p class="text-2xl font-semibold text-primary-600">Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya'), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Matriks Kebutuhan Logistik Lapangan Rumah Tangga --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-4">Daftar Permintaan Sarana Fasilitas / Logistik Rumah Tangga</h4>
                        @if($pengajuan->fasilitasLogistiks->count() > 0)
                            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Komponen Prasarana</th>
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Jumlah Permintaan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->fasilitasLogistiks as $index => $fasilitas)
                                            <tr class="hover:bg-gray-100 transition-colors">
                                                <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $fasilitas->nama_fasilitas }}</td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">{{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? 'Unit' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 bg-white border border-gray-100 rounded-lg text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="mt-2 text-gray-500">Tidak melampirkan permintaan fasilitas sarana gedung Rumah Tangga.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Tinjauan Riwayat Catatan Disposisi Sebelumnya --}}
                    {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
                    @if($pengajuan->catatan_kepala && $pengajuan->status !== 'diajukan')
                    <div class="rounded-lg border border-primary-200 bg-primary-50 p-5">
                        <h4 class="text-base font-bold text-primary-800 mb-2">Catatan Disposisi Anda Sebelumnya</h4>
                        <div class="p-4 bg-white border border-primary-100 rounded-lg text-sm leading-relaxed text-gray-700">
                            {{ $pengajuan->catatan_kepala }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Form Lembar Tindakan Disposisi Kepala Balai (Hanya muncul jika status 'diajukan') --}}
                {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status dan menyematkan ID eksplisit pada route --}}
                @if($pengajuan->status === 'diajukan')
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-4">Lembar Disposisi Kepala Balai</h4>
                    
                    <div x-data="{ action: null }" class="space-y-5">
                        {{-- Opsi Tombol Tindakan BPMN State --}}
                        <div class="flex flex-wrap gap-3">
                            <button @click="action = 'approve'" type="button" 
                                class="inline-flex items-center px-4 py-2 border text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'approve' ? 'bg-green-700 border-transparent text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Setujui Usulan
                            </button>
                            <button @click="action = 'revisi'" type="button"
                                class="inline-flex items-center px-4 py-2 border text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'revisi' ? 'bg-orange-700 border-transparent text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Kembalikan (Revisi)
                            </button>
                            <button @click="action = 'reject'" type="button"
                                class="inline-flex items-center px-4 py-2 border text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'reject' ? 'bg-red-600 border-transparent text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Usulan
                            </button>
                        </div>

                        {{-- Form Submit - Setuju --}}
                        <form x-show="action === 'approve'" x-cloak action="{{ route('approval.kepala.approve', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-5">
                            @csrf
                            <div>
                                <label for="catatan_approve" class="block text-sm font-semibold text-gray-700">Catatan Disposisi Tambahan (Opsional)</label>
                                <textarea name="catatan" id="catatan_approve" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Tambahkan instruksi strategis khusus kepanitiaan jika diperlukan..."></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Sahkan Disposisi Setuju
                            </button>
                        </form>

                        {{-- Form Submit - Minta Revisi --}}
                        <form x-show="action === 'revisi'" x-cloak action="{{ route('approval.kepala.revisi', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-5">
                            @csrf
                            <div>
                                <label for="catatan_revisi" class="block text-sm font-semibold text-gray-700">Rincian Instruksi Koreksi Dokumen <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_revisi" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Jelaskan secara rinci komponen materi atau logistik apa yang perlu diperbaiki PIC..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Kembalikan Berkas ke PIC
                            </button>
                        </form>

                        {{-- Form Submit - Tolak Usulan --}}
                        <form x-show="action === 'reject'" x-cloak action="{{ route('approval.kepala.reject', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-5">
                            @csrf
                            <div>
                                <label for="catatan_reject" class="block text-sm font-semibold text-gray-700">Alasan Penolakan Permanen Usulan Kegiatan <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_reject" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Tuliskan alasan penolakan rasional berdasarkan hasil evaluasi DIPA instansi..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Tolak Usulan Kegiatan Permanen
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('approval.kepala.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar Peninjauan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>