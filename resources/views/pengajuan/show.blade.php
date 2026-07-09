<x-app-layout>
    <x-slot name="header">
        Detail Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('pengajuan.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium">
                            Pengajuan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-medium">Detail</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner --}}
            @php
                // REFAKTORISASI: Menyesuaian peta status alur kerja (BPMN State Machine)
                $statusColors = [
                    'draft_pic' => 'bg-gray-100 border-gray-400 text-gray-800',
                    'diajukan' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
                    'disetujui_kepala' => 'bg-blue-100 border-blue-400 text-blue-800',
                    'disetujui_ppk' => 'bg-indigo-100 border-indigo-400 text-indigo-800',
                    'disetujui_final' => 'bg-green-100 border-green-400 text-green-800',
                    'persiapan' => 'bg-emerald-100 border-emerald-400 text-emerald-800',
                    'berlangsung' => 'bg-teal-100 border-teal-400 text-teal-800',
                    'selesai' => 'bg-green-100 border-green-400 text-green-800',
                    'ditolak' => 'bg-red-100 border-red-400 text-red-800',
                    'perlu_revisi' => 'bg-orange-100 border-orange-400 text-orange-800',
                ];
                $statusLabels = [
                    'draft_pic' => 'Draft - Belum Diajukan',
                    'diajukan' => 'Menunggu Persetujuan Kepala Balai',
                    'disetujui_kepala' => 'Disetujui Kepala Balai - Menunggu Review PPK',
                    'disetujui_ppk' => 'Disetujui PPK',
                    'disetujui_final' => 'Disetujui Final - Kelas Bimtek Terbentuk',
                    'persiapan' => 'Tahap Persiapan Pelaksanaan',
                    'berlangsung' => 'Kegiatan Sedang Berlangsung',
                    'selesai' => 'Kegiatan Selesai',
                    'ditolak' => 'Usulan Ditolak',
                    'perlu_revisi' => 'Perlu Revisi Dokumen',
                ];
                $verificationLabel = $pengajuan->butuh_verifikasi_dokumen
                    ? 'Verifikasi Dokumen Aktif'
                    : 'Verifikasi Dokumen Nonaktif';
                $verificationClass = $pengajuan->butuh_verifikasi_dokumen
                    ? 'bg-amber-100 border-amber-400 text-amber-800'
                    : 'bg-gray-100 border-gray-400 text-gray-700';
            @endphp
            
            {{-- REFAKTORISASI: Mengubah properti status_pengajuan menjadi status --}}
            <div class="mb-6 p-4 rounded-xl border-l-4 bg-white shadow-sm {{ $statusColors[$pengajuan->status] ?? 'bg-gray-100 border-gray-400 text-gray-800' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Alur Birokrasi: {{ $statusLabels[$pengajuan->status] ?? $pengajuan->status }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jenis Kegiatan</p>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Internal' : 'Eksternal' }}
                    </div>
                    <p class="mt-2 text-xs text-gray-400">
                        {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Aktor internal BBPMP' : 'Sasaran instansi luar' }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Mode Pelaksanaan</p>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                        {{ $pengajuan->mode_pelaksanaan_label }}
                    </div>
                    <p class="mt-2 text-xs text-gray-400">
                        {{ $pengajuan->mode_pelaksanaan === 'online' ? 'Media virtual meeting.' : ($pengajuan->mode_pelaksanaan === 'hybrid' ? 'Sesi luring & daring.' : 'Tatap muka fisik di lokasi.') }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Verifikasi Peserta</p>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $verificationClass }}">
                        {{ $verificationLabel }}
                    </div>
                    <p class="mt-2 text-xs text-gray-400">
                        {{ $pengajuan->butuh_verifikasi_dokumen ? 'Wajib unggah Surat Tugas/SPPD.' : 'Akses kelas terbuka langsung.' }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Periode Kegiatan</p>
                    <p class="mt-2 text-sm font-bold text-gray-900">
                        {{ $pengajuan->tanggal_mulai_rencana?->format('d M Y') ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-400 font-medium">s/d {{ $pengajuan->tanggal_selesai_rencana?->format('d M Y') ?? '-' }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Lokasi Perencanaan</p>
                    {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                    <p class="mt-2 text-sm font-bold text-gray-900 line-clamp-2">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 mb-6">
                {{-- Card Header --}}
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $pengajuan->judul_rencana }}</h2>
                            {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                            <p class="mt-1 text-sm text-gray-500 font-medium">Diusulkan oleh <span class="text-gray-700 font-semibold">{{ $pengajuan->pic->name ?? '-' }}</span> pada {{ $pengajuan->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>
                </div>

                {{-- Detail Info --}}
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Tempat Pelaksanaan Rencana</h4>
                            {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                            <p class="mt-1 text-base font-medium text-gray-900">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Sumber Pembiayaan / DIPA</h4>
                            <p class="mt-1 text-base font-medium text-gray-900">{{ $pengajuan->sumber_pembiayaan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5">
                        <h4 class="text-sm font-bold text-gray-700">Deskripsi / Pokok Pemikiran</h4>
                        <div class="mt-2 p-4 bg-white rounded-lg border border-gray-100 shadow-sm text-sm leading-relaxed text-gray-800 whitespace-pre-line">
                            {{ $pengajuan->deskripsi_rencana ?? 'Tidak ada deskripsi.' }}
                        </div>
                    </div>

                    {{-- Rincian Anggaran Biaya (RAB) SBM --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5">
                        <h4 class="text-sm font-bold text-gray-700 mb-1">Rincian Komponen Anggaran Biaya (RAB)</h4>
                        <p class="text-xs text-gray-500 mb-4">Daftar pagu komponen biaya belanja mengacu pada standar biaya masukan (SBM) tahun berjalan.</p>
                        
                        @if($pengajuan->kebutuhanAnggarans->count() > 0)
                            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 bg-gray-50 text-gray-600 font-semibold text-xs uppercase">
                                            <th class="px-4 py-3 text-left w-12">No</th>
                                            <th class="px-4 py-3 text-left">Komponen Item Belanja</th>
                                            <th class="px-4 py-3 text-center">Volume 1</th>
                                            <th class="px-4 py-3 text-center">Satuan 1</th>
                                            <th class="px-4 py-3 text-center">Volume 2</th>
                                            <th class="px-4 py-3 text-center">Satuan 2</th>
                                            <th class="px-4 py-3 text-right">Harga Satuan</th>
                                            <th class="px-4 py-3 text-right">Total Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-gray-700">
                                        @foreach($pengajuan->kebutuhanAnggarans as $index => $item)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_item }}</td>
                                                <td class="px-4 py-3 text-center font-medium">{{ $item->volume_1 }}</td>
                                                {{-- REFAKTORISASI: Menyesuaikan properti kolom database satuan_1 dan satuan_2 --}}
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $item->satuan_1 }}</td>
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $item->volume_2 ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $item->satuan_2 ?? '-' }}</td>
                                                <td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-bold text-gray-900">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Akumulasi Total Anggaran --}}
                            <div class="mt-4 flex justify-end">
                                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm text-right min-w-[240px]">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Pagu RAB Usulan:</p>
                                    <p class="text-2xl font-bold text-primary-600">Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya'), 0, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- Ringkasan Berdasarkan Kategori --}}
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($pengajuan->kebutuhanAnggarans->groupBy('kategori') as $kategori => $items)
                                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $kategori }}</p>
                                        <p class="text-base font-bold text-gray-800 mt-0.5">Rp {{ number_format($items->sum('total_biaya'), 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 bg-white border border-gray-100 rounded-xl text-center shadow-sm">
                                <p class="text-sm text-gray-500 font-medium">Belum ada rincian komponen anggaran biaya yang diajukan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Permintaan Sarana Fasilitas Lapangan --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-sm font-bold text-gray-700">Kebutuhan Fasilitas & Sarana Logistik</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Daftar checklist pemenuhan logistik aula/ruangan oleh unit kerja Rumah Tangga.</p>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary-700 bg-primary-100 px-2.5 py-1 rounded-full">Unit RT</span>
                        </div>
                        
                        @if($pengajuan->fasilitasLogistiks->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($pengajuan->fasilitasLogistiks as $fasilitas)
                                    <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm">
                                        <div class="flex items-center min-w-0">
                                            <span class="inline-flex items-center justify-center bg-primary-50 text-primary-700 text-xs font-bold rounded-lg h-7 px-2.5 mr-3 flex-shrink-0">
                                                {{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? 'Unit' }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-800 truncate">{{ $fasilitas->nama_fasilitas }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold flex-shrink-0
                                            @if($fasilitas->status === 'tersedia') bg-green-100 text-green-800
                                            @elseif($fasilitas->status === 'tidak_tersedia') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $fasilitas->status === 'diminta' ? 'Diminta' : ($fasilitas->status === 'tersedia' ? 'Tersedia' : 'Kosong') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 bg-white border border-gray-100 rounded-xl text-center shadow-sm">
                                <p class="text-sm text-gray-500 font-medium">Tidak ada permintaan fasilitas sarana lapangan yang dilampirkan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Catatan Kebijakan Pejabat Berwenang --}}
                    @if($pengajuan->catatan_kepala)
                        <div class="rounded-xl border border-blue-200 bg-blue-50/30 p-5">
                            <h4 class="text-sm font-bold text-blue-800 mb-2">Lembar Disposisi Kepala Balai</h4>
                            <div class="p-4 bg-white border border-blue-100 rounded-xl text-sm leading-relaxed text-gray-800 shadow-sm shadow-blue-50">
                                {{ $pengajuan->catatan_kepala }}
                            </div>
                        </div>
                    @endif

                    @if($pengajuan->catatan_ppk)
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50/30 p-5">
                            <h4 class="text-sm font-bold text-indigo-800 mb-2">Lembar Disposisi Pejabat PPK</h4>
                            <div class="p-4 bg-white border border-indigo-100 rounded-xl text-sm leading-relaxed text-gray-800 shadow-sm shadow-indigo-50">
                                {{ $pengajuan->catatan_ppk }}
                            </div>
                        </div>
                    @endif

                    @if($pengajuan->catatan_rt)
                        <div class="rounded-xl border border-purple-200 bg-purple-50/30 p-5">
                            <h4 class="text-sm font-bold text-purple-800 mb-2">Catatan Kelayakan Koordinator Rumah Tangga</h4>
                            <div class="p-4 bg-white border border-purple-100 rounded-xl text-sm leading-relaxed text-gray-800 shadow-sm shadow-purple-50">
                                {{ $pengajuan->catatan_rt }}
                            </div>
                        </div>
                    @endif

                    {{-- Tautan Masuk Kelas Bimtek Pelaksanaan --}}
                    {{-- REFAKTORISASI: Jika state status menembak fase pelaksanaan (bukan draft/diajukan), kelas bimtek dinyatakan aktif --}}
                    @if(in_array($pengajuan->status, ['disetujui_final', 'persiapan', 'berlangsung', 'selesai']))
                        <div class="rounded-xl border border-green-200 bg-green-50/30 p-5">
                            <div class="p-4 bg-white border border-green-100 rounded-xl shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-green-100 text-green-700 mr-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-green-900 block">Kegiatan Telah Resmi Disahkan</span>
                                            <span class="text-xs text-gray-500 font-medium">Peta alur perencanaan selesai. Anda dapat langsung mengelola kepanitiaan dan materi kelas.</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('bimtek.show', $pengajuan->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                                        Masuk Panel Kelas →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Action Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            <span>Manajemen Perencanaan Usulan</span>
                        </div>

                        <div class="flex items-center justify-end space-x-3 w-full sm:w-auto">
                            <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-50 transition shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </a>

                            {{-- REFAKTORISASI: Menyesuaikan parameter properti pic_user_id, status, dan kata kunci 'draft_pic' --}}
                            @if((Auth::id() === $pengajuan->pic_user_id || Auth::user()->isAdminIt()) && in_array($pengajuan->status, ['draft_pic', 'diajukan', 'perlu_revisi']))
                                <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-lg transition shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Data
                                </a>
                                <form action="{{ route('pengajuan.destroy', $pengajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas usulan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm rounded-lg transition shadow-sm">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>