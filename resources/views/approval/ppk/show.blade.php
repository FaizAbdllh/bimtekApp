<x-app-layout>
    <x-slot name="header">
        Review Anggaran Usulan
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('approval.ppk.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium">
                            Persetujuan Anggaran
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-medium">Review Pagu</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner --}}
            @php
                $statusColors = [
                    'disetujui_kepala' => 'bg-yellow-50 border-yellow-100 text-yellow-800',
                    'disetujui_ppk' => 'bg-primary-50 border-primary-100 text-primary-800',
                    'disetujui_final' => 'bg-green-50 border-green-100 text-green-800',
                ];
                $statusLabels = [
                    'disetujui_kepala' => 'Menunggu Review & Persetujuan Anggaran PPK',
                    'disetujui_ppk' => 'Telah Disetujui PPK - Menunggu Sinkronisasi Akhir',
                    'disetujui_final' => 'Disetujui Final - Kelas Bimtek Telah Aktif',
                ];
            @endphp
            {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
            <div class="mb-6 p-3 rounded-lg border text-sm {{ $statusColors[$pengajuan->status] ?? 'bg-gray-50 border-gray-200 text-gray-800' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Status Verifikasi Keuangan: {{ $statusLabels[$pengajuan->status] ?? $pengajuan->status }}</span>
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
                            <p class="mt-1 text-sm text-gray-500">Diajukan oleh <span class="font-medium text-gray-900">{{ $pengajuan->pic->name ?? '-' }}</span> pada {{ $pengajuan->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold w-fit {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                            Sasaran: {{ ucfirst($pengajuan->jenis_kegiatan) }}
                        </span>
                    </div>
                </div>

                {{-- Detail Informasi Usulan --}}
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rencana Tempat Kegiatan</h4>
                            {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber Pembiayaan / Kode Pagu</h4>
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
                        <h4 class="text-base font-bold text-gray-800">Deskripsi / Pokok Pemikiran Kegiatan</h4>
                        <div class="mt-2 p-4 bg-white rounded-lg border border-gray-100 text-sm leading-relaxed text-gray-700 whitespace-pre-line">
                            {{ $pengajuan->deskripsi_rencana ?? 'Tidak ada deskripsi.' }}
                        </div>
                    </div>

                    {{-- Lembar Disposisi Kepala Balai (Sebagai dasar telaah PPK) --}}
                    @if($pengajuan->catatan_kepala)
                    <div class="rounded-lg border border-primary-200 bg-primary-50 p-5">
                        <h4 class="text-base font-bold text-primary-800 mb-2">Catatan Disposisi dari Kepala Balai</h4>
                        <div class="p-4 bg-white border border-primary-100 rounded-lg text-sm leading-relaxed text-gray-700">
                            {{ $pengajuan->catatan_kepala }}
                        </div>
                    </div>
                    @endif

                    {{-- Tabel Review Utama Komponen Anggaran Biaya (RAB SBM) --}}
                    @if($pengajuan->kebutuhanAnggarans->count() > 0)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-4">Rincian Komponen Anggaran Belanja (RAB SBM)</h4>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center w-12 text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Komponen Belanja</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Vol 1</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Satuan 1</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Vol 2</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Satuan 2</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Harga Satuan</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total Biaya</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-gray-700">
                                    @foreach($pengajuan->kebutuhanAnggarans as $i => $anggaran)
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="px-4 py-3 text-center text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $anggaran->nama_item }}</td>
                                        <td class="px-4 py-3 text-center font-medium">{{ $anggaran->volume_1 ?? '-' }}</td>
                                        {{-- REFAKTORISASI: Mengubah satuan_primary menjadi satuan_1 --}}
                                        <td class="px-4 py-3 text-center text-gray-500">{{ $anggaran->satuan_1 ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center font-medium">{{ $anggaran->volume_2 ?? '-' }}</td>
                                        {{-- REFAKTORISASI: Mengubah satuan_secondary menjadi satuan_2 --}}
                                        <td class="px-4 py-3 text-center text-gray-500">{{ $anggaran->satuan_2 ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format($anggaran->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($anggaran->total_biaya ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 text-right min-w-[240px]">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Usulan Biaya:</p>
                                <p class="text-2xl font-semibold text-primary-600">Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya'), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Informasi Logistik Rumah Tangga --}}
                    @if($pengajuan->fasilitasLogistiks->count() > 0)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div>
                                <p class="text-base font-bold text-gray-800">Kebutuhan Fasilitas Lapangan: <span class="text-primary-600">{{ $pengajuan->fasilitasLogistiks->count() }} Item</span></p>
                                <p class="text-sm text-gray-500 mt-0.5">* Detail permintaan prasarana ruangan otomatis dialirkan ke meja kerja Koordinator RT setelah pengesahan ini.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Tinjauan Riwayat Catatan PPK Sebelumnya --}}
                    {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status --}}
                    @if($pengajuan->catatan_ppk && $pengajuan->status !== 'disetujui_kepala')
                    <div class="rounded-lg border border-primary-200 bg-primary-50 p-5">
                        <h4 class="text-base font-bold text-primary-800 mb-2">Catatan Kelayakan Anda Sebelumnya</h4>
                        <div class="p-4 bg-white border border-primary-100 rounded-lg text-sm leading-relaxed text-gray-700">
                            {{ $pengajuan->catatan_ppk }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Form Lembar Tindakan Disposisi Anggaran PPK (Hanya muncul jika status 'disetujui_kepala') --}}
                {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status dan menyematkan ID usulan eksplisit pada rute --}}
                @if($pengajuan->status === 'disetujui_kepala')
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-4">Lembar Kendali Biaya Pejabat PPK</h4>
                    
                    {{-- 💡 Deklarasi x-data Alpine.js sudah dibersihkan dan siap bekerja --}}
                    <div x-data="{ action: null }" class="space-y-5">
                        {{-- Pilihan Opsi Aksi --}}
                        <div class="flex flex-wrap gap-3">
                            <button @click="action = 'approve'" type="button" 
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'approve' ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Sahkan & Setujui Biaya
                            </button>
                            <button @click="action = 'revisi'" type="button"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'revisi' ? 'bg-orange-700 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Kembalikan Ke PIC (Revisi Biaya)
                            </button>
                            <button @click="action = 'reject'" type="button"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="action === 'reject' ? 'bg-red-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100'">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Usulan Anggaran
                            </button>
                        </div>

                        {{-- Form Submit - Approve --}}
                        <form x-show="action === 'approve'" x-cloak action="{{ route('approval.ppk.approve', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-4">
                            @csrf
                            <div class="p-3 bg-green-50 border border-green-100 rounded-lg text-sm text-green-800 leading-relaxed">
                                Konfirmasi: Dengan mensahkan pagu ini, sistem otomatis menerbitkan entitas Kelas Pelaksanaan Bimtek aktif yang siap dikelola tim PIC Pokja.
                            </div>
                            <div>
                                <label for="catatan_approve" class="block text-sm font-semibold text-gray-700">Catatan Pengesahan Belanja (Opsional)</label>
                                <textarea name="catatan" id="catatan_approve" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Tambahkan nomor instruksi SPM / maklumat anggaran jika diperlukan..."></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Sahkan Pagu Anggaran
                            </button>
                        </form>

                        {{-- Form Submit - Minta Revisi --}}
                        <form x-show="action === 'revisi'" x-cloak action="{{ route('approval.ppk.revisi', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-4">
                            @csrf
                            <div class="p-3 bg-orange-50 border border-orange-100 rounded-lg text-sm text-orange-800 leading-relaxed">
                                Catatan Alur: Berkas usulan biaya akan dikembalikan langsung ke meja draf PIC pengaju. Pasca-revisi, berkas otomatis kembali ke antrean PPK tanpa perlu mengulang tanda tangan Kepala Balai.
                            </div>
                            <div>
                                <label for="catatan_revisi" class="block text-sm font-semibold text-gray-700">Rincian Koreksi Pagu SBM <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_revisi" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Jelaskan komponen harga satuan atau volume belanja apa yang wajib dikurangi PIC..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Kembalikan Berkas Belanja
                            </button>
                        </form>

                        {{-- Form Submit - Tolak Usulan --}}
                        <form x-show="action === 'reject'" x-cloak action="{{ route('approval.ppk.reject', $pengajuan->id) }}" method="POST" class="space-y-5 border-t border-gray-200 pt-4">
                            @csrf
                            <div class="p-3 bg-red-50 border border-red-100 rounded-lg text-sm text-red-800 leading-relaxed">
                                Peringatan: Penolakan ini bersifat permanen. Seluruh rincian kebutuhan komponen belanja dibatalkan dari sistem DIPA berjalan.
                            </div>
                            <div>
                                <label for="catatan_reject" class="block text-sm font-semibold text-gray-700">Alasan Penolakan Anggaran Kegiatan <span class="text-red-500">*</span></label>
                                <textarea name="catatan" id="catatan_reject" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Jelaskan alasan penolakan rasional (misal: Alokasi pagu sub-kegiatan DIPA tahun berjalan sudah habis)..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Tolak Komponen Anggaran Belanja
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('approval.ppk.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar Anggaran
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>