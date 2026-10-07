<x-app-layout>
    <x-slot name="header">
        Detail Pengajuan
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('pengajuan.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium transition-colors">
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

            {{-- Status Banner (Stepper Visual Alur Birokrasi) --}}
            @php
                // 1. Definisikan urutan alur persetujuan logis
                $birokrasiSteps = [
                    'diajukan' => 'Diajukan',
                    'disetujui_kepala' => 'Persetujuan Kepala',
                    'disetujui_ppk' => 'Persetujuan PPK',
                    'disetujui_final' => 'Disetujui Final'
                ];

                $currentStatus = $pengajuan->status;
                
                // 2. Tentukan level aktif (0 sampai 3)
                $activeIndex = -1;
                if (in_array($currentStatus, ['draft_pic'])) $activeIndex = -1;
                elseif (in_array($currentStatus, ['diajukan'])) $activeIndex = 0;
                elseif (in_array($currentStatus, ['disetujui_kepala'])) $activeIndex = 1;
                elseif (in_array($currentStatus, ['disetujui_ppk'])) $activeIndex = 2;
                elseif (in_array($currentStatus, ['disetujui_final', 'persiapan', 'registrasi', 'persiapan_selesai', 'berlangsung', 'selesai'])) $activeIndex = 3;

                // 3. Deteksi jika ada penolakan atau revisi
                $isDitolak = $currentStatus === 'ditolak';
                $isRevisi = $currentStatus === 'perlu_revisi';

                // 4. Setup label verifikasi (INI YANG HILANG SEBELUMNYA)
                $verificationLabel = $pengajuan->butuh_verifikasi_dokumen
                    ? 'Verifikasi Dokumen Aktif'
                    : 'Verifikasi Dokumen Nonaktif';
                $verificationClass = $pengajuan->butuh_verifikasi_dokumen
                    ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-gray-100 text-gray-800';
            @endphp
            
            <div class="mb-6 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Status Alur Birokrasi</h3>
                
                {{-- Container Stepper --}}
                <div class="flex w-full mt-2">
                    @foreach($birokrasiSteps as $key => $label)
                        @php
                            $stepIndex = $loop->index;
                            $isLast = $loop->last;
                            
                            // Pewarnaan solid agar garis di belakang tidak tembus
                            if ($isDitolak) {
                                $colorClass = 'bg-red-600 text-white border-2 border-red-600'; 
                            } elseif ($isRevisi) {
                                $colorClass = 'bg-orange-700 text-white border-2 border-orange-700'; 
                            } elseif ($stepIndex < $activeIndex) {
                                $colorClass = 'bg-primary-600 text-white border-2 border-primary-600'; 
                            } elseif ($stepIndex === $activeIndex) {
                                $colorClass = 'bg-white text-primary-600 border-4 border-primary-600'; 
                            } else {
                                $colorClass = 'bg-white text-gray-500 border-2 border-gray-300'; 
                            }
                        @endphp

                        {{-- Pembungkus flex-1 menjamin lebar setiap langkah seimbang dan sejajar --}}
                        <div class="relative flex-1 flex flex-col items-center">
                            
                            {{-- Garis Penghubung (Digambar dari titik tengah item saat ini menembus ke item berikutnya) --}}
                            @if(!$isLast)
                                {{-- Garis Background Abu-abu --}}
                                <div class="absolute left-1/2 top-5 w-full h-1 bg-gray-200 transform -translate-y-1/2 z-0"></div>
                                
                                {{-- Garis Progres Aktif --}}
                                @if($activeIndex > $stepIndex && !$isDitolak && !$isRevisi)
                                    <div class="absolute left-1/2 top-5 w-full h-1 bg-primary-600 transform -translate-y-1/2 z-0"></div>
                                @endif
                            @endif

                            {{-- Lingkaran Angka/Ikon --}}
                            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm {{ $colorClass }}">
                                @if($isDitolak && $stepIndex === 0)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                @elseif($isRevisi && $stepIndex === $activeIndex)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @elseif($stepIndex < $activeIndex || $activeIndex === 3)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    {{ $stepIndex + 1 }}
                                @endif
                            </div>
                            
                            {{-- Teks Label dijamin rata tengah tepat di bawah lingkaran --}}
                            <span class="mt-3 text-xs md:text-sm font-semibold {{ $stepIndex <= $activeIndex && !$isDitolak && !$isRevisi ? 'text-gray-800' : 'text-gray-500' }} text-center px-1">
                                {{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Pesan Khusus Jika Ditolak/Revisi --}}
                @if($isDitolak)
                    <div class="mt-6 p-3 bg-red-50 border border-red-100 text-red-800 text-sm rounded-lg flex items-start">
                        <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        <div>
                            <strong class="font-semibold">Pengajuan Ditolak.</strong> Silakan lihat catatan pimpinan pada bagian log untuk detail penolakan.
                        </div>
                    </div>
                @elseif($isRevisi)
                    <div class="mt-6 p-3 bg-orange-50 border border-orange-100 text-orange-800 text-sm rounded-lg flex items-start">
                        <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <strong class="font-semibold">Perlu Revisi.</strong> Pimpinan meminta perbaikan pada dokumen atau data pengajuan ini sebelum dapat diproses lebih lanjut.
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kegiatan</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                        {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Internal' : 'Eksternal' }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Aktor internal BBPMP' : 'Sasaran instansi luar' }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Mode Pelaksanaan</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                        {{ $pengajuan->mode_pelaksanaan_label }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $pengajuan->mode_pelaksanaan === 'online' ? 'Media virtual meeting.' : ($pengajuan->mode_pelaksanaan === 'hybrid' ? 'Sesi luring & daring.' : 'Tatap muka fisik di lokasi.') }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Verifikasi Peserta</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $verificationClass }}">
                        {{ $verificationLabel }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $pengajuan->butuh_verifikasi_dokumen ? 'Wajib unggah dokumen persyaratan.' : 'Akses kelas terbuka langsung.' }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Periode Kegiatan</p>
                    <p class="mt-2 text-sm font-medium text-gray-900">
                        {{ $pengajuan->tanggal_mulai_rencana?->format('d M Y') ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-500">s/d {{ $pengajuan->tanggal_selesai_rencana?->format('d M Y') ?? '-' }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Lokasi Perencanaan</p>
                    {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                    <p class="mt-2 text-sm font-medium text-gray-900 line-clamp-2">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-6">
                {{-- Card Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-primary-800">{{ $pengajuan->judul_rencana }}</h2>
                            {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                            <p class="mt-1 text-sm text-gray-500">Diusulkan oleh <span class="text-gray-700 font-semibold">{{ $pengajuan->pic->name ?? '-' }}</span> pada {{ $pengajuan->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>
                </div>

                {{-- Detail Info --}}
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tempat Pelaksanaan Rencana</h4>
                            {{-- REFAKTORISASI: Mengubah tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber Pembiayaan / DIPA</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $pengajuan->sumber_pembiayaan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800">Deskripsi / Pokok Pemikiran</h4>
                        <div class="mt-2 p-4 bg-white rounded-lg border border-gray-100 text-sm leading-relaxed text-gray-700 whitespace-pre-line">
                            {{ $pengajuan->deskripsi_rencana ?? 'Tidak ada deskripsi.' }}
                        </div>
                    </div>

                    {{-- Rincian Anggaran Biaya (RAB) SBM --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <h4 class="text-base font-bold text-gray-800 mb-1">Rincian Komponen Anggaran Biaya (RAB)</h4>
                        <p class="text-sm text-gray-500 mb-4">Daftar pagu komponen biaya belanja mengacu pada standar biaya masukan (SBM) tahun berjalan.</p>
                        
                        @if($pengajuan->kebutuhanAnggarans->count() > 0)
                            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider">
                                            <th class="px-4 py-3 text-center w-12">No</th>
                                            <th class="px-4 py-3 text-left">Komponen Item Belanja</th>
                                            <th class="px-4 py-3 text-center">Volume 1</th>
                                            <th class="px-4 py-3 text-center">Satuan 1</th>
                                            <th class="px-4 py-3 text-center">Volume 2</th>
                                            <th class="px-4 py-3 text-center">Satuan 2</th>
                                            <th class="px-4 py-3 text-right">Harga Satuan</th>
                                            <th class="px-4 py-3 text-right">Total Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 text-gray-700">
                                        @foreach($pengajuan->kebutuhanAnggarans as $index => $item)
                                            <tr class="hover:bg-gray-100 transition-colors">
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_item }}</td>
                                                <td class="px-4 py-3 text-center font-medium">{{ $item->volume_1 }}</td>
                                                {{-- REFAKTORISASI: Menyesuaikan properti kolom database satuan_1 dan satuan_2 --}}
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $item->satuan_1 }}</td>
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $item->volume_2 ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $item->satuan_2 ?? '-' }}</td>
                                                <td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Akumulasi Total Anggaran --}}
                            <div class="mt-4 flex justify-end">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 text-right min-w-[240px]">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Pagu RAB Usulan:</p>
                                    <p class="text-2xl font-semibold text-primary-600">Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya'), 0, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- Ringkasan Berdasarkan Kategori --}}
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($pengajuan->kebutuhanAnggarans->groupBy('kategori') as $kategori => $items)
                                    <div class="bg-white border border-gray-100 rounded-lg p-3">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $kategori }}</p>
                                        <p class="text-base font-semibold text-gray-800 mt-0.5">Rp {{ number_format($items->sum('total_biaya'), 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-8 bg-white border border-gray-100 rounded-lg text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <p class="mt-2 text-gray-500">Belum ada rincian komponen anggaran biaya yang diajukan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Permintaan Sarana Fasilitas Lapangan --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-base font-bold text-gray-800">Kebutuhan Fasilitas & Sarana Logistik</h4>
                                <p class="text-sm text-gray-500 mt-0.5">Daftar checklist pemenuhan logistik aula/ruangan oleh unit kerja Rumah Tangga.</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold text-primary-800 bg-primary-100 rounded-full flex-shrink-0">Unit RT</span>
                        </div>
                        
                        @if($pengajuan->fasilitasLogistiks->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($pengajuan->fasilitasLogistiks as $fasilitas)
                                    <div class="flex items-center justify-between bg-white rounded-lg px-4 py-3 border border-gray-100">
                                        <div class="flex items-center min-w-0">
                                            <span class="inline-flex items-center justify-center bg-primary-100 text-primary-800 text-xs font-semibold rounded-full px-2 py-1 mr-3 flex-shrink-0">
                                                {{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? 'Unit' }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 truncate">{{ $fasilitas->nama_fasilitas }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0
                                            @if($fasilitas->status === 'tersedia') bg-green-100 text-green-800
                                            @elseif($fasilitas->status === 'tidak_tersedia') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $fasilitas->status === 'diminta' ? 'Diminta' : ($fasilitas->status === 'tersedia' ? 'Tersedia' : 'Kosong') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-8 bg-white border border-gray-100 rounded-lg text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="mt-2 text-gray-500">Tidak ada permintaan fasilitas sarana lapangan yang dilampirkan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Catatan Kebijakan Pejabat Berwenang --}}
                    @if($pengajuan->catatan_kepala)
                        <div class="rounded-lg border border-primary-200 bg-primary-50 p-5">
                            <h4 class="text-base font-bold text-primary-800 mb-2">Lembar Disposisi Kepala Balai</h4>
                            <div class="p-4 bg-white border border-primary-100 rounded-lg text-sm leading-relaxed text-gray-700">
                                {{ $pengajuan->catatan_kepala }}
                            </div>
                        </div>
                    @endif

                    @if($pengajuan->catatan_ppk)
                        <div class="rounded-lg border border-primary-200 bg-primary-50 p-5">
                            <h4 class="text-base font-bold text-primary-800 mb-2">Lembar Disposisi Pejabat PPK</h4>
                            <div class="p-4 bg-white border border-primary-100 rounded-lg text-sm leading-relaxed text-gray-700">
                                {{ $pengajuan->catatan_ppk }}
                            </div>
                        </div>
                    @endif

                    @if($pengajuan->catatan_rt)
                        <div class="rounded-lg border border-primary-200 bg-primary-50 p-5">
                            <h4 class="text-base font-bold text-primary-800 mb-2">Catatan Kelayakan Koordinator Rumah Tangga</h4>
                            <div class="p-4 bg-white border border-primary-100 rounded-lg text-sm leading-relaxed text-gray-700">
                                {{ $pengajuan->catatan_rt }}
                            </div>
                        </div>
                    @endif

                    {{-- Tautan Masuk Kelas Bimtek Pelaksanaan --}}
                    {{-- REFAKTORISASI: Jika state status menembak fase pelaksanaan (bukan draft/diajukan), kelas bimtek dinyatakan aktif --}}
                    @if(in_array($pengajuan->status, ['disetujui_final', 'persiapan', 'berlangsung', 'selesai']))
                        <div class="rounded-lg border border-green-200 bg-green-50 p-5">
                            <div class="p-4 bg-white border border-green-100 rounded-lg">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="text-sm font-semibold text-green-800 block">Kegiatan Telah Resmi Disahkan</span>
                                            <span class="text-sm text-gray-500">Peta alur perencanaan selesai. Anda dapat langsung mengelola kepanitiaan dan materi kelas.</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('bimtek.show', $pengajuan->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white font-semibold text-sm rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        Masuk Panel Kelas →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Action Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <span>Manajemen Perencanaan Usulan</span>
                        </div>

                        <div class="flex items-center justify-end space-x-3 w-full sm:w-auto">
                            <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </a>

                            {{-- REFAKTORISASI: Menyesuaikan parameter properti pic_user_id, status, dan kata kunci 'draft_pic' --}}
                            @if((Auth::id() === $pengajuan->pic_user_id || Auth::user()->isAdminIt()) && in_array($pengajuan->status, ['draft_pic', 'diajukan', 'perlu_revisi']))
                                <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white font-semibold text-sm rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Data
                                </a>
                                <form action="{{ route('pengajuan.destroy', $pengajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas usulan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
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