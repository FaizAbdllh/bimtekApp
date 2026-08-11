<x-app-layout>
    <x-slot name="header">
        Detail Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>

            {{-- Header Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        @php
                            // REFAKTORISASI: Mengubah status_pelaksanaan menjadi status dari tabel tunggal bimteks
                            $statusColors = [
                                'disetujui_final' => 'bg-gray-100 text-gray-800',
                                'persiapan' => 'bg-yellow-100 text-yellow-800',
                                'berlangsung' => 'bg-blue-100 text-blue-800',
                                'selesai' => 'bg-green-100 text-green-800',
                                'dibatalkan' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'disetujui_final' => 'Disetujui',
                                'persiapan' => 'Persiapan',
                                'berlangsung' => 'Berlangsung',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusColors[$bimtek->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$bimtek->status] ?? ucfirst($bimtek->status) }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</h2>

                    @php
                        $modeBadge = [
                            'offline' => 'bg-slate-100 text-slate-800',
                            'online' => 'bg-sky-100 text-sky-800',
                            'hybrid' => 'bg-emerald-100 text-emerald-800',
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm border-t border-gray-50 pt-4">
                        <div>
                            <span class="text-gray-400 font-medium block">Mode Pelaksanaan:</span>
                            <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $modeBadge[$bimtek->mode_pelaksanaan] ?? 'bg-gray-100 text-gray-800' }}">{{ $bimtek->mode_pelaksanaan_label }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium block">Tautan Virtual:</span>
                            @if($bimtek->virtual_meeting_url)
                                <a href="{{ $bimtek->virtual_meeting_url }}" target="_blank" rel="noopener" class="text-primary-600 hover:text-primary-700 font-semibold block break-all mt-0.5">Buka Ruang Virtual (Zoom/Teams)</a>
                            @else
                                <span class="text-gray-400 block mt-0.5">{{ in_array($bimtek->mode_pelaksanaan, ['online', 'hybrid']) ? 'Belum ditentukan' : '-' }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium block">Lokasi:</span>
                            <span class="text-gray-800 font-bold block mt-0.5">{{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium block">Tanggal Pelaksanaan:</span>
                            <span class="text-gray-800 font-bold block mt-0.5">
                                {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('d M Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('d M Y') : '-') }}
                                @if($bimtek->tanggal_selesai_aktual)
                                    - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($bimtek->deskripsi_jadwal)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <span class="text-gray-400 text-xs font-semibold uppercase tracking-wider block">Deskripsi Jadwal:</span>
                            <p class="text-gray-700 mt-1 text-sm leading-relaxed whitespace-pre-line">{{ $bimtek->deskripsi_jadwal }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Verification Alert Section --}}
            @php
                $isPeserta = $bimtek->peserta->contains(auth()->id());
                if ($isPeserta && $bimtek->butuh_verifikasi_dokumen) {
                    // REFAKTORISASI: Membaca status dokumen kelulusan dari tabel bridge baru bimtek_pesertas
                    $pesertaPivot = Illuminate\Support\Facades\DB::table('bimtek_pesertas')
                        ->where('bimtek_id', $bimtek->id)
                        ->where('user_id', auth()->id())
                        ->first();
                    $statusVerifikasi = $pesertaPivot ? $pesertaPivot->status_verifikasi : 'pending';
                } else {
                    $statusVerifikasi = null;
                }
                $isVerified = (!$bimtek->butuh_verifikasi_dokumen || $statusVerifikasi === 'verified' || $statusVerifikasi === 'diverifikasi');
            @endphp

            @if($bimtek->butuh_verifikasi_dokumen && !$isVerified && $isPeserta)
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-xl shadow-sm mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-bold text-amber-900">
                                @if($statusVerifikasi === 'pending')
                                    Dokumen Berkas Sedang Diverifikasi Panitia
                                @else
                                    Verifikasi Berkas Persyaratan Diperlukan
                                @endif
                            </h3>
                            <div class="mt-2 text-xs text-amber-700 font-medium leading-relaxed">
                                @if($statusVerifikasi === 'pending')
                                    <p>Dokumen administrasi Anda sedang dalam proses pemeriksaan oleh tim pokja panitia. Modul absensi, tugas, dan sertifikat akan terbuka otomatis setelah disetujui.</p>
                                @else
                                    <p>Anda wajib mengunggah dokumen persyaratan resmi (Surat Tugas / SPPD) terlebih dahulu agar mendapatkan hak akses penuh pada fitur kelas bimbingan teknis ini.</p>
                                @endif
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                                    @if($statusVerifikasi === 'pending')
                                        Lihat Status Berkas
                                    @else
                                        Unggah Dokumen Sekarang
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tab Navigation (Alpine.js) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100" x-data="{ activeTab: 'materi' }">
                <div class="border-b border-gray-100 bg-gray-50/50">
                    <nav class="flex -mb-px overflow-x-auto">
                        <button @click="activeTab = 'materi'" 
                                :class="activeTab === 'materi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition">
                            Materi Kuliah
                        </button>
                        @if($bimtek->has_tugas)
                            <button @click="activeTab = 'tugas'" 
                                    :class="activeTab === 'tugas' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition">
                                Tugas Pengayaan
                            </button>
                        @endif
                        <button @click="activeTab = 'absensi'" 
                                :class="activeTab === 'absensi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition">
                            Lembar Presensi
                        </button>
                        @if($bimtek->has_sertifikat)
                            <button @click="activeTab = 'sertifikat'" 
                                    :class="activeTab === 'sertifikat' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition">
                                Unduh Sertifikat
                            </button>
                        @endif
                    </nav>
                </div>

                {{-- Tab Content - Materi --}}
                <div class="p-6" x-show="activeTab === 'materi'">
                    @if(!$isVerified && $isPeserta)
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 rounded-full mb-4 border border-dashed border-gray-200">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-1">Modul Pembelajaran Terkunci</h3>
                            <p class="text-xs text-gray-500 max-w-sm mx-auto leading-relaxed">Selesaikan rangkaian unggah dokumen persyaratan administrasi terlebih dahulu untuk membuka kunci materi bimbingan teknis.</p>
                        </div>
                    @else
                        @if($bimtek->materis->count() > 0)
                            <div class="space-y-2">
                                @foreach($bimtek->materis as $materi)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100/60 transition border border-gray-100">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $materi->judul }}</p>
                                                <p class="text-[10px] text-gray-400 font-medium mt-0.5">
                                                    {{ $materi->tipe === 'materi' ? 'Bahan Ajar Utama' : 'Panduan Praktis' }} • {{ $materi->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center shrink-0 ml-4">
                                            <a href="{{ route('bimtek.materi.download', [$bimtek->id, $materi->id]) }}" class="px-3 py-1 bg-white border border-gray-200 text-xs font-bold text-primary-600 rounded-lg hover:bg-gray-50 transition shadow-sm">Unduh</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <p class="text-sm text-gray-400 font-medium">Belum ada dokumen materi pembelajaran yang diunggah instruktur.</p>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Tab Content - Tugas --}}
                @if($bimtek->has_tugas)
                    <div class="p-6" x-show="activeTab === 'tugas'">
                        @if(!$isVerified && $isPeserta)
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 rounded-full mb-4 border border-dashed border-gray-200">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 mb-1">Akses Penyerahan Tugas Terkunci</h3>
                                <p class="text-xs text-gray-500 max-w-sm mx-auto leading-relaxed">Lembar pengerjaan tugas pengayaan akan aktif otomatis setelah status berkas terverifikasi.</p>
                            </div>
                        @else
                            @if($bimtek->tugas->count() > 0)
                                <div class="space-y-2">
                                    @foreach($bimtek->tugas as $tugas)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100/60 transition border border-gray-100">
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="text-sm font-bold text-gray-900 hover:text-primary-600 transition block truncate">{{ $tugas->judul }}</a>
                                                <p class="text-[10px] text-gray-400 font-medium mt-0.5">Batas Akhir: {{ $tugas->deadline ? $tugas->deadline->format('d M Y H:i') : 'Tanpa Batas Waktu' }} WIB</p>
                                            </div>
                                            <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="ml-4 shrink-0 px-3 py-1.5 bg-primary-600 text-white font-bold text-xs rounded-lg hover:bg-primary-700 transition shadow-sm">Buka Tugas</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <p class="text-sm text-gray-400 font-medium">Tidak ada lembar tugas belajar untuk saat ini.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- Tab Content - Absensi --}}
                <div class="p-6" x-show="activeTab === 'absensi'">
                    @if(!$isVerified && $isPeserta)
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 rounded-full mb-4 border border-dashed border-gray-200">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-1">Sesi Presensi Terkunci</h3>
                            <p class="text-xs text-gray-500 max-w-sm mx-auto leading-relaxed">Sistem scan QR code presensi kehadiran hanya dapat digunakan oleh peserta yang sudah terverifikasi lunas berkas.</p>
                        </div>
                    @else
                        @if($bimtek->sesiAbsensis->count() > 0)
                            <div class="space-y-2">
                                @foreach($bimtek->sesiAbsensis as $sesi)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-900 truncate">{{ $sesi->nama_sesi }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium mt-0.5">Dibuka pada: {{ $sesi->created_at->format('d M Y, H:i') }} WIB</p>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0 ml-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sesi->isOpen() ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                                {{ $sesi->isOpen() ? 'Terbuka' : 'Ditutup' }}
                                            </span>
                                            @if($sesi->isOpen())
                                                @if(in_array($bimtek->mode_pelaksanaan, ['offline', 'hybrid']))
                                                    <a href="{{ route('bimtek.absensi.scan-interface', [$bimtek->id, $sesi->id]) }}" class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs rounded-lg transition shadow-sm">Scan QR</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <p class="text-sm text-gray-400 font-medium">Belum ada sesi absensi kehadiran yang dibuka panitia hari ini.</p>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Tab Content - Sertifikat --}}
                @if($bimtek->has_sertifikat)
                    <div class="p-6" x-show="activeTab === 'sertifikat'">
                        @if(!$isVerified && $isPeserta)
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 rounded-full mb-4 border border-dashed border-gray-200">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 mb-1">Klaim Sertifikat Terkunci</h3>
                                <p class="text-xs text-gray-500 max-w-sm mx-auto leading-relaxed">Lembar sertifikat tanda lulus kelulusan hanya akan diterbitkan bagi peserta resmi yang tuntas verifikasi berkas dan syarat kehadiran.</p>
                            </div>
                        @else
                            @php
                                $sertifikatSaya = $bimtek->sertifikats->where('user_id', auth()->id())->first();
                            @endphp
                            @if($sertifikatSaya)
                                <div class="p-5 border border-green-200 bg-green-50/40 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div>
                                        <h4 class="text-sm font-bold text-green-900">Selamat! Sertifikat Kelulusan Anda Tersedia</h4>
                                        <p class="text-xs text-green-700 font-medium font-mono mt-1">No Seri: {{ $sertifikatSaya->nomor_sertifikat }}</p>
                                    </div>
                                    
                                    <div class="flex items-center gap-2.5 shrink-0">
                                        {{-- Tombol Pratinjau (Membuka PDF di Tab Baru) --}}
                                        <a href="{{ route('bimtek.sertifikat.preview', [$bimtek->id, $sertifikatSaya->user_id]) }}" 
                                        target="_blank" 
                                        rel="noopener" 
                                        class="px-4 py-2 bg-white border border-green-300 hover:bg-green-50 text-green-700 font-bold text-xs rounded-xl transition shadow-sm">
                                            Pratinjau
                                        </a>

                                        {{-- Tombol Unduh PDF --}}
                                        <a href="{{ route('bimtek.sertifikat.download', [$bimtek->id, $sertifikatSaya->user_id]) }}" 
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                                            Unduh PDF
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12 bg-gray-50/50 rounded-2xl border border-gray-100">
                                    <p class="text-sm text-gray-400 font-medium">Sertifikat Anda belum diterbitkan panitia pelaksana.</p>
                                    <p class="text-xs text-gray-400 mt-1">Pastikan persentase kehadiran Anda memenuhi batas minimum kelulusan.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>