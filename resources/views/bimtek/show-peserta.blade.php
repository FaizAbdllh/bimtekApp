<x-app-layout>
    <x-slot name="header">
        Detail Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>

            {{-- Header Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        @php
                            $statusColors = [
                                'persiapan' => 'bg-yellow-100 text-yellow-800',
                                'berlangsung' => 'bg-blue-100 text-blue-800',
                                'selesai' => 'bg-green-100 text-green-800',
                                'dibatalkan' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$bimtek->status_pelaksanaan] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($bimtek->status_pelaksanaan) }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $bimtek->judul_final }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Mode Pelaksanaan:</span>
                            <span class="text-gray-900 font-medium block">{{ $bimtek->pengajuan?->mode_pelaksanaan_label ?? $bimtek->mode_pelaksanaan_label }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Tautan Virtual:</span>
                            @if($bimtek->virtual_meeting_url)
                                <a href="{{ $bimtek->virtual_meeting_url }}" target="_blank" rel="noopener" class="text-primary-600 hover:text-primary-700 font-medium block break-all">Buka Ruang Virtual</a>
                            @else
                                <span class="text-gray-900 font-medium block">-</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-500">Lokasi:</span>
                            <span class="text-gray-900 font-medium block">{{ $bimtek->lokasi_aktual ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Tanggal:</span>
                            <span class="text-gray-900 font-medium block">
                                {{ $bimtek->tanggal_mulai_aktual?->format('d M Y') ?? '-' }}
                                @if($bimtek->tanggal_selesai_aktual && $bimtek->tanggal_mulai_aktual != $bimtek->tanggal_selesai_aktual)
                                    - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Deskripsi Jadwal --}}
                    @if($bimtek->deskripsi_jadwal)
                        <div class="mt-4 pt-4 border-t">
                            <span class="text-gray-500 text-sm">Deskripsi Jadwal:</span>
                            <p class="text-gray-700 mt-1">{{ $bimtek->deskripsi_jadwal }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Verification Alert --}}
            @php
                $pesertaPivot = $bimtek->users()
                    ->where('users.id', auth()->id())
                    ->where('bimtek_user.peran_kontekstual', 'peserta')
                    ->first();
                $statusVerifikasi = $pesertaPivot ? ($pesertaPivot->pivot->status_verifikasi ?? 'invited') : 'invited';
                $isVerified = $statusVerifikasi === 'verified';
            @endphp

            @if($bimtek->butuh_verifikasi_dokumen && !$isVerified)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-yellow-800">
                                @if($statusVerifikasi === 'invited')
                                    Verifikasi Dokumen Diperlukan
                                @elseif($statusVerifikasi === 'pending')
                                    Dokumen Sedang Diverifikasi
                                @elseif($statusVerifikasi === 'rejected')
                                    Dokumen Ditolak - Upload Ulang Diperlukan
                                @endif
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                @if($statusVerifikasi === 'invited')
                                    <p>Anda perlu mengupload dokumen persyaratan sebelum dapat mengakses fitur Absensi, Tugas, dan Sertifikat.</p>
                                @elseif($statusVerifikasi === 'pending')
                                    <p>Dokumen Anda sedang dalam proses verifikasi oleh panitia. Anda akan dapat mengakses fitur setelah dokumen disetujui.</p>
                                @elseif($statusVerifikasi === 'rejected')
                                    <p>Dokumen Anda ditolak. Silakan upload ulang dokumen yang sesuai untuk mendapatkan akses ke fitur Bimtek.</p>
                                @endif
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                                    @if($statusVerifikasi === 'invited' || $statusVerifikasi === 'rejected')
                                        Upload Dokumen
                                    @else
                                        Lihat Status Dokumen
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tab Navigation --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ activeTab: 'materi' }">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px overflow-x-auto">
                        <button @click="activeTab = 'materi'" 
                                :class="activeTab === 'materi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                            Materi
                        </button>
                        @if($bimtek->has_tugas)
                            <button @click="activeTab = 'tugas'" 
                                    :class="activeTab === 'tugas' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                                Tugas
                            </button>
                        @endif
                        <button @click="activeTab = 'absensi'" 
                                :class="activeTab === 'absensi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                            Absensi
                        </button>
                        @if($bimtek->has_sertifikat)
                            <button @click="activeTab = 'sertifikat'" 
                                    :class="activeTab === 'sertifikat' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                                Sertifikat
                            </button>
                        @endif
                    </nav>
                </div>

                {{-- Tab Content - Materi --}}
                <div class="p-6" x-show="activeTab === 'materi'">
                    @if(!$isVerified && $bimtek->butuh_verifikasi_dokumen)
                        {{-- Locked State for Unverified Peserta --}}
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                            <p class="text-sm text-gray-600 max-w-md mx-auto">
                                Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses materi pembelajaran.
                            </p>
                        </div>
                    @else
                        {{-- Accessible Content for Verified Peserta --}}
                        @if($bimtek->materis->count() > 0)
                            <div class="space-y-3">
                                @foreach($bimtek->materis as $materi)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="p-2 bg-white rounded-lg border">
                                                @if(str_ends_with($materi->file_path, '.pdf'))
                                                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                                    </svg>
                                                @elseif(str_ends_with($materi->file_path, ['.doc', '.docx']))
                                                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $materi->judul }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $materi->tipe === 'materi' ? 'Materi Pembelajaran' : 'Panduan/Petunjuk' }}
                                                    • {{ $materi->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            @if(in_array(pathinfo($materi->file_path, PATHINFO_EXTENSION), ['pdf']))
                                                <a href="{{ route('bimtek.materi.preview', [$bimtek, $materi]) }}" target="_blank" 
                                                   class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition" 
                                                   title="Lihat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('bimtek.materi.download', [$bimtek, $materi]) }}" 
                                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition" 
                                               title="Download">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('bimtek.materi.index', $bimtek) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat Daftar Lengkap →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Belum ada materi.</p>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Tab Content - Tugas --}}
                @if($bimtek->has_tugas)
                    <div class="p-6" x-show="activeTab === 'tugas'">
                    @if(!$isVerified && $bimtek->butuh_verifikasi_dokumen)
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                            <p class="text-sm text-gray-600 max-w-md mx-auto">
                                Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses tugas.
                            </p>
                        </div>
                    @else
                        @if($bimtek->tugas->count() > 0)
                            <div class="space-y-3">
                                @foreach($bimtek->tugas as $tugas)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="p-2 bg-white rounded-lg border">
                                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $tugas->judul }}</p>
                                                <p class="text-xs text-gray-500">
                                                    Tugas • {{ $tugas->deadline ? $tugas->deadline->format('d M Y H:i') : 'Tanpa deadline' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            @if($tugas->file_instruksi_path)
                                                <a href="{{ route('bimtek.tugas.preview-instruksi', [$bimtek, $tugas]) }}" target="_blank"
                                                   class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                                   title="Lihat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('bimtek.tugas.download-instruksi', [$bimtek, $tugas]) }}"
                                                   class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                                   title="Download">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                </a>
                                            @else
                                                <a href="{{ route('bimtek.tugas.show', [$bimtek, $tugas]) }}"
                                                   class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                                   title="Lihat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('bimtek.tugas.index', $bimtek) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat Daftar Lengkap →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Belum ada tugas.</p>
                            </div>
                        @endif
                    @endif
                    </div>
                @endif

                {{-- Tab Content - Absensi --}}
                <div class="p-6" x-show="activeTab === 'absensi'">
                    @php
                        $modePelaksanaan = $bimtek->mode_pelaksanaan_code;
                    @endphp
                    @if(!$isVerified && $bimtek->butuh_verifikasi_dokumen)
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                            <p class="text-sm text-gray-600 max-w-md mx-auto">
                                Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses absensi.
                            </p>
                        </div>
                    @else
                        @if($bimtek->sesiAbsensis->count() > 0)
                            <div class="space-y-3">
                                @foreach($bimtek->sesiAbsensis as $sesi)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="p-2 bg-white rounded-lg border">
                                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $sesi->nama_sesi }}</p>
                                                <p class="text-xs text-gray-500">
                                                    Sesi Absensi • {{ ucfirst($sesi->status) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            <a href="{{ route('bimtek.absensi.show', [$bimtek, $sesi]) }}"
                                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                               title="Lihat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @if($sesi->isOpen())
                                                @if(in_array($modePelaksanaan, ['offline', 'hybrid']))
                                                    <a href="{{ route('bimtek.absensi.scan-interface', [$bimtek, $sesi]) }}"
                                                       class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                                       title="Scan QR">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h3M14 7h3M7 17h3M14 17h3M5 3h4M15 3h4M5 21h4M15 21h4"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                @if(in_array($modePelaksanaan, ['online', 'hybrid']))
                                                    <a href="{{ route('bimtek.absensi.show', [$bimtek, $sesi]) }}"
                                                       class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                                       title="Upload Bukti Online">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M4 12l1.41-1.41a2 2 0 012.83 0L9 11m0 0l3.59-3.59a2 2 0 012.82 0L20 12m-11-1v6"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('bimtek.absensi.index', $bimtek) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat Daftar Lengkap →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Belum ada sesi absensi.</p>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Tab Content - Sertifikat --}}
                @if($bimtek->has_sertifikat)
                    <div class="p-6" x-show="activeTab === 'sertifikat'">
                    @if(!$isVerified && $bimtek->butuh_verifikasi_dokumen)
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                            <p class="text-sm text-gray-600 max-w-md mx-auto">
                                Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses sertifikat.
                            </p>
                        </div>
                    @else
                        @if($bimtek->sertifikats->count() > 0)
                            <div class="space-y-3">
                                @foreach($bimtek->sertifikats as $sertifikat)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="p-2 bg-white rounded-lg border">
                                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">Sertifikat Bimtek</p>
                                                <p class="text-xs text-gray-500">
                                                    Nomor: {{ $sertifikat->nomor_sertifikat ?? '-' }} • {{ $sertifikat->tanggal_terbit ? $sertifikat->tanggal_terbit->format('d M Y') : '-' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            <a href="{{ route('bimtek.sertifikat.preview', [$bimtek, $sertifikat]) }}" target="_blank"
                                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                               title="Lihat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('bimtek.sertifikat.download', [$bimtek, $sertifikat]) }}"
                                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-white rounded-lg transition"
                                               title="Download">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('bimtek.sertifikat.index', $bimtek) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat Daftar Lengkap →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3 0 2.5 3 5 3 5s3-2.5 3-5c0-1.657-1.343-3-3-3z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Belum ada sertifikat.</p>
                            </div>
                        @endif
                    @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
