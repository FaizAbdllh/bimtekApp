<x-app-layout>
    <x-slot name="header">
        Detail Ruang Kelas Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
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

                            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</h2>

                            @php
                                $modeBadge = [
                                    'offline' => 'bg-gray-100 text-gray-800',
                                    'online' => 'bg-secondary-100 text-secondary-800',
                                    'hybrid' => 'bg-green-100 text-green-800',
                                ];
                            @endphp

                            {{-- Grid Informasi Detail Kolom --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-4">
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Mode Pelaksanaan</span>
                                    <span class="inline-flex mt-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $modeBadge[$bimtek->mode_pelaksanaan] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $bimtek->mode_pelaksanaan_label }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Tautan Ruang Virtual</span>
                                    @if($bimtek->virtual_meeting_url)
                                        <a href="{{ $bimtek->virtual_meeting_url }}" target="_blank" rel="noopener" class="text-primary-600 hover:text-primary-800 font-medium block break-all mt-1">Buka Ruang Virtual (Zoom/Teams)</a>
                                    @else
                                        <span class="text-sm font-medium text-gray-900 block mt-1">{{ in_array($bimtek->mode_pelaksanaan, ['online', 'hybrid']) ? 'Belum ditentukan' : '-' }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Lokasi Aktual</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1">{{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Periode Pelaksanaan</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1">
                                        {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('d M Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('d M Y') : '-') }}
                                        @if($bimtek->tanggal_selesai_aktual)
                                            - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Alokasi Anggaran DIPA</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1">Rp {{ number_format($bimtek->kebutuhanAnggarans->sum('total_biaya') ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Sumber Dana / Pagu</span>
                                    <span class="text-sm font-medium text-gray-900 block mt-1 truncate">{{ $bimtek->sumber_pembiayaan ?? '-' }}</span>
                                </div>
                            </div>

                            @if($bimtek->deskripsi_jadwal)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Catatan / Deskripsi Jadwal Aktual</span>
                                    <p class="text-sm text-gray-700 mt-1 leading-relaxed whitespace-pre-line">{{ $bimtek->deskripsi_jadwal }}</p>
                                </div>
                            @endif

                            {{-- Modul Surat Undangan --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Surat Undangan</span>
                                    </div>

                                    @if($bimtek->file_surat_undangan_path)
                                        <div class="flex items-center justify-between text-sm bg-white p-3 rounded-lg border border-gray-200">
                                            <div class="flex items-center min-w-0 mr-4">
                                                <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="truncate text-gray-700 font-medium">
                                                    {{ basename($bimtek->file_surat_undangan_path) }}
                                                </span>
                                            </div>
                                            <div class="flex gap-3 shrink-0 text-sm font-semibold">
                                                <a href="{{ route('bimtek.preview-draft', $bimtek->id) }}" target="_blank" class="text-primary-600 hover:text-primary-800">Lihat</a>
                                                <a href="{{ route('bimtek.download-draft', $bimtek->id) }}" class="text-green-600 hover:text-green-800">Unduh</a>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-500 my-2">Belum ada berkas surat undangan yang diunggah.</p>
                                    @endif

                                    @if($canManage && in_array($bimtek->status, ['disetujui_final', 'persiapan', 'registrasi', 'persiapan_selesai']))
                                        <button type="button" x-data @click="$dispatch('open-modal', 'upload-undangan-modal')" class="w-full mt-3 inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            {{ $bimtek->file_surat_undangan_path ? 'Ganti Berkas Surat Undangan' : 'Unggah Surat Undangan' }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Persyaratan Berkas Masuk Kelas --}}
                            @if($bimtek->butuh_verifikasi_dokumen && $bimtek->syaratDokumens->isNotEmpty())
                                <div class="mt-4 p-5 bg-gray-50 border border-gray-200 rounded-lg">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Persyaratan Berkas Masuk Kelas
                                    </h4>
                                    
                                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($bimtek->syaratDokumens as $syarat)
                                            <li class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-900">
                                                        {{ $syarat->nama_dokumen }}
                                                    </span>
                                                </div>
                                            
                                                @if($syarat->is_wajib)
                                                    <span class="px-2.5 py-0.5 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                                        Wajib
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-0.5 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                                        Opsional
                                                    </span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Tautan Pendaftaran Mandiri --}}
                            @if($canManage && $bimtek->mode_pelaksanaan !== 'internal')
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Tautan Pendaftaran Mandiri Peserta Luar</span>
                                    @if($bimtek->invite_code)
                                        <div class="flex items-center gap-2">
                                            <input id="invite-link" class="flex-1 bg-white border border-gray-300 text-xs py-2 px-3 rounded-lg text-gray-700 font-mono" readonly 
                                                   value="{{ url('/register?code=' . $bimtek->invite_code) }}">
                                            <button onclick="navigator.clipboard.writeText(document.getElementById('invite-link').value); alert('Link berhasil disalin!');" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 hover:bg-gray-100 transition-colors">Salin</button>
                                        </div>
                                    @else
                                        <form action="{{ route('bimtek.generate-invite', $bimtek->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white font-semibold text-xs rounded-lg transition-colors">
                                                Aktifkan Link & Token Registrasi Mandiri
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
                        <div class="p-3 rounded-lg border text-sm bg-orange-50 border-orange-100 text-orange-800 flex items-start">
                            <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <span class="font-semibold">
                                    @if($statusVerifikasi === 'pending')
                                        Dokumen Persyaratan Menunggu Pemeriksaan Panitia
                                    @else
                                        Berkas Syarat Administrasi Belum Lengkap / Ditolak
                                    @endif
                                </span>
                                <p class="mt-1 text-xs leading-relaxed">
                                    @if($statusVerifikasi === 'pending')
                                        File unggahan Anda sedang diperiksa oleh tim pokja. Fitur absensi and pengerjaan tugas akan otomatis terbuka setelah berkas dinyatakan sah.
                                    @else
                                        Anda wajib melampirkan berkas dokumen persyaratan (Surat Tugas / SPPD resmi) terlebih dahulu untuk dapat mengikuti rangkaian kegiatan ini.
                                    @endif
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs rounded-lg transition-colors">
                                        Buka Meja Unggah Berkas
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Konfigurasi Kelas: Switcher Penugasan --}}
                    @if($canManage)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                            <form action="{{ route('bimtek.toggle-tugas', $bimtek->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                    <div class="flex-1">
                                        <h3 class="text-base font-bold text-gray-800 mb-1">Konfigurasi Penugasan Peserta</h3>
                                        <p class="text-xs text-gray-500 max-w-2xl leading-relaxed">
                                            Tentukan apakah kegiatan Bimbingan Teknis ini mewajibkan peserta untuk mengerjakan tugas/RTL. 
                                            Pengaturan ini hanya dapat diubah selama kelas masih berada di fase <span class="font-semibold text-yellow-800">Persiapan</span>.
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center shrink-0">
                                        <label class="relative inline-flex items-center cursor-pointer {{ $bimtek->status !== 'persiapan' ? 'opacity-50' : '' }}">
                                            <input type="checkbox" name="has_tugas" value="1" class="sr-only peer" 
                                                   {{ $bimtek->has_tugas ? 'checked' : '' }}
                                                   {{ $bimtek->status !== 'persiapan' ? 'disabled' : '' }}
                                                   onchange="this.form.submit()">
                                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary-600"></div>
                                            <span class="ml-3 text-xs font-semibold {{ $bimtek->has_tugas ? 'text-primary-600' : 'text-gray-400' }}">
                                                {{ $bimtek->has_tugas ? 'AKTIF' : 'NONAKTIF' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                @if($bimtek->has_tugas)
                                    <div class="mt-5 pt-4 border-t border-gray-100">
                                        <div class="p-3 rounded-lg border text-sm bg-primary-50 border-primary-100 text-primary-800 flex items-start">
                                            <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <div>
                                                <span class="font-semibold">Tab Kelola Tugas Telah Terbuka:</span> Pastikan Anda telah mengunggah minimal 1 (satu) formulir penugasan mandiri pada tab <strong>Lembar Tugas Belajar</strong> di bawah sebelum memulai acara.
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endif

                    {{-- Manajemen Transisi State Aksi PIC/Panitia --}}
                    @if($canManage)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 mb-6">
                            <h3 class="text-base font-bold text-gray-800 mb-3">Panel Kontrol Operasional Kegiatan</h3>
                            
                            @if(session('error'))
                                <div class="mb-4 p-3 rounded-lg border text-sm bg-red-50 border-red-100 text-red-800 flex items-start">
                                    <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div>
                                        <span class="font-semibold">Transisi Ditolak:</span> {!! session('error') !!}
                                    </div>
                                </div>
                            @endif

                            @if(session('error_validasi'))
                                <div class="mb-4 p-3 rounded-lg border text-sm bg-red-50 border-red-100 text-red-800">
                                    <span class="font-semibold">Peralihan status ditolak karena beberapa syarat berikut belum terpenuhi:</span>
                                    <ul class="mt-2 list-disc list-inside space-y-1">
                                        @foreach(session('error_validasi') as $errorMessage)
                                            <li>{{ $errorMessage }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="mb-4 p-3 rounded-lg border text-sm bg-green-50 border-green-100 text-green-800 flex items-start">
                                    <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <div>{{ session('success') }}</div>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-3">
                                @if($bimtek->status === 'persiapan')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="registrasi">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white font-semibold text-xs rounded-lg transition-colors" onclick="return confirm('Apakah Anda yakin ingin membuka fase pendaftaran peserta?')">
                                            Buka Registrasi Peserta
                                        </button>
                                    </form>
                                @endif

                                @if($bimtek->status === 'registrasi')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="persiapan_selesai">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold text-xs rounded-lg transition-colors" onclick="return confirm('Kunci pendaftaran dan selesaikan tahapan persiapan?')">
                                            Selesaikan Persiapan
                                        </button>
                                    </form>
                                @endif

                                @if($bimtek->status === 'persiapan_selesai')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="berlangsung">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-xs rounded-lg transition-colors" onclick="return confirm('Mulai pelaksanaan kegiatan Bimtek sekarang?')">
                                            Mulai Kick-Off Kegiatan
                                        </button>
                                    </form>
                                @endif

                                @if($bimtek->status === 'berlangsung')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white font-semibold text-xs rounded-lg transition-colors" onclick="return confirm('Selesaikan kelas? Seluruh rekap matriks nilai akan dikunci untuk penerbitan sertifikat.')">
                                            Tutup & Selesaikan Kelas
                                        </button>
                                    </form>
                                @endif

                                @if($isPic && !in_array($bimtek->status, ['selesai', 'dibatalkan']))
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="dibatalkan">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg transition-colors" onclick="return confirm('Apakah Anda yakin ingin MEMBATALKAN pelaksanaan bimtek ini?')">
                                            Batalkan Pelaksanaan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    <style>
                        .no-scrollbar::-webkit-scrollbar { display: none; }
                        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                    </style>

                    {{-- Sistem Navigasi Tab Konten Pengajaran (Alpine.js) --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100" x-data="{ tab: 'materi' }">
                        <div class="border-b border-gray-100 bg-gray-50 px-2">
                            <nav class="flex space-x-2 overflow-x-auto no-scrollbar -mb-px" aria-label="Tabs">
                                <button @click="tab = 'materi'" :class="tab === 'materi' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 border-b-2 text-sm whitespace-nowrap transition-colors">
                                    Silabus Materi
                                </button>
                                @if($bimtek->has_tugas)
                                    <button @click="tab = 'tugas'" :class="tab === 'tugas' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 border-b-2 text-sm whitespace-nowrap transition-colors">
                                        Lembar Tugas Belajar
                                    </button>
                                @endif
                                <button @click="tab = 'absensi'" :class="tab === 'absensi' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 border-b-2 text-sm whitespace-nowrap transition-colors">
                                    Lembar Presensi
                                </button>
                                <button @click="tab = 'peserta_tab'" :class="tab === 'peserta_tab' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 border-b-2 text-sm whitespace-nowrap transition-colors">
                                    Anggota Kelas
                                </button>
                                @if($bimtek->has_sertifikat)
                                    <button @click="tab = 'sertifikat'" :class="tab === 'sertifikat' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 border-b-2 text-sm whitespace-nowrap transition-colors">
                                        Kelulusan Sertifikat
                                    </button>
                                @endif
                            </nav>
                        </div>

                        <div class="p-6">
                            {{-- [TAB 1] SILABUS MATERI --}}
                            <div x-show="tab === 'materi'" x-cloak>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-gray-100 mb-5 gap-3">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-base font-bold text-gray-800">Silabus & Bahan Ajar</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            {{ $bimtek->materis->count() }} Berkas
                                        </span>
                                    </div>
                                    <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                        Buka Modul Materi →
                                    </a>
                                </div>
                                
                                @if($bimtek->materis->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($bimtek->materis->take(5) as $materi)
                                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-white">
                                                <div class="flex items-center min-w-0 mr-4 gap-2">
                                                    <span class="text-sm font-medium text-gray-900 truncate">{{ $materi->judul }}</span>
                                                    <span class="shrink-0 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $materi->tipe === 'materi' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                                                        {{ $materi->tipe }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center text-xs font-semibold shrink-0">
                                                    <a href="{{ route('bimtek.materi.download', [$bimtek->id, $materi->id]) }}" class="text-primary-600 hover:text-primary-800 bg-primary-50 px-3 py-1.5 rounded-lg transition-colors">Unduh</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        <p class="mt-2 text-sm text-gray-500">Belum ada unggahan berkas bahan ajar silabus.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- [TAB 2] LEMBAR TUGAS --}}
                            @if($bimtek->has_tugas)
                                <div x-show="tab === 'tugas'" x-cloak>
                                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                        <div class="flex flex-col items-center justify-center py-8 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6v2m0-6h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/></svg>
                                            <p class="mt-2 text-sm text-gray-500">Akses tugas dikunci. Selesaikan verifikasi berkas administrasi Anda terlebih dahulu.</p>
                                        </div>
                                    @else
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-gray-100 mb-5 gap-3">
                                            <div class="flex items-center gap-3">
                                                <h3 class="text-base font-bold text-gray-800">Lembar Tugas Mandiri</h3>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                    {{ $bimtek->tugas->count() }} Modul
                                                </span>
                                            </div>
                                            <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                                Buka Modul Tugas →
                                            </a>
                                        </div>
                                        
                                        @if($bimtek->tugas->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($bimtek->tugas->take(5) as $tugas)
                                                    <div class="p-3 border border-gray-200 rounded-lg bg-white flex justify-between items-center">
                                                        <div class="min-w-0 flex flex-col">
                                                            <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="text-sm font-semibold text-gray-900 hover:text-primary-600 transition-colors truncate">
                                                                {{ $tugas->judul }}
                                                            </a>
                                                            <span class="text-xs text-gray-500 mt-0.5">Batas Pengumpulan: {{ $tugas->deadline?->format('d M Y, H:i') }} WIB</span>
                                                        </div>
                                                        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-lg shrink-0">Buka</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                                <p class="mt-2 text-sm text-gray-500">Belum ada tugas pengayaan yang diterbitkan oleh instruktur.</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            {{-- [TAB 3] LEMBAR PRESENSI --}}
                            <div x-show="tab === 'absensi'" x-cloak>
                                @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                    <div class="flex flex-col items-center justify-center py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6v2m0-6h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/></svg>
                                        <p class="mt-2 text-sm text-gray-500">Akses lembar presensi dikunci hingga berkas administrasi Anda disetujui panitia.</p>
                                    </div>
                                @else
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-gray-100 mb-5 gap-3">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-base font-bold text-gray-800">Sesi Presensi Harian</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                {{ $bimtek->sesiAbsensis->count() }} Sesi
                                            </span>
                                        </div>
                                        <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                            Kelola Sesi Presensi →
                                        </a>
                                    </div>
                                    
                                    @if($bimtek->sesiAbsensis->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($bimtek->sesiAbsensis->take(5) as $sesi)
                                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-white">
                                                    <div class="min-w-0 flex flex-col">
                                                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $sesi->nama_sesi }}</h4>
                                                        <span class="text-xs text-gray-500 mt-0.5">Dibuat: {{ $sesi->created_at->format('d M Y, H:i') }} WIB</span>
                                                    </div>
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase shrink-0 {{ $sesi->isOpen() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $sesi->isOpen() ? 'Terbuka' : 'Ditutup' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center justify-center py-8 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="mt-2 text-sm text-gray-500">Belum ada lembar sesi absensi hari ini.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- [TAB 4] ANGGOTA KELAS --}}
                            <div x-show="tab === 'peserta_tab'" x-cloak>
                                @php
                                    $countPic = $bimtek->pic_user_id ? 1 : 0;
                                    $countPanitia = $bimtek->panitia->count();
                                    $countPeserta = $bimtek->peserta->count();
                                @endphp
                                
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-gray-100 mb-5 gap-3">
                                    <h3 class="text-base font-bold text-gray-800">Manajemen Anggota Kelas</h3>
                                </div>

                                <div class="grid grid-cols-3 gap-3 mb-5">
                                    <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-center">
                                        <div class="text-xl font-bold text-gray-800">{{ $countPic }}</div>
                                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5">PIC Utama</div>
                                    </div>
                                    <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-center">
                                        <div class="text-xl font-bold text-gray-800">{{ $countPanitia }}</div>
                                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Tim Pokja</div>
                                    </div>
                                    <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-center">
                                        <div class="text-xl font-bold text-gray-800">{{ $countPeserta }}</div>
                                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Peserta</div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    @if($bimtek->pic)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; flex: none !important;" class="rounded-full bg-secondary-100 flex items-center justify-center font-semibold text-secondary-800 text-xs">PIC</div>
                                                <div class="min-w-0 flex flex-col">
                                                    <h4 class="text-sm font-semibold text-gray-900 truncate leading-tight">{{ $bimtek->pic->name }}</h4>
                                                    <p class="text-xs text-gray-500 mt-0.5">Penanggung Jawab Utama</p>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 bg-secondary-100 text-secondary-800 text-xs font-semibold rounded-full shrink-0">PIC</span>
                                        </div>
                                    @endif

                                    @foreach($bimtek->panitia->take(3) as $p)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-white">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; flex: none !important;" class="rounded-full bg-gray-100 flex items-center justify-center font-semibold text-gray-700 text-xs">{{ strtoupper(substr($p->name, 0, 2)) }}</div>
                                                <div class="min-w-0 flex flex-col">
                                                    <h4 class="text-sm font-semibold text-gray-900 truncate leading-tight">{{ $p->name }}</h4>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $p->pivot->fungsi_panitia ?? 'Panitia Pelaksana' }}</p>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full shrink-0">Tim Pokja</span>
                                        </div>
                                    @endforeach

                                    @foreach($bimtek->peserta->take(3) as $pe)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-white">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; flex: none !important;" class="rounded-full bg-primary-100 flex items-center justify-center font-semibold text-primary-800 text-xs">PS</div>
                                                <div class="min-w-0 flex flex-col">
                                                    <h4 class="text-sm font-semibold text-gray-900 truncate leading-tight">{{ $pe->name }}</h4>
                                                    <p class="text-xs text-gray-500 mt-0.5">Peserta Kegiatan</p>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 bg-primary-100 text-primary-800 text-xs font-semibold rounded-full shrink-0">Peserta</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- [TAB 5] KELULUSAN SERTIFIKAT --}}
                            @if($bimtek->has_sertifikat)
                                <div x-show="tab === 'sertifikat'" x-cloak>
                                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                        <div class="flex flex-col items-center justify-center py-8 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6v2m0-6h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/></svg>
                                            <p class="mt-2 text-sm text-gray-500">Modul sertifikat kelulusan terkunci hingga seluruh berkas administrasi diverifikasi oleh panitia.</p>
                                        </div>
                                    @else
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-gray-100 mb-5 gap-3">
                                            <div class="flex items-center gap-3">
                                                <h3 class="text-base font-bold text-gray-800">Penerbitan Sertifikat Kelulusan</h3>
                                            </div>
                                            <a href="{{ route('bimtek.sertifikat.index', $bimtek->id) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800 flex items-center gap-1 transition-colors shrink-0">
                                                Kelola Kelulusan →
                                            </a>
                                        </div>
                                        
                                        @if($bimtek->sertifikats->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($bimtek->sertifikats->take(5) as $cert)
                                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-white">
                                                        <div class="min-w-0 flex flex-col">
                                                            <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $cert->user->name ?? '-' }}</h4>
                                                            <span class="text-xs text-gray-500 font-mono mt-0.5">No: {{ $cert->nomor_sertifikat }}</span>
                                                        </div>
                                                        <span class="text-xs font-semibold text-green-800 bg-green-100 px-2.5 py-1 rounded-full shrink-0">Tersedia</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
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
                        <div class="bg-primary-600 overflow-hidden shadow-sm rounded-xl p-5 text-white">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-primary-200 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="font-bold text-base">Verifikasi Dokumen Kelulusan</h3>
                            </div>
                            <p class="text-xs text-primary-100 mb-4 leading-relaxed">Periksa kesahihan Surat Tugas/SPPD yang dikirim oleh peserta luar instansi.</p>
                            @php
                                $pendingCount = Illuminate\Support\Facades\DB::table('bimtek_pesertas')->where('bimtek_id', $bimtek->id)->where('status_verifikasi', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <div class="mb-4 p-3 bg-white/10 border border-white/20 rounded-lg text-xs font-semibold">
                                    Ada {{ $pendingCount }} berkas peserta mengantre untuk ditinjau.
                                </div>
                            @endif
                            <a href="{{ route('bimtek.verifikasi-dokumen.index', $bimtek->id) }}" class="block w-full text-center py-2 bg-white text-primary-700 font-semibold text-sm rounded-lg hover:bg-primary-50 transition-colors">
                                Masuk Meja Pemeriksa
                            </a>
                        </div>
                    @endif

                    {{-- Susunan Tim Kerja Panitia --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h3 class="font-bold text-sm text-gray-800">Susunan Tim Kerja Panitia</h3>
                            @if($isPic && $bimtek->status === 'persiapan')
                                <button x-data @click="$dispatch('open-modal', 'assign-panitia')" class="text-xs font-semibold text-primary-600 hover:text-primary-800 transition-colors">+ Tambah</button>
                            @endif
                        </div>
                        <div class="p-4 space-y-2">
                            @if($bimtek->panitia->count() > 0)
                                @foreach($bimtek->panitia as $panitiaUser)
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; align-self: center !important; flex: none !important;" 
                                                 class="rounded-full bg-gray-100 flex items-center justify-center font-semibold text-gray-700 text-xs border border-gray-200">
                                                {{ strtoupper(substr($panitiaUser->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0 flex flex-col">
                                                <h4 class="text-sm font-medium text-gray-900 truncate leading-tight">
                                                    {{ $panitiaUser->name }}
                                                </h4>
                                                <p class="text-xs text-gray-500 truncate mt-0.5">
                                                    {{ $panitiaUser->pivot->fungsi_panitia ?? 'Anggota Pelaksana' }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($isPic && $bimtek->status === 'persiapan')
                                            <form action="{{ route('bimtek.remove-peserta', [$bimtek->id, $panitiaUser->id]) }}" method="POST" onsubmit="return confirm('Cabut kewenangan kepanitiaan pegawai ini?')" class="shrink-0">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-semibold transition-colors">
                                                    Copot
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-center text-xs text-gray-500 py-3">Belum ada tim panitia pelaksana yang didaftarkan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Daftar Pemateri / Narasumber --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h3 class="font-bold text-sm text-gray-800">Daftar Pemateri / Narasumber</h3>
                            @if($canManage && in_array($bimtek->status, ['disetujui_final', 'persiapan']))
                                <button x-data @click="$dispatch('open-modal', 'add-pemateri-modal')" class="text-xs font-semibold text-primary-600 hover:text-primary-800 transition-colors">
                                    + Tambah
                                </button>
                            @endif
                        </div>
                        <div class="p-4 space-y-2">
                            @if($bimtek->pemateris->count() > 0)
                                @foreach($bimtek->pemateris as $pemateri)
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; align-self: center !important; flex: none !important;" 
                                                 class="rounded-full bg-secondary-100 flex items-center justify-center font-semibold text-secondary-800 text-xs border border-secondary-200">
                                                {{ strtoupper(substr($pemateri->nama_pemateri, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0 flex flex-col">
                                                <h4 class="text-sm font-medium text-gray-900 truncate leading-tight">
                                                    {{ $pemateri->nama_pemateri }}
                                                </h4>
                                                <p class="text-xs text-gray-500 truncate mt-0.5">
                                                    {{ $pemateri->asal_instansi }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($canManage && in_array($bimtek->status, ['disetujui_final', 'persiapan']))
                                            <form action="{{ route('bimtek.remove-pemateri', [$bimtek->id, $pemateri->id]) }}" method="POST" onsubmit="return confirm('Hapus narasumber ini dari daftar pengajaran kelas?')" class="shrink-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-semibold transition-colors">
                                                    Copot
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-center text-xs text-gray-500 py-3">Belum ada pemateri yang didaftarkan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Daftar Ringkas Peserta Terdaftar --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h3 class="font-bold text-sm text-gray-800">Peserta Terdaftar ({{ $bimtek->peserta->count() }})</h3>
                            @if($canManage)
                                <a href="{{ route('bimtek.peserta.index', $bimtek->id) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800 transition-colors">Kelola</a>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($bimtek->peserta->count() > 0)
                                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                                    @foreach($bimtek->peserta->take(8) as $pesertaUser)
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center font-semibold text-primary-800 text-xs flex-shrink-0">
                                                {{ strtoupper(substr($pesertaUser->name, 0, 2)) }}
                                            </div>
                                            <span class="ml-2.5 text-sm font-medium text-gray-700 truncate">{{ $pesertaUser->name }}</span>
                                        </div>
                                    @endforeach
                                    @if($bimtek->peserta->count() > 8)
                                        <p class="text-xs text-gray-500 font-semibold text-center pt-2 border-t border-gray-100">+ {{ $bimtek->peserta->count() - 8 }} Aktor Peserta Lainnya</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-center text-xs text-gray-500 py-3">Belum ada daftar nama peserta.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INTERFACE PANEL --}}
    <x-modal name="assign-panitia" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.assign-panitia', $bimtek->id) }}" method="POST" class="p-6">
            @csrf
            <h3 class="text-base font-bold text-gray-800 mb-4">Tambahkan Jajaran Panitia Pokja</h3>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pilih Pegawai Internal</label>
                <select name="user_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 bg-white" required>
                    <option value="">-- Pilih Anggota Pegawai --</option>
                    @foreach($availableUsers as $avUser)
                        <option value="{{ $avUser->id }}">{{ $avUser->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fungsi Struktur Jabatan</label>
                <select name="fungsi_panitia" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 bg-white" required>
                    <option value="">-- Pilih Penugasan Kerja --</option>
                    <option value="Ketua Pelaksana">Ketua Pelaksana</option>
                    <option value="Sekretaris Pokja">Sekretaris Pokja</option>
                    <option value="Bendahara Pengeluaran">Bendahara Pengeluaran</option>
                    <option value="Anggota Tim Administrasi">Anggota Tim Administrasi</option>
                    <option value="Anggota Tim Teknis Lapangan">Anggota Tim Teknis Lapangan</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white font-semibold text-sm rounded-lg transition-colors">Suntik Data</button>
            </div>
        </form>
    </x-modal>

    <x-modal name="upload-undangan-modal" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.upload-draft', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-base font-bold text-gray-800 mb-2">Unggah Berkas Surat Undangan</h3>
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">Silakan unggah dokumen surat undangan resmi BBPMP dalam format PDF (Maksimal 5MB).</p>
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pilih Berkas Lampiran</label>
                <input type="file" name="surat_draft" accept=".pdf" class="w-full rounded-lg border-gray-300 shadow-sm text-sm" required>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white font-semibold text-sm rounded-lg transition-colors">Mulai Unggah</button>
            </div>
        </form>
    </x-modal>

    <x-modal name="add-pemateri-modal" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.add-pemateri', $bimtek->id) }}" method="POST" class="p-6">
            @csrf
            <h3 class="text-base font-bold text-gray-800 mb-4">Tambahkan Pemateri / Narasumber</h3>
            
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap Pemateri (Beserta Gelar)</label>
                <input type="text" name="nama_pemateri" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 bg-white" placeholder="Dr. Ir. Hermawan, M.T." required>
            </div>
            
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Asal Instansi / Lembaga Asal</label>
                <input type="text" name="asal_instansi" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 bg-white" placeholder="Direktorat Kebijakan PMP / Universitas Negeri" required>
            </div>
            
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white font-semibold text-sm rounded-lg transition-colors">Simpan Data</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>