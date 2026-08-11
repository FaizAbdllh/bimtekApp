<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    {{-- REFAKTORISASI: Kestabilan ID UUID dan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li>/</li>
                    <li><a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">Tugas</a></li>
                    <li>/</li>
                    <li class="text-gray-800 font-bold truncate max-w-[200px]">{{ Str::limit($tugas->judul, 20) }}</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Detail Penugasan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Aksi Atasan --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="text-sm font-semibold text-gray-700">Kelas: <span class="text-gray-900 font-bold">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>
                </div>
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide">
                    {{-- REFAKTORISASI: Kestabilan ID rute kembali --}}
                    <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Daftar Tugas
                    </a>
                    @if($canManage)
                        {{-- REFAKTORISASI: Kestabilan ID rute ubah/edit tugas --}}
                        <a href="{{ route('bimtek.tugas.edit', [$bimtek->id, $tugas->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-amber-600 rounded-xl hover:bg-amber-50 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Koreksi Aturan
                        </a>
                    @endif
                </div>
            </div>

            {{-- Pembagian Grid Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                {{-- SISI KIRI-TENGAH: Konten Detail & Formulir Interaksi (2/3 Width) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Detail Deskripsi Tugas --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h2 class="text-xl font-bold text-gray-900 leading-snug">{{ $tugas->judul }}</h2>

                        {{-- Panel Banner Penunjuk Sisa Waktu (Deadline) --}}
                        <div class="p-4 rounded-xl border {{ $tugas->isDeadlinePassed() ? 'bg-red-50/60 border-red-100 text-red-800' : 'bg-blue-50/60 border-blue-100 text-blue-800' }}">
                            <div class="flex items-center gap-3 text-sm font-semibold">
                                <svg class="w-5 h-5 shrink-0 {{ $tugas->isDeadlinePassed() ? 'text-red-500' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p>Batas Pengumpulan: {{ $tugas->deadline->format('d M Y, H:i') }} WIB</p>
                                    <p class="text-xs font-medium mt-0.5 opacity-80">
                                        {{ $tugas->isDeadlinePassed() ? 'Waktu pengumpulan telah ditutup ' . $tugas->deadline->diffForHumans() : 'Sisa waktu pengerjaan: ' . $tugas->deadline->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi Tekstual Instruksi --}}
                        @if($tugas->deskripsi)
                            <div class="pt-2">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Instruksi Kerja Lembar Penugasan</h3>
                                <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">{{ $tugas->deskripsi }}</p>
                            </div>
                        @endif

                        {{-- Lampiran Berkas Panduan Lembar Kerja dari Instruktur --}}
                        @if($tugas->file_instruksi_path)
                            <div class="pt-4 border-t border-gray-100">
                                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumen Acuan Pendukung</h3>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200/60 shadow-inner shadow-gray-50/30">
                                    
                                    {{-- Sisi Kiri: Ikon & Nama File dengan Proteksi Truncate --}}
                                    <div class="flex items-center min-w-0 gap-2.5 flex-1 mr-4">
                                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{-- Mengunci nama file panjang agar otomatis terpotong titik-titik (...) secara aman --}}
                                        <span class="text-xs font-semibold text-gray-700 truncate max-w-[200px] sm:max-w-xs md:max-w-md block" title="{{ basename($tugas->file_instruksi_path) }}">
                                            {{ basename($tugas->file_instruksi_path) }}
                                        </span>
                                    </div>
                                    
                                    {{-- Sisi Kanan: Opsi Akses Berkas Berdampingan --}}
                                    <div class="flex items-center gap-3 font-bold text-xs shrink-0 pl-2">
                                        {{-- Fitur Pratinjau (Membuka PDF langsung di tab baru peramban) --}}
                                        <a href="{{ route('bimtek.tugas.preview-instruksi', [$bimtek->id, $tugas->id]) }}" target="_blank" rel="noopener" class="text-primary-600 hover:text-primary-800 transition-colors">
                                            Lihat
                                        </a>
                                        <span class="text-gray-300 font-light text-[10px]">|</span>
                                        {{-- Fitur Unduh Otomatis --}}
                                        <a href="{{ route('bimtek.tugas.download-instruksi', [$bimtek->id, $tugas->id]) }}" class="text-green-600 hover:text-green-800 transition-colors">
                                            Unduh
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- SUB-SECTION REGISTRASI EVALUASI (Aktor Operator / Instruktur Pokja Only) --}}
                    @if($canManage)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Lembar Kendali Pengumpulan Jawaban</h3>
                                <span class="px-2 py-0.5 bg-gray-100 rounded text-[10px] font-bold text-gray-500 uppercase">
                                    {{ $tugas->pengumpulanTugas->count() }} / {{ $peserta->count() }} Masuk
                                </span>
                            </div>

                            @if($tugas->pengumpulanTugas->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                                            <tr>
                                                <th class="px-6 py-3 text-left pl-6">Nama Lengkap Anggota</th>
                                                <th class="px-4 py-3 text-center">Waktu Kirim</th>
                                                <th class="px-4 py-3 text-center">Dokumen Hasil</th>
                                                <th class="px-4 py-3 text-center">Skor Evaluasi</th>
                                                <th class="px-6 py-3 text-right pr-6 w-32">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                            @foreach($tugas->pengumpulanTugas as $pengumpulan)
                                                <tr class="hover:bg-gray-50/50 transition-colors">
                                                    <td class="px-6 py-4 pl-6 whitespace-nowrap">
                                                        <div class="flex items-center gap-3">
                                                            <div class="h-8 w-8 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                                {{ strtoupper(substr($pengumpulan->user->name ?? 'U', 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <span class="text-sm font-bold text-gray-900 block">{{ $pengumpulan->user->name ?? '-' }}</span>
                                                                <span class="text-[10px] text-gray-400 font-medium block mt-0.5">{{ $pengumpulan->user->email ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                                        <span class="text-xs font-semibold text-gray-800 block">{{ $pengumpulan->created_at->format('d M Y') }}</span>
                                                        <span class="text-[10px] text-gray-400 font-medium mt-0.5 block">{{ $pengumpulan->created_at->format('H:i') }} WIB</span>
                                                        @if($pengumpulan->created_at->gt($tugas->deadline))
                                                            <span class="inline-flex mt-1 px-2 py-0.5 bg-red-50 border border-red-100 text-red-700 font-bold rounded text-[9px] uppercase tracking-wide">Terlambat</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap font-bold text-xs">
                                                        <div class="flex items-center justify-center gap-2">
                                                            {{-- REFAKTORISASI: Kestabilan ID parameter rute pratinjau dan unduh jawaban --}}
                                                            <a href="{{ route('bimtek.tugas.preview-jawaban', [$bimtek->id, $tugas->id, $pengumpulan->user_id]) }}" target="_blank" rel="noopener" class="text-purple-600 hover:text-purple-800 hover:underline">Lihat</a>
                                                            <span class="text-gray-200">|</span>
                                                            <a href="{{ route('bimtek.tugas.download-jawaban', [$bimtek->id, $tugas->id, $pengumpulan->user_id]) }}" class="text-primary-600 hover:text-primary-800 hover:underline">Unduh</a>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                                        @if($pengumpulan->nilai !== null)
                                                            <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-bold bg-green-50 border border-green-100 text-green-700">
                                                                {{ $pengumpulan->nilai }} / 100
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-gray-400 font-medium italic">Belum Dinilai</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-right pr-6 whitespace-nowrap align-middle">
                                                        <button type="button"
                                                                onclick="openGradeModal('{{ $pengumpulan->user_id }}', '{{ addslashes($pengumpulan->user->name ?? '-') }}', '{{ $pengumpulan->nilai ?? '' }}', '{{ addslashes($pengumpulan->feedback ?? '') }}')"
                                                                class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold text-xs uppercase tracking-wide transition shadow-sm shadow-primary-50">
                                                            {{ $pengumpulan->nilai !== null ? 'Koreksi' : 'Beri Nilai' }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Daftar Peserta yang Belum Mengumpulkan Berkas --}}
                                @php
                                    $submittedUserIds = $tugas->pengumpulanTugas->pluck('user_id')->toArray();
                                    $belumMengumpulkan = $peserta->whereNotIn('id', $submittedUserIds);
                                @endphp
                                @if($belumMengumpulkan->count() > 0)
                                    <div class="p-6 bg-gray-50/50 border-t border-gray-100 space-y-2.5">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Belum Mengirimkan Jawaban ({{ $belumMengumpulkan->count() }} orang)</h4>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($belumMengumpulkan as $p)
                                                <span class="px-2.5 py-1 bg-white border border-gray-200 text-gray-600 font-semibold text-xs rounded-lg shadow-sm">
                                                    {{ $p->name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-12 text-gray-400 font-medium">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-xs text-gray-400">Belum ada lembar file tanggapan jawaban tugas yang diunggah oleh peserta kelas.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- SUB-SECTION INTERAKSI UNGGHA JAWABAN (Aktor Anggota / Peserta Kelas Only) --}}
                    @if($isPeserta)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Pengumpulan Jawaban Anda</h3>
                            </div>
                            <div class="p-6">
                                @if($userSubmission)
                                    {{-- Keadaan Jika Peserta Sudah Berhasil Mengumpulkan Tugas --}}
                                    <div class="space-y-4">
                                        <div class="bg-green-50/60 border border-green-100 rounded-xl p-4 flex items-center gap-3">
                                            <div class="w-9 h-9 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-bold text-base shadow-sm">✓</div>
                                            <div class="text-sm">
                                                <p class="font-bold text-green-900">Berkas Jawaban Berhasil Dikirim</p>
                                                <p class="text-xs text-green-600/90 font-medium mt-0.5">Diserahkan pada {{ $userSubmission->created_at->format('d F Y, H:i') }} WIB</p>
                                            </div>
                                        </div>

                                        {{-- Informasi Berkas File Unggahan Jawaban --}}
                                        <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100 shadow-inner">
                                            <div class="flex items-center min-w-0 mr-4">
                                                <svg class="w-8 h-8 text-gray-400 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-xs font-bold text-gray-700 truncate block">{{ basename($userSubmission->file_jawaban_path) }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 font-bold text-xs shrink-0">
                                                {{-- REFAKTORISASI: Kestabilan ID parameter rute unduh & pratinjau --}}
                                                <a href="{{ route('bimtek.tugas.preview-jawaban', [$bimtek->id, $tugas->id, $userSubmission->user_id]) }}" target="_blank" rel="noopener" class="text-purple-600 hover:text-purple-800">Lihat</a>
                                                <a href="{{ route('bimtek.tugas.download-jawaban', [$bimtek->id, $tugas->id, $userSubmission->user_id]) }}" class="text-primary-600 hover:text-primary-800">Unduh</a>
                                            </div>
                                        </div>

                                        {{-- Panel Hasil Pemeriksaan Skor Nilai Instruktur --}}
                                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl space-y-2">
                                            <div class="flex items-center justify-between border-b border-gray-200/60 pb-2">
                                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Perolehan Nilai Akhir:</span>
                                                @if($userSubmission->nilai !== null)
                                                    <span class="text-2xl font-black {{ $userSubmission->nilai >= 70 ? 'text-green-600' : 'text-red-500' }}">{{ $userSubmission->nilai }} <span class="text-xs font-bold text-gray-400">/ 100</span></span>
                                                @else
                                                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-100 uppercase tracking-wide">Menunggu Evaluasi</span>
                                                @endif
                                            </div>
                                            @if($userSubmission->nilai !== null && $userSubmission->feedback)
                                                <div class="text-xs">
                                                    <p class="font-bold text-gray-500 uppercase tracking-wide mb-1">Catatan Korektif Guru / Instruktur:</p>
                                                    <p class="text-gray-600 font-medium bg-white p-2.5 rounded-lg border border-gray-100 leading-relaxed">"{{ $userSubmission->feedback }}"</p>
                                                </div>
                                            @endif
                                            @if($userSubmission->nilai !== null && $userSubmission->penilai)
                                                <p class="text-[10px] text-gray-400 font-semibold text-right">Divalidasi Oleh: {{ $userSubmission->penilai->name ?? '-' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- Kondisi Jika Peserta Belum Mengirimkan Berkas Dokumen Tugas --}}
                                    @if($bimtek->status !== 'berlangsung')
                                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-500 font-medium text-center">
                                            Gerbang pengumpulan berkas terkunci karena status kelas sedang tidak aktif.
                                        </div>
                                    @elseif($tugas->isDeadlinePassed())
                                        <div class="bg-red-50/60 border border-red-100 rounded-xl p-4 text-xs font-semibold text-red-800 flex items-center gap-2">
                                            <span>✗ Waktu pengumpulan telah berakhir. Anda tidak dapat melampirkan lembar jawaban karena batas tenggat terlewati.</span>
                                        </div>
                                    @else
                                        {{-- REFAKTORISASI FORM SUBMIT: Kestabilan ID parameter rute pengumpulan --}}
                                        <form action="{{ route('bimtek.tugas.submit', [$bimtek->id, $tugas->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                            @csrf
                                            <div>
                                                <label for="file_jawaban" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                                    Pilih File Lembar Jawaban Kerja <span class="text-red-500">*</span>
                                                </label>
                                                <input type="file" 
                                                       name="file_jawaban" 
                                                       id="file_jawaban"
                                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                       class="w-full border border-gray-300 rounded-xl bg-gray-50 text-xs font-semibold text-gray-500 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 p-2"
                                                       required>
                                                @error('file_jawaban')
                                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                                @enderror
                                                <p class="mt-1.5 text-[10px] text-gray-400 font-medium">Format dokumen yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (Ukuran Maks. 20MB)</p>
                                            </div>

                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                </svg>
                                                Kumpulkan Lembar Jawaban
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- SISI KANAN: Panel Widget Statistik & Ringkasan Progress (1/3 Width) --}}
                <div class="space-y-6 text-sm">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">Statistik Pengumpulan Sesi</h3>
                        
                        <div class="space-y-3.5 font-semibold text-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 uppercase tracking-wide">Total Peserta Kelas</span>
                                <span class="text-gray-900 font-bold">{{ $peserta->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 uppercase tracking-wide">Sudah Mengirim</span>
                                <span class="text-green-600 font-bold">{{ $tugas->pengumpulanTugas->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 uppercase tracking-wide">Belum Mengirim</span>
                                <span class="text-red-500 font-bold">{{ max(0, $peserta->count() - $tugas->pengumpulanTugas->count()) }}</span>
                            </div>

                            {{-- Progress Grafik Batang --}}
                            @php
                                $progress = $peserta->count() > 0 ? ($tugas->pengumpulanTugas->count() / $peserta->count()) * 100 : 0;
                            @endphp
                            <div class="pt-2">
                                <div class="flex items-center justify-between text-xs mb-1 font-bold">
                                    <span class="text-gray-400 uppercase tracking-wide">Rasio Rampung</span>
                                    <span class="text-gray-900">{{ number_format($progress, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden shadow-inner">
                                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- Akumulasi Nilai Rata-rata Kelas (Untuk Panitia) --}}
                            @if($canManage && $tugas->pengumpulanTugas->count() > 0)
                                @php
                                    $dinilai = $tugas->pengumpulanTugas->whereNotNull('nilai')->count();
                                    $avgNilai = $tugas->pengumpulanTugas->whereNotNull('nilai')->avg('nilai');
                                @endphp
                                <div class="pt-4 border-t border-gray-100 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-400 uppercase tracking-wide">Sudah Diperiksa</span>
                                        <span class="text-gray-900 font-bold">{{ $dinilai }} / {{ $tugas->pengumpulanTugas->count() }}</span>
                                    </div>
                                    @if($avgNilai)
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-400 uppercase tracking-wide">Rata-rata Nilai</span>
                                            <span class="text-primary-600 font-black text-base">{{ number_format($avgNilai, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODUL MODAL PENILAIAN MANUAL --}}
    @if($canManage)
        <div id="grade-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:p-0">
                <div id="grade-modal-overlay" class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"></div>

                <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-2xl text-left border border-gray-100 overflow-hidden transform transition-all my-8 align-middle">
                    <form id="grade-form" method="POST" data-action-base="{{ url('bimtek/' . $bimtek->id . '/tugas/' . $tugas->id . '/pengumpulan') }}">
                        @csrf
                        <div class="p-6 space-y-4">
                            <div class="border-b border-gray-50 pb-3">
                                <h3 class="text-base font-bold text-gray-900">Evaluasi & Beri Nilai Kelulusan</h3>
                                <p class="text-xs text-gray-400 font-semibold mt-0.5">Nama Anggota: <span id="grade-peserta-nama" class="text-primary-600 font-bold"></span></p>
                            </div>

                            <div>
                                <label for="grade-nilai" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Skor Nilai Akhir Kuantitatif (0 - 100) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai" id="grade-nilai" min="0" max="100"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-bold"
                                       required>
                            </div>

                            <div>
                                <label for="grade-feedback" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Catatan Masukan Korektif (Opsional)
                                </label>
                                <textarea name="feedback" id="grade-feedback" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-medium"
                                          placeholder="Tambahkan catatan rekomendasi perbaikan berkas jika diperlukan peserta..."></textarea>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t border-gray-100 font-bold text-xs uppercase tracking-wide shadow-inner shadow-gray-50">
                            <button type="button" id="grade-modal-close" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
                                Sahkan Nilai Tugas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Script Global Langsung --}}
        <script>
            function openGradeModal(userId, nama, nilai, feedback) {
                const modal = document.getElementById('grade-modal');
                if (!modal) return;

                const nameEl = document.getElementById('grade-peserta-nama');
                const nilaiInput = document.getElementById('grade-nilai');
                const feedbackInput = document.getElementById('grade-feedback');
                const form = document.getElementById('grade-form');
                const actionBase = form.getAttribute('data-action-base');

                nameEl.textContent = nama;
                nilaiInput.value = nilai || '';
                feedbackInput.value = feedback || '';
                form.action = `${actionBase}/${userId}/grade`;

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-y-hidden');
            }

            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('grade-modal');
                if (!modal) return;

                const overlay = document.getElementById('grade-modal-overlay');
                const closeBtn = document.getElementById('grade-modal-close');

                function closeModal() {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-y-hidden');
                }

                if (overlay) overlay.addEventListener('click', closeModal);
                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });
            });
        </script>
    @endif
</x-app-layout>