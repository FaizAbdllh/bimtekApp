<x-app-layout>
    <x-slot name="header">
        Detail Ruang Kelas Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Kelas
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kiri & Tengah: Informasi Utama & Tab Konten --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Kartu Utama Informasi Bimtek --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                @php
                                    // REFAKTORISASI: Menyelaraskan peta status pelaksanaan dari kolom tunggal 'status'
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
                                    $canManage = ($bimtek->pic_user_id === auth()->id() || $bimtek->panitia()->where('user_id', auth()->id())->exists() || auth()->user()->isAdminIt());
                                    $isPic = $bimtek->pic_user_id === auth()->id();
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

                            {{-- Grid Informasi Detail Kolom --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm border-t border-gray-50 pt-4">
                                <div>
                                    <span class="text-gray-400 font-medium block">Mode Pelaksanaan:</span>
                                    {{-- REFAKTORISASI: Langsung membaca properti ke model induk terpadu --}}
                                    <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $modeBadge[$bimtek->mode_pelaksanaan] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $bimtek->mode_pelaksanaan_label }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Tautan Ruang Virtual:</span>
                                    @if($bimtek->virtual_meeting_url)
                                        <a href="{{ $bimtek->virtual_meeting_url }}" target="_blank" rel="noopener" class="text-primary-600 hover:text-primary-700 font-semibold block break-all mt-0.5">Buka Ruang Virtual (Zoom/Teams)</a>
                                    @else
                                        <span class="text-gray-400 block mt-0.5">{{ in_array($bimtek->mode_pelaksanaan, ['online', 'hybrid']) ? 'Belum ditentukan' : '-' }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Lokasi Aktual:</span>
                                    <span class="text-gray-800 font-bold block mt-0.5">{{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Periode Pelaksanaan:</span>
                                    <span class="text-gray-800 font-bold block mt-0.5">
                                        {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('d M Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('d M Y') : '-') }}
                                        @if($bimtek->tanggal_selesai_aktual)
                                            - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Alokasi Anggaran DIPA:</span>
                                    <span class="text-gray-800 font-bold block mt-0.5">Rp {{ number_format($bimtek->kebutuhanAnggarans->sum('total_biaya') ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Sumber Dana / Pagu:</span>
                                    <span class="text-gray-800 font-semibold block mt-0.5 truncate">{{ $bimtek->sumber_pembiayaan ?? '-' }}</span>
                                </div>
                            </div>

                            @if($bimtek->deskripsi_jadwal)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-gray-400 text-xs font-semibold uppercase tracking-wider block">Catatan / Deskripsi Jadwal Aktual:</span>
                                    <p class="text-gray-700 text-sm mt-1 leading-relaxed whitespace-pre-line">{{ $bimtek->deskripsi_jadwal }}</p>
                                </div>
                            @endif

                            {{-- Modul Surat Undangan Draft --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-800 text-xs font-bold uppercase tracking-wider">Berkas Surat Tugas/Undangan Draft</span>
                                    </div>
                                    @if($bimtek->file_surat_draft_path)
                                        <div class="flex items-center justify-between text-xs bg-white p-2 rounded-lg border border-gray-200">
                                            <span class="truncate text-gray-700 font-medium max-w-[150px]">{{ basename($bimtek->file_surat_draft_path) }}</span>
                                            <div class="flex gap-2">
                                                <a href="{{ route('bimtek.download-draft', $bimtek->id) }}" class="text-primary-600 font-bold hover:underline">Unduh</a>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-500 my-2">Belum ada berkas draft.</p>
                                    @endif
                                    @if($canManage && $bimtek->status == 'persiapan')
                                        <button type="button" @click="$dispatch('open-modal', 'upload-draft')" class="w-full mt-2 text-center text-xs py-1.5 bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition font-bold">
                                            {{ $bimtek->file_surat_draft_path ? 'Ganti Berkas' : 'Unggah Berkas Draft' }}
                                        </button>
                                    @endif
                                </div>

                                {{-- Modul Surat Undangan Final (Diambil alih PIC/Panitia/Admin) --}}
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-800 text-xs font-bold uppercase tracking-wider">Surat Undangan Final (Resmi TTD)</span>
                                    </div>
                                    @if($bimtek->file_surat_final_path)
                                        <div class="flex items-center justify-between text-xs bg-white p-2 rounded-lg border border-gray-200">
                                            <span class="truncate text-gray-700 font-medium max-w-[150px]">{{ basename($bimtek->file_surat_final_path) }}</span>
                                            <div class="flex gap-2">
                                                <a href="{{ route('bimtek.download-final', $bimtek->id) }}" class="text-green-600 font-bold hover:underline">Unduh</a>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-500 my-2">Belum ada surat final bertanda tangan resmi.</p>
                                    @endif
                                    @if($canManage && $bimtek->status == 'persiapan')
                                        <button type="button" @click="$dispatch('open-modal', 'upload-final')" class="w-full mt-2 text-center text-xs py-1.5 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition font-bold">
                                            Unggah Surat Tugas Final Resmi
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Tautan Registrasi Mandiri / Shared Link --}}
                            @if($canManage && $bimtek->mode_pelaksanaan !== 'internal')
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-gray-700 text-xs font-bold uppercase tracking-wider block mb-2">Tautan Pendaftaran Mandiri Peserta Luar:</span>
                                    @if($bimtek->invite_code)
                                        <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-xl border border-gray-200">
                                            <input id="invite-link" class="flex-1 bg-white border border-gray-200 text-xs p-2 rounded-lg text-gray-600 font-mono" readonly value="{{ route('register') . '?invite_code=' . $bimtek->invite_code }}">
                                            <button onclick="navigator.clipboard.writeText(document.getElementById('invite-link').value); alert('Link berhasil disalin!');" class="px-3 py-2 bg-gray-700 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition">Salin</button>
                                        </div>
                                    @else
                                        <form action="{{ route('bimtek.generate-invite', $bimtek->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold text-xs rounded-lg transition shadow-sm">
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
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-xl shadow-sm">
                            <div class="flex items-start">
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-bold text-amber-900">
                                        @if($statusVerifikasi === 'pending')
                                            Dokumen Persyaratan Menunggu Pemeriksaan Panitia
                                        @else
                                            Berkas Syarat Administrasi Belum Lengkap / Ditolak
                                        @endif
                                    </h3>
                                    <div class="mt-1 text-xs text-amber-700 font-medium">
                                        @if($statusVerifikasi === 'pending')
                                            <p>File unggahan Anda sedang diperiksa oleh tim pokja. Fitur absensi dan pengerjaan tugas akan otomatis terbuka setelah berkas dinyatakan sah.</p>
                                        @else
                                            <p>Anda wajib melampirkan berkas dokumen persyaratan (Surat Tugas / SPPD resmi) terlebih dahulu untuk dapat mengikuti rangkaian kegiatan ini.</p>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition">
                                            Buka Meja Unggah Berkas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Manajemen Transisi State Aksi PIC/Panitia --}}
                    @if($canManage)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Panel Kontrol Operasional Kegiatan</h3>
                            <div class="flex flex-wrap gap-3">
                                @if($bimtek->status === 'persiapan')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="berlangsung">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition" onclick="return confirm('Apakah Anda yakin ingin mengubah status kelas menjadi BERLANGSUNG?')">
                                            Mulai Kick-Off Kegiatan
                                        </button>
                                    </form>
                                    @if($isPic)
                                        <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="dibatalkan">
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg transition" onclick="return confirm('Apakah Anda yakin ingin MEMBATALKAN pelaksanaan bimtek ini?')">
                                                Batalkan Pelaksanaan
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if($bimtek->status === 'berlangsung')
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="persiapan">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold text-xs rounded-lg transition">
                                            Kembalikan ke Fase Persiapan
                                        </button>
                                    </form>
                                    <form action="{{ route('bimtek.update-status', $bimtek->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold text-xs rounded-lg transition" onclick="return confirm('Selesaikan kelas? Seluruh rekap matriks nilai akan dikunci untuk penerbitan sertifikat.')">
                                            Tutup & Selesaikan Kelas
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Sistem Navigasi Tab Konten Pengajaran (Alpine.js) --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100" x-data="{ tab: 'materi' }">
                        <div class="border-b border-gray-100 bg-gray-50/50">
                            <nav class="flex -mb-px overflow-x-auto">
                                <button @click="tab = 'materi'" :class="tab === 'materi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3.5 px-6 border-b-2 font-bold text-sm whitespace-nowrap transition">
                                    Silabus Materi
                                </button>
                                @if($bimtek->has_tugas)
                                    <button @click="tab = 'tugas'" :class="tab === 'tugas' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3.5 px-6 border-b-2 font-bold text-sm whitespace-nowrap transition">
                                        Lembar Tugas Belajar
                                    </button>
                                @endif
                                <button @click="tab = 'absensi'" :class="tab === 'absensi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3.5 px-6 border-b-2 font-bold text-sm whitespace-nowrap transition">
                                    Lembar Presensi
                                </button>
                                <button @click="tab = 'peserta_tab'" :class="tab === 'peserta_tab' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3.5 px-6 border-b-2 font-bold text-sm whitespace-nowrap transition">
                                    Anggota Kelas
                                </button>
                                @if($bimtek->has_sertifikat)
                                    <button @click="tab = 'sertifikat'" :class="tab === 'sertifikat' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3.5 px-6 border-b-2 font-bold text-sm whitespace-nowrap transition">
                                        Kelulusan Sertifikat
                                    </button>
                                @endif
                            </nav>
                        </div>

                        <div class="p-6">
                            {{-- Materi Tab Content --}}
                            <div x-show="tab === 'materi'" x-cloak>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $bimtek->materis->count() }} Bahan Ajar Terunggah</span>
                                    <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="text-primary-600 hover:underline text-xs font-bold">Buka Modul Materi →</a>
                                </div>
                                @if($bimtek->materis->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($bimtek->materis->take(5) as $materi)
                                            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50/50 transition">
                                                <div class="flex items-center min-w-0 mr-4">
                                                    <span class="text-sm font-medium text-gray-800 truncate">{{ $materi->judul }}</span>
                                                    <span class="ml-2 shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $materi->tipe === 'materi' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                                        {{ $materi->tipe }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3 text-xs font-bold shrink-0">
                                                    <a href="{{ route('bimtek.materi.download', [$bimtek->id, $materi->id]) }}" class="text-primary-600 hover:text-primary-800">Unduh</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-center text-sm text-gray-400 py-6 font-medium">Belum ada unggahan berkas bahan ajar silabus.</p>
                                @endif
                            </div>

                            {{-- Tugas Tab Content --}}
                            @if($bimtek->has_tugas)
                                <div x-show="tab === 'tugas'" x-cloak>
                                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                        <p class="text-center text-sm text-gray-400 py-6 font-medium">Akses tugas dikunci. Selesaikan verifikasi berkas administrasi Anda terlebih dahulu.</p>
                                    @else
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $bimtek->tugas->count() }} Tugas Pengayaan</span>
                                            <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="text-primary-600 hover:underline text-xs font-bold">Buka Modul Tugas →</a>
                                        </div>
                                        @if($bimtek->tugas->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($bimtek->tugas->take(5) as $tugas)
                                                    <div class="p-3 border border-gray-100 rounded-xl hover:bg-gray-50/50 transition flex justify-between items-center">
                                                        <div>
                                                            <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="text-sm font-bold text-gray-800 hover:text-primary-600 transition block">{{ $tugas->judul }}</a>
                                                            <span class="text-[10px] font-medium text-gray-400 block mt-0.5">Batas Pengumpulan: {{ $tugas->deadline?->format('d M Y, H:i') }} WIB</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-center text-sm text-gray-400 py-6 font-medium">Belum ada tugas pengayaan yang diterbitkan oleh instruktur.</p>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            {{-- Absensi Tab Content --}}
                            <div x-show="tab === 'absensi'" x-cloak>
                                @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                    <p class="text-center text-sm text-gray-400 py-6 font-medium">Akses lembar presensi dikunci hingga berkas administrasi Anda disetujui panitia.</p>
                                @else
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $bimtek->sesiAbsensis->count() }} Sesi Presensi Terjadwal</span>
                                        <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="text-primary-600 hover:underline text-xs font-bold">Kelola Sesi Presensi →</a>
                                    </div>
                                    @if($bimtek->sesiAbsensis->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($bimtek->sesiAbsensis->take(5) as $sesi)
                                                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl bg-white shadow-sm">
                                                    <div>
                                                        <h4 class="text-sm font-bold text-gray-800">{{ $sesi->nama_sesi }}</h4>
                                                        <span class="text-[10px] font-medium text-gray-400 block mt-0.5">Dibuat: {{ $sesi->created_at->format('d M Y, H:i') }} WIB</span>
                                                    </div>
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sesi->isOpen() ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ $sesi->isOpen() ? 'Terbuka' : 'Ditutup' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-center text-sm text-gray-400 py-6 font-medium">Belum ada lembar sesi absensi hari ini.</p>
                                    @endif
                                review@endif
                            </div>

                            {{-- Anggota Kelas Tab Content --}}
                            <div x-show="tab === 'peserta_tab'" x-cloak>
                                @php
                                    // REFAKTORISASI RADIKAL: Menghitung total aktor secara independen dari relasi terpisah baru
                                    $countPic = $bimtek->pic_user_id ? 1 : 0;
                                    $countPanitia = $bimtek->panitia->count();
                                    $countPeserta = $bimtek->peserta->count();
                                    $totalAktor = $countPic + $countPanitia + $countPeserta;
                                @endphp
                                <div class="grid grid-cols-3 gap-3 mb-5">
                                    <div class="bg-red-50/60 border border-red-100 p-3 rounded-xl text-center">
                                        <div class="text-xl font-bold text-red-700">{{ $countPic }}</div>
                                        <div class="text-[10px] font-bold text-red-500 uppercase tracking-wider mt-0.5">PIC Utama</div>
                                    </div>
                                    <div class="bg-purple-50/60 border border-purple-100 p-3 rounded-xl text-center">
                                        <div class="text-xl font-bold text-purple-700">{{ $countPanitia }}</div>
                                        <div class="text-[10px] font-bold text-purple-500 uppercase tracking-wider mt-0.5">Tim Pokja</div>
                                    </div>
                                    <div class="bg-blue-50/60 border border-blue-100 p-3 rounded-xl text-center">
                                        <div class="text-xl font-bold text-blue-700">{{ $countPeserta }}</div>
                                        <div class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mt-0.5">Peserta Kelas</div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    {{-- List Penanggung Jawab PIC --}}
                                    @if($bimtek->pic)
                                        <div class="flex items-center justify-between p-3 border border-red-100 bg-red-55/10 rounded-xl">
                                            <div class="flex items-center min-w-0">
                                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center font-bold text-red-700 text-xs flex-shrink-0">PIC</div>
                                                <div class="ml-3 min-w-0"><span class="text-sm font-bold text-gray-900 block truncate">{{ $bimtek->pic->name }}</span></div>
                                            </div>
                                            <span class="px-2.5 py-0.5 bg-red-100 text-red-800 text-[10px] font-bold uppercase rounded-full">Penanggung Jawab</span>
                                        </div>
                                    @endif

                                    {{-- List Panitia Pokja --}}
                                    @foreach($bimtek->panitia->take(3) as $p)
                                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl">
                                            <div class="flex items-center min-w-0">
                                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center font-bold text-purple-700 text-xs flex-shrink-0">PT</div>
                                                <div class="ml-3 min-w-0"><span class="text-sm font-medium text-gray-800 block truncate">{{ $p->name }}</span></div>
                                            </div>
                                            <span class="px-2.5 py-0.5 bg-purple-50 text-purple-700 text-[10px] font-bold rounded-full">{{ $p->pivot->fungsi_panitia ?? 'Panitia Pelaksana' }}</span>
                                        </div>
                                    @endforeach

                                    {{-- List Contoh Peserta --}}
                                    @foreach($bimtek->peserta->take(3) as $pe)
                                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl">
                                            <div class="flex items-center min-w-0">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700 text-xs flex-shrink-0">PS</div>
                                                <div class="ml-3 min-w-0"><span class="text-sm font-medium text-gray-800 block truncate">{{ $pe->name }}</span></div>
                                            </div>
                                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full">Peserta Kelas</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Sertifikat Tab Content --}}
                            @if($bimtek->has_sertifikat)
                                <div x-show="tab === 'sertifikat'" x-cloak>
                                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                        <p class="text-center text-sm text-gray-400 py-6 font-medium">Modul sertifikat kelulusan terkunci hingga seluruh berkas administrasi diverifikasi oleh panitia.</p>
                                    @else
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sertifikat Kelulusan Resmi</span>
                                            <a href="{{ route('bimtek.sertifikat.index', $bimtek->id) }}" class="text-primary-600 hover:underline text-xs font-bold">Kelola Kelulusan →</a>
                                        </div>
                                        @if($bimtek->sertifikats->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($bimtek->sertifikats->take(5) as $cert)
                                                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl bg-white shadow-sm">
                                                        <div>
                                                            <h4 class="text-sm font-bold text-gray-800">{{ $cert->user->name ?? '-' }}</h4>
                                                            <span class="text-[10px] font-mono text-gray-400 block mt-0.5">No: {{ $cert->nomor_sertifikat }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-center text-sm text-gray-400 py-6 font-medium">Belum ada lembar sertifikat kelulusan yang diterbitkan.</p>
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
                        <div class="bg-gradient-to-br from-primary-600 to-primary-700 overflow-hidden shadow-sm sm:rounded-2xl p-5 text-white">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-primary-100 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="font-bold text-base">Verifikasi Dokumen Kelulusan</h3>
                            </div>
                            <p class="text-xs text-primary-100 mb-4 leading-relaxed">Periksa kesahihan Surat Tugas/SPPD yang dikirim oleh peserta luar instansi.</p>
                            @php
                                // REFAKTORISASI: Menghitung berkas pending langsung dari tabel jembatan baru
                                $pendingCount = Illuminate\Support\Facades\DB::table('bimtek_pesertas')->where('bimtek_id', $bimtek->id)->where('status_verifikasi', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <div class="mb-4 p-3 bg-white/10 border border-white/20 rounded-xl text-xs font-semibold">
                                    Ada {{ $pendingCount }} berkas peserta mengantre untuk ditinjau.
                                </div>
                            @endif
                            <a href="{{ route('bimtek.verifikasi-dokumen.index', $bimtek->id) }}" class="block w-full text-center py-2.5 bg-white text-primary-700 font-bold text-xs rounded-xl hover:bg-primary-50 transition shadow-sm">
                                Masuk Meja Pemeriksa
                            </a>
                        </div>
                    @endif

                    {{-- Daftar Visual Anggota Panitia Pokja --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-sm text-gray-900">Susunan Tim Kerja Panitia</h3>
                            @if($isPic && $bimtek->status === 'persiapan')
                                <button x-data @click="$dispatch('open-modal', 'assign-panitia')" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition">+ Tambah</button>
                            @endif
                        </div>
                        <div class="p-4 space-y-3">
                            @if($bimtek->panitia->count() > 0)
                                @foreach($bimtek->panitia as $panitiaUser)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center min-w-0">
                                            <div class="w-7 h-7 rounded-full bg-purple-50 flex items-center justify-center font-bold text-purple-700 text-xs flex-shrink-0">
                                                {{ strtoupper(substr($panitiaUser->name, 0, 2)) }}
                                            </div>
                                            <div class="ml-2.5 min-w-0">
                                                <span class="text-sm font-semibold text-gray-800 block truncate">{{ $panitiaUser->name }}</span>
                                                <span class="text-[10px] text-gray-400 font-medium block">{{ $panitiaUser->pivot->fungsi_panitia ?? 'Anggota Pelaksana' }}</span>
                                            </div>
                                        </div>
                                        @if($isPic && $bimtek->status === 'persiapan')
                                            <form action="{{ route('bimtek.remove-panitia', [$bimtek->id, $panitiaUser->id]) }}" method="POST" onsubmit="return confirm('Cabut kewenangan kepanitiaan pegawai ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[10px] font-bold text-red-500 hover:text-red-700 transition">Copot</button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-center text-xs text-gray-400 py-3 font-medium">Belum ada tim panitia pelaksana yang didaftarkan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Daftar Ringkas Peserta Terdaftar --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-sm text-gray-900">Peserta Terdaftar ({{ $bimtek->peserta->count() }})</h3>
                            @if($canManage)
                                <a href="{{ route('bimtek.peserta.index', $bimtek->id) }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition">Kelola</a>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($bimtek->peserta->count() > 0)
                                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                                    @foreach($bimtek->peserta->take(8) as $pesertaUser)
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-700 text-xs flex-shrink-0">
                                                {{ strtoupper(substr($pesertaUser->name, 0, 2)) }}
                                            </div>
                                            <span class="ml-2.5 text-sm font-semibold text-gray-700 truncate">{{ $pesertaUser->name }}</span>
                                        </div>
                                    @endforeach
                                    @if($bimtek->peserta->count() > 8)
                                        <p class="text-[10px] text-gray-400 font-bold text-center pt-2 border-t border-gray-50">+ {{ $bimtek->peserta->count() - 8 }} Aktor Peserta Lainnya</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-center text-xs text-gray-400 py-4 font-medium">Belum ada daftar nama peserta.</p>
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
            <h3 class="text-base font-bold text-gray-900 mb-4">Tambahkan Jajaran Panitia Pokja</h3>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Pilih Pegawai Internal:</label>
                <select name="user_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 bg-white" required>
                    <option value="">-- Pilih Anggota Pegawai --</option>
                    @foreach($availableUsers as $avUser)
                        <option value="{{ $avUser->id }}">{{ $avUser->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Fungsi Struktur Jabatan:</label>
                <select name="fungsi_panitia" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2 bg-white" required>
                    <option value="">-- Pilih Penugasan Kerja --</option>
                    <option value="Ketua Pelaksana">Ketua Pelaksana</option>
                    <option value="Sekretaris Pokja">Sekretaris Pokja</option>
                    <option value="Bendahara Pengeluaran">Bendahara Pengeluaran</option>
                    <option value="Anggota Tim Administrasi">Anggota Tim Administrasi</option>
                    <option value="Anggota Tim Teknis Lapangan">Anggota Tim Teknis Lapangan</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-50 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-semibold text-xs rounded-xl hover:bg-primary-700 transition shadow-sm">Suntik Data</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Unggah Surat Draft --}}
    <x-modal name="upload-draft" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.upload-draft', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-base font-bold text-gray-900 mb-2">Unggah Berkas Surat Undangan Draft</h3>
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">File draft format PDF ini digunakan sebagai acuan dasar penerbitan tata nomor surat keluar resmi.</p>
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Pilih Berkas Lampiran (PDF Max 2MB):</label>
                <input type="file" name="surat_draft" accept=".pdf" class="w-full rounded-xl border-gray-300 shadow-sm text-sm" required>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-50 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold text-xs rounded-xl hover:bg-blue-700 transition shadow-sm">Mulai Unggah</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Unggah Surat Final --}}
    <x-modal name="upload-final" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.upload-final', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-base font-bold text-gray-900 mb-2">Unggah Berkas Surat Undangan Final TTD</h3>
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">Pastikan file PDF yang diunggah telah memuat lembar tanda tangan sah Kepala Balai beserta stempel resmi instansi.</p>
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Pilih Berkas Hasil TTD (PDF Max 2MB):</label>
                <input type="file" name="surat_final" accept=".pdf" class="w-full rounded-xl border-gray-300 shadow-sm text-sm" required>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-50 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-semibold text-xs rounded-xl hover:bg-green-700 transition shadow-sm">Sahkan Dokumen</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>