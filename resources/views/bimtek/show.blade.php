<x-app-layout>
    <x-slot name="header">
        Detail Ruang Kelas Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-6">
                <a href="{{ route('bimtek.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Kelas
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kiri & Tengah: Informasi Utama & Tab Konten --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Kartu Utama Informasi Bimtek --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                @php
                                    $statusColors = [
                                        'disetujui_final' => 'bg-gray-100 text-gray-800',
                                        'persiapan' => 'bg-yellow-100 text-yellow-800',
                                        'registrasi' => 'bg-primary-100 text-primary-800',
                                        'persiapan_selesai' => 'bg-secondary-100 text-secondary-800',
                                        'berlangsung' => 'bg-green-100 text-green-800',
                                        'selesai' => 'bg-green-100 text-green-800',
                                        'dibatalkan' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusLabels = [
                                        'disetujui_final' => 'Disetujui',
                                        'persiapan' => 'Persiapan',
                                        'registrasi' => 'Registrasi',
                                        'persiapan_selesai' => 'Persiapan Selesai',
                                        'berlangsung' => 'Berlangsung',
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                    ];
                                    $canManage = ($bimtek->pic_user_id === auth()->id() || $bimtek->panitia()->where('user_id', auth()->id())->exists() || auth()->user()->isAdminIt());
                                    $isPic = $bimtek->pic_user_id === auth()->id();
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColors[$bimtek->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$bimtek->status] ?? ucfirst($bimtek->status) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-3 mb-4">
                                <h2 class="text-lg font-bold text-gray-800">
                                    {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                                </h2>

                                @if(
                                    $canManage
                                    && !auth()->user()->isAdminIt()
                                    && $bimtek->isEditable()
                                )
                                    <a href="{{ route('bimtek.edit', $bimtek->id) }}"
                                    class="shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        Edit Data
                                    </a>
                                @endif
                            </div>

                            @php
                                $modeBadge = [
                                    'offline' => 'bg-gray-100 text-gray-800',
                                    'online' => 'bg-gray-100 text-gray-800',
                                    'hybrid' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp

                            {{-- Grid Informasi Detail Kolom --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm border-t border-gray-200 pt-4">
                                <div>
                                    <span class="text-sm text-gray-500 block">Mode Pelaksanaan:</span>
                                    <span class="inline-flex mt-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $modeBadge[$bimtek->mode_pelaksanaan] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $bimtek->mode_pelaksanaan_label }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 block">Link Virtual Meeting:</span>
                                    @if($bimtek->virtual_meeting_url)
                                        <div class="flex items-center gap-2 mt-1">
                                            <a href="{{ $bimtek->virtual_meeting_url }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors break-all">
                                                {{ $bimtek->virtual_meeting_url }}
                                            </a>

                                            @if($canManage)
                                                <button type="button"
                                                        onclick="navigator.clipboard.writeText(@js($bimtek->virtual_meeting_url))"
                                                        class="shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                                                    Salin Link
                                                </button>
                                                @if($bimtek->status === 'persiapan')
                                                    <button type="button"
                                                            x-data
                                                            @click="$dispatch('open-modal', 'virtual-meeting-modal')"
                                                            class="shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                                                        Ubah Link
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    @elseif(in_array($bimtek->mode_pelaksanaan, ['online', 'hybrid']))
                                        <span class="text-sm text-gray-500">Belum ditentukan</span>

                                        @if($canManage && $bimtek->status === 'persiapan')
                                            <button type="button"
                                                    x-data
                                                    @click="$dispatch('open-modal', 'virtual-meeting-modal')"
                                                    class="ml-2 text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                                                Tambahkan Link
                                            </button>
                                        @endif

                                    @endif
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 block">Lokasi Pelaksanaan:</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1">{{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 block">Periode Pelaksanaan:</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1">
                                        {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('d M Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('d M Y') : '-') }}
                                        @if($bimtek->tanggal_selesai_aktual)
                                            - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 block">Alokasi Anggaran DIPA:</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1">Rp {{ number_format($bimtek->kebutuhanAnggarans->sum('total_biaya') ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 block">Sumber Dana:</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1 truncate">{{ $bimtek->sumber_pembiayaan ?? '-' }}</span>
                                </div>
                            </div>

                            @if($bimtek->deskripsi_rencana)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <span class="text-base font-bold text-gray-800">
                                        Latar Belakang Kegiatan
                                    </span>

                                    <p class="mt-2 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ $bimtek->deskripsi_rencana }}
                                    </p>
                                </div>
                            @endif

                            @if($canManage || $bimtek->deskripsi_jadwal)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-base font-bold text-gray-800">
                                            Detail Pelaksanaan Kegiatan
                                        </span>

                                        @if($canManage && $bimtek->isEditable())
                                            <button type="button"
                                                    x-data
                                                    @click="$dispatch('open-modal', 'rundown-modal')"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                                                {{ $bimtek->deskripsi_jadwal ? 'Edit data' : 'tambah data' }}
                                            </button>
                                        @endif
                                    </div>

                                    @if($bimtek->deskripsi_jadwal)
                                        <p class="mt-2 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                            {{ $bimtek->deskripsi_jadwal }}
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm text-gray-500">
                                            Rundown pelaksanaan belum diisi.
                                        </p>
                                    @endif
                                </div>
                            @endif

                            {{-- Permintaan Sarana Fasilitas Lapangan --}}
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-bold text-gray-800">Pemenuhan Kebutuhan Fasilitas/Logistik</h4>
                                       
                                    </div>
                 
                                </div>
                                
                                @if($bimtek->fasilitasLogistiks->count() > 0)
                                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($bimtek->fasilitasLogistiks as $fasilitas)
                                            <li class="flex items-center justify-between gap-3 bg-white rounded-lg px-4 py-3 border border-gray-200">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $fasilitas->nama_fasilitas }}
                                                    </p>
                                                    <p class="mt-0.5 text-xs text-gray-500">
                                                        {{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? 'Unit' }}
                                                    </p>
                                                </div>

                                                @if($fasilitas->status === 'tersedia')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-xs font-semibold whitespace-nowrap flex-shrink-0">
                                                        
                                                        Tersedia
                                                    </span>
                                                @elseif($fasilitas->status === 'tidak_tersedia')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-xs font-semibold whitespace-nowrap flex-shrink-0">
                                                       
                                                        Tidak Tersedia
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 text-yellow-700 rounded-full text-xs font-semibold whitespace-nowrap flex-shrink-0">
                                                       
                                                        Belum Diperiksa
                                                    </span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            {{-- 💡 PERBAIKAN 1: Modul Tunggal Surat Undangan (Melebur Draft & Final Selaras Kolom Terpadu) --}}
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-base font-bold text-gray-800">Surat Undangan Peserta</span>
                                    </div>

                                    @if($bimtek->file_surat_undangan_path)
                                        <div class="flex items-center justify-between text-sm p-4 bg-white rounded-lg border border-gray-100">
                                            <div class="flex items-center min-w-0 mr-4">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-red-600 bg-red-100 mr-3">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span class="truncate font-medium text-gray-900">
                                                    {{ basename($bimtek->file_surat_undangan_path) }}
                                                </span>
                                            </div>
                                            <div class="flex gap-3 shrink-0">
                                                <a href="{{ route('bimtek.preview-draft', $bimtek->id) }}" target="_blank" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Lihat</a>
                                                <a href="{{ route('bimtek.download-draft', $bimtek->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Unduh</a>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 my-2">Belum ada berkas surat undangan yang diunggah.</p>
                                    @endif

                                    @if($canManage && in_array($bimtek->status, ['disetujui_final', 'persiapan', 'registrasi', 'persiapan_selesai']))
                                        <button type="button" x-data @click="$dispatch('open-modal', 'upload-undangan-modal')" class="w-full mt-3 inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                                            {{ $bimtek->file_surat_undangan_path ? 'Ganti Berkas Surat Undangan' : 'Unggah Surat Undangan' }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Daftar syarat berkas yang aktif di halaman detail Bimtek --}}
                            @if($bimtek->butuh_verifikasi_dokumen && $bimtek->syaratDokumens->isNotEmpty())
                                <div class="mt-4 rounded-lg border border-gray-200 bg-white p-5">
                                    <h4 class="text-base font-bold text-gray-800 mb-3">
                                        Dokumen Persyaratan Administrasi
                                    </h4>

                                    <ul class="divide-y divide-gray-200">
                                        @foreach($bimtek->syaratDokumens as $syarat)
                                            <li class="flex items-center justify-between py-2.5">
                                                <span class="text-sm text-gray-900">{{ $syarat->nama_dokumen }}</span>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-xs font-semibold whitespace-nowrap flex-shrink-0">
                                                    {{ $syarat->is_wajib ? 'Wajib' : 'Opsional' }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Tautan Registrasi Mandiri / Shared Link --}}
                            @if($canManage && $bimtek->mode_pelaksanaan !== 'internal')
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <span class="text-base font-bold text-gray-800 mb-3">Link Registrasi Peserta</span>
                                    @if($bimtek->invite_code)
                                        <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                            <input id="invite-link" class="flex-1 min-w-0 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-gray-700" readonly 
                                                    value="{{ url('/register?code=' . $bimtek->invite_code) }}">
                                            <button onclick="navigator.clipboard.writeText(document.getElementById('invite-link').value); alert('Link berhasil disalin!');" class="shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">Salin</button>
                                        </div>
                                    @else
                                        <form action="{{ route('bimtek.generate-invite', $bimtek->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">
                                                Generate Link Registrasi
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Perlindungan Berkas Verifikasi Kelulusan Dokumen Bagi Aktor Peserta --}}
                    @php
                        $isPeserta = $bimtek->peserta->contains(auth()->id());
                        if ($isPeserta && $bimtek->butuh_verifikasi_dokumen) {
                            $pesertaPivot = Illuminate\Support\Facades\DB::table('bimtek_pesertas')
                                ->where('bimtek_id', $bimtek->id)
                                ->where('user_id', auth()->id())
                                ->first();
                            $statusVerifikasi = $pesertaPivot ? $pesertaPivot->status_verifikasi : 'pending';
                        } else {
                            $statusVerifikasi = null;
                        }
                    @endphp

                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                        <div class="bg-yellow-50 border border-yellow-100 text-yellow-800 p-3 rounded-lg text-sm">
                            <div class="flex items-start">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-yellow-800">
                                        @if($statusVerifikasi === 'pending')
                                            Dokumen Persyaratan Menunggu Pemeriksaan Panitia
                                        @else
                                            Berkas Syarat Administrasi Belum Lengkap / Ditolak
                                        @endif
                                    </h3>
                                    <div class="mt-1 text-sm text-yellow-800">
                                        @if($statusVerifikasi === 'pending')
                                            <p>File unggahan Anda sedang diperiksa oleh tim pokja. Fitur absensi and pengerjaan tugas akan otomatis terbuka setelah berkas dinyatakan sah.</p>
                                        @else
                                            <p>Anda wajib melampirkan berkas dokumen persyaratan (Surat Tugas / SPPD resmi) terlebih dahulu untuk dapat mengikuti rangkaian kegiatan ini.</p>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">
                                            Buka Meja Unggah Berkas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- 💡 KONFIGURASI KELAS: Switcher Penugasan (has_tugas) AUTO-SUBMIT --}}
                    @if($canManage)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                            <form action="{{ route('bimtek.toggle-tugas', $bimtek->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">Konfigurasi Penugasan Peserta</h3>
                                        <p class="text-sm text-gray-500 max-w-2xl leading-relaxed">
                                            Tentukan apakah kegiatan Bimbingan Teknis ini mewajibkan peserta untuk mengerjakan tugas. 
                                            Pengaturan ini hanya dapat diubah selama kelas masih berada di fase <strong class="font-semibold">Persiapan</strong>.
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center shrink-0">
                                        {{-- Toggle Switcher Bawaan HTML & Tailwind (Anti Gagal) --}}
                                        <label class="relative inline-flex items-center cursor-pointer {{ $bimtek->status !== 'persiapan' ? 'opacity-50' : '' }}">
                                            
                                            {{-- Input Checkbox Asli (Disembunyikan tapi menjadi otak utama) --}}
                                            <input type="checkbox" name="has_tugas" value="1" class="sr-only peer" 
                                                   {{ $bimtek->has_tugas ? 'checked' : '' }}
                                                   {{ $bimtek->status !== 'persiapan' ? 'disabled' : '' }}
                                                   onchange="this.form.submit()">
                                            
                                            {{-- Desain Visual Sakelar --}}
                                            <div class="relative w-11 h-6 bg-gray-300 rounded-full transition-colors duration-200 ease-in-out peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-500 peer-focus:ring-offset-2 peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:bg-white after:rounded-full after:shadow after:transition-transform after:duration-200 after:ease-in-out peer-checked:after:translate-x-5"></div>
                                            
                                            {{-- Indikator Teks di Kanan Sakelar --}}
                                            <span class="ml-3 text-sm font-semibold {{ $bimtek->has_tugas ? 'text-primary-600' : 'text-gray-400' }}">
                                                {{ $bimtek->has_tugas ? 'AKTIF' : 'NONAKTIF' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Pesan Peringatan Saat Penugasan Aktif (Tampil langsung dari Backend) --}}
                                @if($bimtek->has_tugas)
                                    <div class="mt-5 pt-4 border-t border-gray-200">
                                        <div class="p-3 bg-primary-50 border border-primary-100 text-primary-800 text-sm rounded-lg flex">
                                            <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="text-sm leading-relaxed">
                                                <span class="font-semibold block mb-1">Tab Kelola Tugas Telah Terbuka</span>
                                                Pastikan Anda telah mengunggah minimal 1 (satu) formulir penugasan mandiri pada tab <strong class="font-semibold">Lembar Tugas Belajar</strong> di bawah sebelum memulai acara.
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endif

                    @if($canManage && !auth()->user()->isAdminIt())
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                            <form action="{{ route('bimtek.toggle-sertifikat', $bimtek->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">
                                            Konfigurasi Sertifikat
                                        </h3>
                                        <p class="text-sm text-gray-500 max-w-2xl leading-relaxed">
                                            Tentukan apakah peserta yang memenuhi kriteria kelulusan dapat memperoleh e-sertifikat.
                                            Pengaturan hanya dapat diubah selama fase
                                            <strong class="font-semibold">Persiapan</strong>.
                                        </p>
                                    </div>

                                    <div class="flex items-center shrink-0">
                                        <label class="relative inline-flex items-center cursor-pointer {{ $bimtek->status !== 'persiapan' ? 'opacity-50' : '' }}">
                                            <input type="checkbox"
                                                name="has_sertifikat"
                                                value="1"
                                                class="sr-only peer"
                                                {{ $bimtek->has_sertifikat ? 'checked' : '' }}
                                                {{ $bimtek->status !== 'persiapan' ? 'disabled' : '' }}
                                                onchange="this.form.submit()">

                                            {{-- Desain Visual Sakelar --}}
                                            <div class="relative w-11 h-6 bg-gray-300 rounded-full transition-colors duration-200 ease-in-out peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-500 peer-focus:ring-offset-2 peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:bg-white after:rounded-full after:shadow after:transition-transform after:duration-200 after:ease-in-out peer-checked:after:translate-x-5"></div>

                                            <span class="ml-3 text-sm font-semibold {{ $bimtek->has_sertifikat ? 'text-primary-600' : 'text-gray-400' }}">
                                                {{ $bimtek->has_sertifikat ? 'AKTIF' : 'NONAKTIF' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Manajemen Transisi State Aksi PIC/Panitia --}}
                    @if($canManage)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Panel Kontrol Operasional Kegiatan</h3>
                            @php
                                $tahapan = [
                                    'persiapan'         => 'Persiapan',
                                    'registrasi'        => 'Registrasi',
                                    'persiapan_selesai' => 'Persiapan Selesai',
                                    'berlangsung'       => 'Berlangsung',
                                    'selesai'           => 'Selesai',
                                ];
                                $dibatalkan = $bimtek->status === 'dibatalkan';
                                $idxAktif   = array_search($bimtek->status, array_keys($tahapan));
                                $idxAktif   = $idxAktif === false ? -1 : $idxAktif;
                                $semuaSelesai = $bimtek->status === 'selesai';
                            @endphp

                            {{-- Stepper Tahapan Pelaksanaan --}}
                            <div class="mb-6">
                                @if($dibatalkan)
                                    <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-800 text-sm rounded-lg">
                                        <i class="fas fa-ban mr-1"></i> <span class="font-semibold">Pelaksanaan Bimtek ini telah dibatalkan.</span>
                                    </div>
                                @endif

                                <ol class="flex items-start w-full {{ $dibatalkan ? 'opacity-50' : '' }}">
                                    @foreach($tahapan as $key => $label)
                                        @php
                                            $i        = $loop->index;
                                            $selesai  = !$dibatalkan && ($i < $idxAktif || $semuaSelesai);
                                            $aktif    = !$dibatalkan && !$semuaSelesai && $i === $idxAktif;
                                            $tercapai = !$dibatalkan && $i <= $idxAktif;
                                        @endphp
                                        <li class="relative flex-1 flex flex-col items-center text-center">
                                            @if(!$loop->first)
                                                <div class="absolute top-4 -left-1/2 w-full h-0.5 {{ $tercapai ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                                            @endif

                                            <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold
                                                {{ $selesai ? 'bg-primary-600 text-white' : ($aktif ? 'bg-white text-primary-600 border-2 border-primary-600 ring-4 ring-primary-100' : 'bg-white text-gray-400 border-2 border-gray-200') }}">
                                                @if($selesai)
                                                    <i class="fas fa-check text-xs"></i>
                                                @else
                                                    {{ $i + 1 }}
                                                @endif
                                            </div>

                                            <span class="mt-2 px-1 text-xs leading-tight {{ $aktif ? 'font-bold text-primary-700' : ($selesai ? 'font-medium text-gray-700' : 'text-gray-400') }}">
                                                {{ $label }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                            {{-- Tangkap Pesan Error/Success dari Controller --}}
                            @if(session('error'))
                                <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-800 text-sm rounded-lg">
                                    <strong class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Transisi Ditolak:</strong> {!! session('error') !!}
                                </div>
                            @endif

                            {{-- 💡 TAMBAHAN: Tangkap Array Error Validasi dari Model Bimtek --}}
                            @if(session('error_validasi'))
                                <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-800 text-sm rounded-lg">
                                    <span class="font-semibold"><i class="fas fa-clipboard-list mr-1"></i> Peralihan status ditolak karena beberapa syarat berikut belum terpenuhi:</span>
                                    <ul class="mt-2 list-disc list-inside text-sm space-y-1">
                                        @foreach(session('error_validasi') as $errorMessage)
                                            <li>{{ $errorMessage }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="mb-4 p-3 bg-green-50 border border-green-100 text-green-800 text-sm rounded-lg">
                                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-3">
                                
                                {{-- TAHAP 1: PERSIAPAN -> REGISTRASI --}}
                                @if($bimtek->status === 'persiapan')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="registrasi">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white" onclick="return confirm('Apakah Anda yakin ingin membuka fase pendaftaran peserta?')">
                                            Buka Registrasi Peserta
                                        </button>
                                    </form>
                                @endif

                                {{-- TAHAP 2: REGISTRASI -> PERSIAPAN SELESAI --}}
                                @if($bimtek->status === 'registrasi')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="persiapan_selesai">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white" onclick="return confirm('Kunci pendaftaran dan selesaikan tahapan persiapan?')">
                                            Selesaikan Persiapan
                                        </button>
                                    </form>
                                @endif

                                {{-- TAHAP 3: PERSIAPAN SELESAI -> BERLANGSUNG --}}
                                @if($bimtek->status === 'persiapan_selesai')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="berlangsung">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white" onclick="return confirm('Mulai pelaksanaan kegiatan Bimtek sekarang?')">
                                            Mulai Kick-Off Kegiatan
                                        </button>
                                    </form>
                                @endif

                                {{-- TAHAP 4: BERLANGSUNG -> SELESAI --}}
                                @if($bimtek->status === 'berlangsung')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white" onclick="return confirm('Selesaikan kelas? Seluruh rekap matriks nilai akan dikunci untuk penerbitan sertifikat.')">
                                            Tutup & Selesaikan Kelas
                                        </button>
                                    </form>
                                @endif

                                {{-- TOMBOL PEMBATALAN (Hanya untuk PIC Utama, tidak bisa jika sudah selesai/batal) --}}
                                @if($isPic && !in_array($bimtek->status, ['selesai', 'dibatalkan']))
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="dibatalkan">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-red-600 hover:bg-red-700 text-white" onclick="return confirm('Apakah Anda yakin ingin MEMBATALKAN pelaksanaan bimtek ini?')">
                                            Batalkan Pelaksanaan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- 💡 GAYA KUSTOM: Lenyapkan rel scrollbar bawaan sistem peramban secara permanen -->
                    <style>
                        .no-scrollbar::-webkit-scrollbar { display: none; }
                        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                    </style>

                    {{-- Sistem Navigasi Tab Konten Pengajaran (Alpine.js) --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100" x-data="{ tab: 'materi' }">
                        <div class="border-b border-gray-200 px-6">
                            {{-- Navigasi Tab Utama: Menggunakan no-scrollbar agar tombol geser manual hilang --}}
                            <nav class="flex space-x-6 overflow-x-auto no-scrollbar -mb-px text-sm font-medium" aria-label="Tabs">
                                <button @click="tab = 'materi'" :class="tab === 'materi' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3 px-1 border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                                    Materi
                                </button>
                                @if($bimtek->has_tugas)
                                    <button @click="tab = 'tugas'" :class="tab === 'tugas' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3 px-1 border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                                        Penugasan
                                    </button>
                                @endif
                                <button @click="tab = 'absensi'" :class="tab === 'absensi' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3 px-1 border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                                    Presensi
                                </button>
                                <button @click="tab = 'peserta_tab'" :class="tab === 'peserta_tab' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3 px-1 border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                                    Peserta
                                </button>
                                @if($bimtek->has_sertifikat)
                                    <button @click="tab = 'sertifikat'" :class="tab === 'sertifikat' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3 px-1 border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                                        Sertifikat
                                    </button>
                                @endif
                            </nav>
                        </div>

                        {{-- Wadah Utama Konten --}}
                        <div class="p-6">
                            
                            {{-- ==================== [TAB 1] SILABUS MATERI ==================== --}}
                            <div x-show="tab === 'materi'" x-cloak>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 mb-6 gap-3">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-base font-bold text-gray-800">Materi dan Panduan</h3>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $bimtek->materis->count() }} Berkas
                                        </span>
                                    </div>
                                    <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                        Buka Modul Materi →
                                    </a>
                                </div>
                                
                                @if($bimtek->materis->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($bimtek->materis->take(5) as $materi)
                                            <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                                <div class="flex items-center min-w-0 mr-4 gap-2">
                                                    <span class="text-sm font-medium text-gray-900 truncate">{{ $materi->judul }}</span>
                                                    <span class="shrink-0 px-2 py-1 rounded-full text-xs font-semibold capitalize {{ $materi->tipe === 'materi' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                                                        {{ $materi->tipe }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center shrink-0">
                                                    <a href="{{ route('bimtek.materi.download', [$bimtek->id, $materi->id]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Unduh</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        <p class="mt-2 text-sm text-gray-500">Belum ada unggahan berkas bahan ajar silabus.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- ==================== [TAB 2] LEMBAR TUGAS ==================== --}}
                            @if($bimtek->has_tugas)
                                <div x-show="tab === 'tugas'" x-cloak>
                                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m0-6v2m0-6h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/></svg>
                                            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">Akses tugas dikunci. Selesaikan verifikasi berkas administrasi Anda terlebih dahulu.</p>
                                        </div>
                                    @else
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 mb-6 gap-3">
                                            <div class="flex items-center gap-3">
                                                <h3 class="text-base font-bold text-gray-800">Lembar Tugas Mandiri</h3>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    {{ $bimtek->tugas->count() }} Modul
                                                </span>
                                            </div>
                                            <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                                Buka Modul Tugas →
                                            </a>
                                        </div>
                                        
                                        @if($bimtek->tugas->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($bimtek->tugas->take(5) as $tugas)
                                                    <div class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors flex justify-between items-center">
                                                        <div class="min-w-0 flex flex-col">
                                                            <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 transition-colors truncate">
                                                                {{ $tugas->judul }}
                                                            </a>
                                                            <span class="text-sm text-gray-500 mt-1">Batas Pengumpulan: {{ $tugas->deadline?->format('d M Y, H:i') }} WIB</span>
                                                        </div>
                                                        <span class="text-sm font-medium text-primary-600 shrink-0">Buka</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-8">
                                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                                <p class="mt-2 text-sm text-gray-500">Belum ada tugas pengayaan yang diterbitkan oleh instruktur.</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            {{-- ==================== [TAB 3] LEMBAR PRESENSI ==================== --}}
                            <div x-show="tab === 'absensi'" x-cloak>
                                @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m0-6v2m0-6h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/></svg>
                                        <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">Akses lembar presensi dikunci hingga berkas administrasi Anda disetujui panitia.</p>
                                    </div>
                                @else
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 mb-6 gap-3">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-base font-bold text-gray-800">Sesi Presensi Harian</h3>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ $bimtek->sesiAbsensis->count() }} Sesi
                                            </span>
                                        </div>
                                        <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                            Kelola Sesi Presensi →
                                        </a>
                                    </div>
                                    
                                    @if($bimtek->sesiAbsensis->count() > 0)
                                        <div class="space-y-3">
                                            @foreach($bimtek->sesiAbsensis->take(5) as $sesi)
                                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                    <div class="min-w-0 flex flex-col">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $sesi->nama_sesi }}</h4>
                                                        <span class="text-sm text-gray-500 mt-0.5">Dibuat: {{ $sesi->created_at->format('d M Y, H:i') }} WIB</span>
                                                    </div>
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold shrink-0 {{ $sesi->isOpen() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $sesi->isOpen() ? 'Terbuka' : 'Ditutup' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="mt-2 text-sm text-gray-500">Belum ada lembar sesi absensi hari ini.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- ==================== [TAB 4] ANGGOTA KELAS ==================== --}}
                            <div x-show="tab === 'peserta_tab'" x-cloak>
                                @php
                                    $countPic = $bimtek->pic_user_id ? 1 : 0;
                                    $countPanitia = $bimtek->panitia->count();
                                    $countPeserta = $bimtek->peserta->count();
                                @endphp
                                
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 mb-6 gap-3">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-base font-bold text-gray-800">Manajemen Anggota Kelas</h3>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3 mb-5">
                                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                                        <div class="text-base font-semibold text-gray-800">{{ $countPic }}</div>
                                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">PIC Utama</div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                                        <div class="text-base font-semibold text-gray-800">{{ $countPanitia }}</div>
                                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">Tim Pokja</div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                                        <div class="text-base font-semibold text-gray-800">{{ $countPeserta }}</div>
                                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">Peserta</div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    @if($bimtek->pic)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; flex: none !important;" class="rounded-full bg-primary-100 flex items-center justify-center font-semibold text-primary-800 text-xs">PIC</div>
                                                <div class="min-w-0 flex flex-col">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate leading-tight">{{ $bimtek->pic->name }}</h4>
                                                    <p class="text-sm text-gray-500 mt-0.5">Penanggung Jawab Utama</p>
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 bg-primary-100 text-primary-800 text-xs font-semibold rounded-full shrink-0">PIC</span>
                                        </div>
                                    @endif

                                    @foreach($bimtek->panitia->take(3) as $p)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; flex: none !important;" class="rounded-full bg-secondary-100 flex items-center justify-center font-semibold text-secondary-800 text-xs">{{ strtoupper(substr($p->name, 0, 2)) }}</div>
                                                <div class="min-w-0 flex flex-col">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate leading-tight">{{ $p->name }}</h4>
                                                    <p class="text-sm text-gray-500 mt-0.5">{{ $p->pivot->fungsi_panitia ?? 'Panitia Pelaksana' }}</p>
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 bg-secondary-100 text-secondary-800 text-xs font-semibold rounded-full shrink-0">Tim Pokja</span>
                                        </div>
                                    @endforeach

                                    @foreach($bimtek->peserta->take(3) as $pe)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; flex: none !important;" class="rounded-full bg-gray-100 flex items-center justify-center font-semibold text-gray-800 text-xs">PS</div>
                                                <div class="min-w-0 flex flex-col">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate leading-tight">{{ $pe->name }}</h4>
                                                    <p class="text-sm text-gray-500 mt-0.5">Peserta Kegiatan</p>
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full shrink-0">Peserta</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- ==================== [TAB 5] KELULUSAN SERTIFIKAT ==================== --}}
                            @if($bimtek->has_sertifikat)
                                <div x-show="tab === 'sertifikat'" x-cloak>
                                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m0-6v2m0-6h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/></svg>
                                            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">Modul sertifikat kelulusan terkunci hingga seluruh berkas administrasi diverifikasi oleh panitia.</p>
                                        </div>
                                    @else
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 mb-6 gap-3">
                                            <div class="flex items-center gap-3">
                                                <h3 class="text-base font-bold text-gray-800">Penerbitan Sertifikat Kelulusan</h3>
                                            </div>
                                            <a href="{{ route('bimtek.sertifikat.index', $bimtek->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                                Kelola Kelulusan →
                                            </a>
                                        </div>
                                        
                                        @if($bimtek->sertifikats->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($bimtek->sertifikats->take(5) as $cert)
                                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                        <div class="min-w-0 flex flex-col">
                                                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $cert->user->name ?? '-' }}</h4>
                                                            <span class="text-sm text-gray-500 mt-0.5">No: {{ $cert->nomor_sertifikat }}</span>
                                                        </div>
                                                        <span class="text-xs font-semibold text-green-800 bg-green-100 px-2 py-1 rounded-full shrink-0">Tersedia</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-8">
                                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                                <p class="mt-2 text-sm text-gray-500">Belum ada lembar sertifikat kelulusan yang diterbitkan.</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kanan / Sidebar: Detail Keanggotaan Pokja Kerja --}}
                <div class="space-y-6">
                    {{-- Meja Widget Shortcut Pemeriksaan Berkas --}}
                    @if($bimtek->butuh_verifikasi_dokumen && $canManage)
                        <div class="bg-gradient-to-r from-primary-600 to-primary-500 overflow-hidden shadow-lg rounded-xl p-6 text-white">
                            <div class="flex items-center mb-2">
                                
                                <h3 class="text-base font-bold">Verifikasi Dokumen Persyaratan Peserta</h3>
                            </div>
                            
                            @php
                                $pendingCount = Illuminate\Support\Facades\DB::table('bimtek_pesertas')->where('bimtek_id', $bimtek->id)->where('status_verifikasi', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <div class="mb-4 p-3 bg-white/10 border border-white/20 rounded-lg text-sm font-semibold">
                                    Ada {{ $pendingCount }} berkas peserta mengantre untuk ditinjau.
                                </div>
                            @endif
                            <a href="{{ route('bimtek.verifikasi-dokumen.index', $bimtek->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                                Periksa Berkas
                            </a>
                        </div>
                    @endif

                    {{-- 💡 PERBAIKAN 2: Susunan Tim Kerja Panitia --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Panitia</h3>
                            @if($isPic && $bimtek->status === 'persiapan')
                                <button x-data @click="$dispatch('open-modal', 'assign-panitia')" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors shrink-0 ml-3">+ Tambah</button>
                            @endif
                        </div>
                        <div class="p-6 space-y-3">
                            @if($bimtek->panitia->count() > 0)
                                @foreach($bimtek->panitia as $panitiaUser)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3 min-w-0">
                                            {{-- Ikon Bulat Sempurna Mutlak Tanpa Distorsi/Melar --}}
                                            <div style="width: 36px !important; height: 36px !important; min-width: 36px !important; min-height: 36px !important; max-width: 36px !important; max-height: 36px !important; align-self: center !important; flex: none !important;" 
                                                 class="rounded-full bg-secondary-100 flex items-center justify-center font-semibold text-secondary-800 text-xs">
                                                {{ strtoupper(substr($panitiaUser->name, 0, 2)) }}
                                            </div>
                                            {{-- Hierarki Tipografi: Nama Dominan, Jabatan Lebih Kecil Redup --}}
                                            <div class="min-w-0 flex flex-col">
                                                <h4 class="text-sm font-medium text-gray-900 truncate leading-tight">
                                                    {{ $panitiaUser->name }}
                                                </h4>
                                                <p class="text-sm text-gray-500 truncate mt-0.5">
                                                    {{ $panitiaUser->pivot->fungsi_panitia ?? 'Anggota Pelaksana' }}
                                                </p>
                                            </div>
                                        </div>
                                        {{-- Perbaikan Rute Copot Menggunakan POST & name Valid --}}
                                        @if($isPic && $bimtek->status === 'persiapan')
                                            <form action="{{ route('bimtek.remove-peserta', [$bimtek->id, $panitiaUser->id]) }}" method="POST" onsubmit="return confirm('Cabut kewenangan kepanitiaan pegawai ini?')" class="shrink-0 ml-3">
                                                @csrf
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="mt-2 text-sm text-gray-500">Belum ada tim panitia pelaksana yang didaftarkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 💡 📄 KARTU BARU: Daftar Pemateri / Narasumber --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Pemateri</h3>
                            @if($canManage && in_array($bimtek->status, ['disetujui_final', 'persiapan']))
                                <button x-data @click="$dispatch('open-modal', 'add-pemateri-modal')" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors shrink-0 ml-3">
                                    + Tambah
                                </button>
                            @endif
                        </div>
                        <div class="p-6 space-y-3">
                            @if($bimtek->pemateris->count() > 0)
                                @foreach($bimtek->pemateris as $pemateri)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3 min-w-0">
                                            {{-- Ikon Avatar Bulat Sempurna Absolut Anti-Penyet --}}
                                            <div style="width: 36px !important; height: 36px !important; min-width: 36px !important; min-height: 36px !important; max-width: 36px !important; max-height: 36px !important; align-self: center !important; flex: none !important;" 
                                                 class="rounded-full bg-primary-100 flex items-center justify-center font-semibold text-primary-800 text-xs">
                                                {{ strtoupper(substr($pemateri->nama_pemateri, 0, 2)) }}
                                            </div>
                                            {{-- Detail Info Pemateri --}}
                                            <div class="min-w-0 flex flex-col">
                                                <h4 class="text-sm font-medium text-gray-900 truncate leading-tight">
                                                    {{ $pemateri->nama_pemateri }}
                                                </h4>
                                                <p class="text-sm text-gray-500 truncate mt-0.5">
                                                    {{ $pemateri->asal_instansi }}
                                                </p>
                                            </div>
                                        </div>
                                        {{-- Aksi Copot Pemateri --}}
                                        @if($canManage && in_array($bimtek->status, ['disetujui_final', 'persiapan']))
                                            <form action="{{ route('bimtek.remove-pemateri', [$bimtek->id, $pemateri->id]) }}" method="POST" onsubmit="return confirm('Hapus narasumber ini dari daftar pengajaran kelas?')" class="shrink-0 ml-3">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="mt-2 text-sm text-gray-500">Belum ada pemateri yang didaftarkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Daftar Ringkas Peserta Terdaftar --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Peserta ({{ $bimtek->peserta->count() }})</h3>
                            @if($canManage)
                                <a href="{{ route('bimtek.peserta.index', $bimtek->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors shrink-0 ml-3">Kelola</a>
                            @endif
                        </div>
                        <div class="p-6">
                            @if($bimtek->peserta->count() > 0)
                                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                                    @foreach($bimtek->peserta->take(8) as $pesertaUser)
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center font-semibold text-gray-800 text-xs flex-shrink-0">
                                                {{ strtoupper(substr($pesertaUser->name, 0, 2)) }}
                                            </div>
                                            <span class="ml-3 text-sm font-medium text-gray-900 truncate">{{ $pesertaUser->name }}</span>
                                        </div>
                                    @endforeach
                                    @if($bimtek->peserta->count() > 8)
                                        <p class="text-sm text-gray-500 text-center pt-2 border-t border-gray-200">+ {{ $bimtek->peserta->count() - 8 }} Aktor Peserta Lainnya</p>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="mt-2 text-sm text-gray-500">Belum ada daftar nama peserta.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INTERFACE PANEL --}}

    {{-- Modal Suntik Data Panitia --}}
    <x-modal name="assign-panitia" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.assign-panitia', $bimtek->id) }}" method="POST" class="p-6">
            @csrf
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambahkan Jajaran Panitia Pokja</h3>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700">Pilih Pegawai Internal:</label>
                <select name="user_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" required>
                    <option value="">-- Pilih Anggota Pegawai --</option>
                    @foreach($availableUsers as $avUser)
                        <option value="{{ $avUser->id }}">{{ $avUser->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700">Fungsi Struktur Jabatan:</label>
                <select name="fungsi_panitia" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" required>
                    <option value="">-- Pilih Penugasan Kerja --</option>
                    <option value="Ketua Pelaksana">Ketua Pelaksana</option>
                    <option value="Sekretaris Pokja">Sekretaris Pokja</option>
                    <option value="Bendahara Pengeluaran">Bendahara Pengeluaran</option>
                    <option value="Anggota Tim Administrasi">Anggota Tim Administrasi</option>
                    <option value="Anggota Tim Teknis Lapangan">Anggota Tim Teknis Lapangan</option>
                </select>
            </div>
            <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">Simpan Data</button>
            </div>
        </form>
    </x-modal>

    {{-- 💡 PERBAIKAN 3: Modal Tunggal Unggah Berkas Surat Undangan --}}
    <x-modal name="upload-undangan-modal" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.upload-draft', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-lg font-bold text-gray-800 mb-2">Unggah Berkas Surat Undangan</h3>
            <p class="text-sm text-gray-500 mb-4 leading-relaxed">Silakan unggah dokumen surat undangan resmi BBPMP dalam format PDF (Maksimal 5MB).</p>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700">Pilih Berkas Lampiran:</label>
                <input type="file" name="surat_draft" accept=".pdf" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" required>
            </div>
            <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">Upload</button>
            </div>
        </form>
    </x-modal>
    {{-- 💡 INTERFACE MODAL: Formulir Suntik Data Pemateri Baru --}}
    <x-modal name="add-pemateri-modal" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.add-pemateri', $bimtek->id) }}" method="POST" class="p-6">
            @csrf
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambahkan Pemateri / Narasumber</h3>
            
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700">Nama Lengkap Pemateri (Beserta Gelar):</label>
                <input type="text" name="nama_pemateri" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Dr. Ir. Hermawan, M.T." required>
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700">Asal Instansi / Lembaga Asal:</label>
                <input type="text" name="asal_instansi" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Direktorat Kebijakan PMP / Universitas Negeri" required>
            </div>
            
            <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">Simpan Data</button>
            </div>
        </form>
    </x-modal>
    <x-modal name="virtual-meeting-modal" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.virtual-meeting.update', $bimtek->id) }}"
            method="POST"
            class="p-6">
            @csrf
            @method('PATCH')

            <h3 class="text-lg font-bold text-gray-800 mb-2">
                {{ $bimtek->virtual_meeting_url
                    ? 'Ubah Link Virtual Meeting'
                    : 'Tambahkan Link Virtual Meeting' }}
            </h3>

            <p class="text-sm text-gray-500 mb-4">
                Tempelkan link Zoom, Google Meet, atau platform virtual meeting lainnya.
            </p>

            <input type="url"
                    name="virtual_meeting_url"
                    value="{{ old('virtual_meeting_url', $bimtek->virtual_meeting_url) }}"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">

            <div class="flex items-center justify-end space-x-3 mt-5 border-t border-gray-200 pt-4">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    Batal
                </button>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">
                    Simpan Link
                </button>
            </div>
        </form>
    </x-modal>
    <x-modal name="rundown-modal" :show="false" maxWidth="lg">
        <form action="{{ route('bimtek.rundown.update', $bimtek->id) }}"
            method="POST"
            class="p-6">
            @csrf
            @method('PATCH')

            <h3 class="text-lg font-bold text-gray-800 mb-2">
                {{ $bimtek->deskripsi_jadwal
                    ? 'Ubah Rundown Pelaksanaan'
                    : 'Isi Rundown Pelaksanaan' }}
            </h3>

            <p class="text-sm text-gray-500 mb-4">
                Tuliskan urutan acara, waktu pelaksanaan, sesi materi, istirahat, atau catatan teknis lainnya.
            </p>

            <textarea name="deskripsi_jadwal"
                    rows="8"
                    maxlength="10000"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Contoh:
    08.00 - 08.30 Registrasi peserta
    08.30 - 09.00 Pembukaan
    09.00 - 10.30 Sesi materi pertama">{{ old('deskripsi_jadwal', $bimtek->deskripsi_jadwal) }}</textarea>

            @error('deskripsi_jadwal')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3 mt-5 border-t border-gray-200 pt-4">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                    Batal
                </button>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800">
                    Simpan Rundown
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>