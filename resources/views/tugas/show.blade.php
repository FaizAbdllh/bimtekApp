<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium text-gray-500">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    {{-- REFAKTORISASI: Kestabilan ID UUID dan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li class="text-gray-400">/</li>
                    <li><a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">Tugas</a></li>
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-700 truncate max-w-[200px]">{{ Str::limit($tugas->judul, 20) }}</li>
                </ol>
            </nav>
            <h2 class="text-xl font-bold text-gray-800">
                Detail Penugasan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Aksi Atasan --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="text-sm text-gray-500">Kelas: <span class="font-medium text-gray-900">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- REFAKTORISASI: Kestabilan ID rute kembali --}}
                    <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Daftar Tugas
                    </a>
                    @if($canManage)
                        {{-- REFAKTORISASI: Kestabilan ID rute ubah/edit tugas --}}
                        <a href="{{ route('bimtek.tugas.edit', [$bimtek->id, $tugas->id]) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 space-y-4">
                        <h2 class="text-lg font-bold text-gray-800 leading-snug">{{ $tugas->judul }}</h2>

                        {{-- Panel Banner Penunjuk Sisa Waktu (Deadline) --}}
                        <div class="p-3 rounded-lg border text-sm {{ $tugas->isDeadlinePassed() ? 'bg-red-50 border-red-100 text-red-800' : 'bg-primary-50 border-primary-100 text-primary-800' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold">Batas Pengumpulan: {{ $tugas->deadline->format('d M Y, H:i') }} WIB</p>
                                    <p class="text-sm mt-0.5">
                                        {{ $tugas->isDeadlinePassed() ? 'Waktu pengumpulan telah ditutup ' . $tugas->deadline->diffForHumans() : 'Sisa waktu pengerjaan: ' . $tugas->deadline->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi Tekstual Instruksi --}}
                        @if($tugas->deskripsi)
                            <div class="pt-2">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Instruksi Kerja Lembar Penugasan</h3>
                                <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $tugas->deskripsi }}</p>
                            </div>
                        @endif

                        {{-- Lampiran Berkas Panduan Lembar Kerja dari Instruktur --}}
                        @if($tugas->file_instruksi_path)
                            <div class="pt-4 border-t border-gray-200">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Dokumen Acuan Pendukung</h3>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    
                                    {{-- Sisi Kiri: Ikon & Nama File dengan Proteksi Truncate --}}
                                    <div class="flex items-center min-w-0 gap-3 flex-1 mr-4">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-gray-600 bg-gray-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        {{-- Mengunci nama file panjang agar otomatis terpotong titik-titik (...) secara aman --}}
                                        <span class="text-sm font-medium text-gray-900 truncate max-w-[200px] sm:max-w-xs md:max-w-md block" title="{{ basename($tugas->file_instruksi_path) }}">
                                            {{ basename($tugas->file_instruksi_path) }}
                                        </span>
                                    </div>
                                    
                                    {{-- Sisi Kanan: Opsi Akses Berkas Berdampingan --}}
                                    <div class="flex items-center gap-3 shrink-0 pl-2">
                                        {{-- Fitur Pratinjau (Membuka PDF langsung di tab baru peramban) --}}
                                        <a href="{{ route('bimtek.tugas.preview-instruksi', [$bimtek->id, $tugas->id]) }}" target="_blank" rel="noopener" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                                            Lihat
                                        </a>
                                        <span class="text-gray-400">|</span>
                                        {{-- Fitur Unduh Otomatis --}}
                                        <a href="{{ route('bimtek.tugas.download-instruksi', [$bimtek->id, $tugas->id]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                                            Unduh
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- SUB-SECTION REGISTRASI EVALUASI (Aktor Operator / Instruktur Pokja Only) --}}
                    @if($canManage)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-800">Lembar Kendali Pengumpulan Jawaban</h3>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">
                                    {{ $tugas->pengumpulanTugas->count() }} / {{ $peserta->count() }} Masuk
                                </span>
                            </div>

                            @if($tugas->pengumpulanTugas->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th class="px-6 py-3 text-left pl-6 text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Lengkap Anggota</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Waktu Kirim</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Dokumen Hasil</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Skor Evaluasi</th>
                                                <th class="px-6 py-3 text-right pr-6 w-32 text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 text-gray-700">
                                            @foreach($tugas->pengumpulanTugas as $pengumpulan)
                                                <tr class="hover:bg-gray-100 transition-colors">
                                                    <td class="px-6 py-4 pl-6 whitespace-nowrap">
                                                        <div class="flex items-center gap-3">
                                                            <div class="h-8 w-8 rounded-full bg-primary-100 text-primary-800 flex items-center justify-center font-semibold text-xs shrink-0">
                                                                {{ strtoupper(substr($pengumpulan->user->name ?? 'U', 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <span class="text-sm font-medium text-gray-900 block">{{ $pengumpulan->user->name ?? '-' }}</span>
                                                                <span class="text-sm text-gray-500 block mt-0.5">{{ $pengumpulan->user->email ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                                        <span class="text-sm font-medium text-gray-900 block">{{ $pengumpulan->created_at->format('d M Y') }}</span>
                                                        <span class="text-sm text-gray-500 mt-0.5 block">{{ $pengumpulan->created_at->format('H:i') }} WIB</span>
                                                        @if($pengumpulan->created_at->gt($tugas->deadline))
                                                            <span class="inline-flex mt-1 px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Terlambat</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                                        <div class="flex items-center justify-center gap-2">
                                                            {{-- REFAKTORISASI: Kestabilan ID parameter rute pratinjau dan unduh jawaban --}}
                                                            <a href="{{ route('bimtek.tugas.preview-jawaban', [$bimtek->id, $tugas->id, $pengumpulan->user_id]) }}" target="_blank" rel="noopener" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Lihat</a>
                                                            <span class="text-gray-400">|</span>
                                                            <a href="{{ route('bimtek.tugas.download-jawaban', [$bimtek->id, $tugas->id, $pengumpulan->user_id]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Unduh</a>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                                        @if($pengumpulan->nilai !== null)
                                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                                {{ $pengumpulan->nilai }} / 100
                                                            </span>
                                                        @else
                                                            <span class="text-sm text-gray-500">Belum Dinilai</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-right pr-6 whitespace-nowrap align-middle">
                                                        <button type="button"
                                                                onclick="openGradeModal('{{ $pengumpulan->user_id }}', '{{ addslashes($pengumpulan->user->name ?? '-') }}', '{{ $pengumpulan->nilai ?? '' }}', '{{ addslashes($pengumpulan->feedback ?? '') }}')"
                                                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
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
                                    <div class="p-6 bg-gray-50 border-t border-gray-200 space-y-3">
                                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Belum Mengirimkan Jawaban ({{ $belumMengumpulkan->count() }} orang)</h4>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($belumMengumpulkan as $p)
                                                <span class="px-2.5 py-1 bg-white border border-gray-200 text-gray-700 font-medium text-xs rounded-lg">
                                                    {{ $p->name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <svg class="h-12 w-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-gray-500">Belum ada lembar file tanggapan jawaban tugas yang diunggah oleh peserta kelas.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- SUB-SECTION INTERAKSI UNGGHA JAWABAN (Aktor Anggota / Peserta Kelas Only) --}}
                    @if($isPeserta)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-800">Pengumpulan Jawaban Anda</h3>
                            </div>
                            <div class="p-6">
                                @if($userSubmission)
                                    {{-- Keadaan Jika Peserta Sudah Berhasil Mengumpulkan Tugas --}}
                                    <div class="space-y-4">
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
                                            
                                            <div class="text-sm">
                                                <p class="font-semibold text-green-800">Berkas Jawaban Berhasil Dikirim</p>
                                                <p class="text-sm text-green-700 mt-0.5">Diserahkan pada {{ $userSubmission->created_at->format('d F Y, H:i') }} WIB</p>
                                            </div>
                                        </div>

                                        {{-- Informasi Berkas File Unggahan Jawaban --}}
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center min-w-0 mr-4">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3 text-gray-600 bg-gray-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900 truncate block">{{ basename($userSubmission->file_jawaban_path) }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0">
                                                {{-- REFAKTORISASI: Kestabilan ID parameter rute unduh & pratinjau --}}
                                                <a href="{{ route('bimtek.tugas.preview-jawaban', [$bimtek->id, $tugas->id, $userSubmission->user_id]) }}" target="_blank" rel="noopener" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Lihat</a>
                                                <a href="{{ route('bimtek.tugas.download-jawaban', [$bimtek->id, $tugas->id, $userSubmission->user_id]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Unduh</a>
                                            </div>
                                        </div>

                                        {{-- Panel Hasil Pemeriksaan Skor Nilai Instruktur --}}
                                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-2">
                                            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Perolehan Nilai Akhir:</span>
                                                @if($userSubmission->nilai !== null)
                                                    <span class="text-2xl font-semibold {{ $userSubmission->nilai >= 70 ? 'text-green-600' : 'text-red-600' }}">{{ $userSubmission->nilai }} <span class="text-sm font-medium text-gray-500">/ 100</span></span>
                                                @else
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Menunggu Evaluasi</span>
                                                @endif
                                            </div>
                                            @if($userSubmission->nilai !== null && $userSubmission->feedback)
                                                <div class="text-sm">
                                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Catatan Korektif Guru / Instruktur:</p>
                                                    <p class="text-gray-700 bg-white p-3 rounded-lg border border-gray-100 leading-relaxed">"{{ $userSubmission->feedback }}"</p>
                                                </div>
                                            @endif
                                            @if($userSubmission->nilai !== null && $userSubmission->penilai)
                                                <p class="text-sm text-gray-500 text-right">Divalidasi Oleh: {{ $userSubmission->penilai->name ?? '-' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- Kondisi Jika Peserta Belum Mengirimkan Berkas Dokumen Tugas --}}
                                    @if($bimtek->status !== 'berlangsung')
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-500 text-center">
                                            Gerbang pengumpulan berkas terkunci karena status kelas sedang tidak aktif.
                                        </div>
                                    @elseif($tugas->isDeadlinePassed())
                                        <div class="p-3 rounded-lg border border-red-100 bg-red-50 text-red-800 text-sm flex items-center">
                                            <span>✗ Waktu pengumpulan telah berakhir. Anda tidak dapat melampirkan lembar jawaban karena batas tenggat terlewati.</span>
                                        </div>
                                    @else
                                        {{-- REFAKTORISASI FORM SUBMIT: Kestabilan ID parameter rute pengumpulan --}}
                                        <form action="{{ route('bimtek.tugas.submit', [$bimtek->id, $tugas->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                            @csrf
                                            <div>
                                                <label for="file_jawaban" class="block text-sm font-semibold text-gray-700">
                                                    Pilih File Lembar Jawaban Kerja <span class="text-red-500">*</span>
                                                </label>
                                                <input type="file" 
                                                       name="file_jawaban" 
                                                       id="file_jawaban"
                                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                       class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-700 shadow-sm cursor-pointer focus:border-primary-500 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-800 hover:file:bg-primary-200"
                                                       required>
                                                @error('file_jawaban')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <p class="mt-1 text-xs text-gray-500">Format dokumen yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (Ukuran Maks. 20MB)</p>
                                            </div>

                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Statistik Pengumpulan Sesi</h3>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Peserta Kelas</span>
                                <span class="text-base font-semibold text-gray-800">{{ $peserta->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sudah Mengirim</span>
                                <span class="text-base font-semibold text-gray-800">{{ $tugas->pengumpulanTugas->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Belum Mengirim</span>
                                <span class="text-base font-semibold text-gray-800">{{ max(0, $peserta->count() - $tugas->pengumpulanTugas->count()) }}</span>
                            </div>

                            {{-- Progress Grafik Batang --}}
                            @php
                                $progress = $peserta->count() > 0 ? ($tugas->pengumpulanTugas->count() / $peserta->count()) * 100 : 0;
                            @endphp
                            <div class="pt-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Rasio Rampung</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ number_format($progress, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- Akumulasi Nilai Rata-rata Kelas (Untuk Panitia) --}}
                            @if($canManage && $tugas->pengumpulanTugas->count() > 0)
                                @php
                                    $dinilai = $tugas->pengumpulanTugas->whereNotNull('nilai')->count();
                                    $avgNilai = $tugas->pengumpulanTugas->whereNotNull('nilai')->avg('nilai');
                                @endphp
                                <div class="pt-4 border-t border-gray-200 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sudah Diperiksa</span>
                                        <span class="text-base font-semibold text-gray-800">{{ $dinilai }} / {{ $tugas->pengumpulanTugas->count() }}</span>
                                    </div>
                                    @if($avgNilai)
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Rata-rata Nilai</span>
                                            <span class="text-base font-semibold text-gray-800">{{ number_format($avgNilai, 1) }}</span>
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
                <div id="grade-modal-overlay" class="fixed inset-0 bg-gray-900/60"></div>

                <div class="relative bg-white rounded-xl max-w-lg w-full shadow-sm text-left border border-gray-100 overflow-hidden my-8 align-middle">
                    <form id="grade-form" method="POST" data-action-base="{{ url('bimtek/' . $bimtek->id . '/tugas/' . $tugas->id . '/pengumpulan') }}">
                        @csrf
                        <div class="p-6 space-y-5">
                            <div class="border-b border-gray-200 pb-4">
                                <h3 class="text-lg font-bold text-gray-800">Evaluasi & Beri Nilai Kelulusan</h3>
                                <p class="text-sm text-gray-500 mt-1">Nama Anggota: <span id="grade-peserta-nama" class="font-medium text-gray-900"></span></p>
                            </div>

                            <div>
                                <label for="grade-nilai" class="block text-sm font-semibold text-gray-700">
                                    Skor Nilai Akhir Kuantitatif (0 - 100) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai" id="grade-nilai" min="0" max="100"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                       required>
                            </div>

                            <div>
                                <label for="grade-feedback" class="block text-sm font-semibold text-gray-700">
                                    Catatan Masukan Korektif (Opsional)
                                </label>
                                <textarea name="feedback" id="grade-feedback" rows="3"
                                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                          placeholder="Tambahkan catatan rekomendasi perbaikan berkas jika diperlukan peserta..."></textarea>
                            </div>

                            <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                                <button type="button" id="grade-modal-close" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    Sahkan Nilai Tugas
                                </button>
                            </div>
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