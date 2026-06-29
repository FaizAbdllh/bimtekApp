<x-app-layout>
    <x-slot name="header">
        Upload Dokumen Persyaratan - {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('bimtek.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium">
                            Bimtek
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <a href="{{ route('bimtek.show', $bimtek->id) }}" class="ml-1 text-gray-500 hover:text-primary-600 text-sm font-medium truncate max-w-[250px]">
                                {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-bold">Upload Dokumen</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Tombol Kembali Kembali --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Bimtek
                </a>
            </div>

            {{-- Status Banner Pemeriksaan Berkas --}}
            @php
                $statusColors = [
                    'invited' => 'bg-blue-50 border-blue-200 text-blue-800',
                    'pending' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                    'verified' => 'bg-green-50 border-green-200 text-green-800',
                    'rejected' => 'bg-red-50 border-red-200 text-red-800',
                ];
                $statusLabels = [
                    'invited' => 'Belum Mengunggah Dokumen Persyaratan',
                    'pending' => 'Dokumen Menunggu Proses Verifikasi Panitia Pokja',
                    'verified' => 'Seluruh Berkas Dinyatakan Sah & Terverifikasi',
                    'rejected' => 'Ada Berkas yang Ditolak - Mohon Lakukan Unggah Ulang Berkas',
                ];
                
                $rejectedDocs = $userDokumen->where('status', 'rejected');
            @endphp
            <div class="mb-6 p-4 rounded-xl border-l-4 bg-white shadow-sm {{ $statusColors[$assignment->status_verifikasi] ?? 'bg-gray-100 border-gray-400 text-gray-800' }}">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1 text-sm">
                        <span class="font-bold block text-base">Status Kelulusan Berkas: {{ $statusLabels[$assignment->status_verifikasi] ?? $assignment->status_verifikasi }}</span>
                        @if($assignment->status_verifikasi === 'rejected' && $rejectedDocs->count() > 0)
                            <div class="mt-3 p-3 bg-white rounded-xl border border-red-100 text-red-900">
                                <p class="font-bold mb-1 uppercase tracking-wide text-xs text-red-600">Catatan Koreksi Berkas dari Panitia:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs font-semibold">
                                    @foreach($rejectedDocs as $doc)
                                        <li>
                                            <span class="text-gray-900">{{ \Illuminate\Support\Str::of($doc->jenis_dokumen)->replace('_', ' ')->title() }}</span>
                                            @if($doc->catatan_verifikasi)
                                                <span class="text-red-500 italic block ml-4 mt-0.5 font-medium bg-red-50 px-2 py-0.5 rounded border border-red-100/60 w-fit">Alasan: "{{ $doc->catatan_verifikasi }}"</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Ringkas Bimtek --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-6">
                    <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-4">Metrik Informasi Pelaksanaan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Lokasi Kegiatan</p>
                            {{-- REFAKTORISASI LOKASI: Menggunakan lokasi_aktual dengan fallback rencana awal --}}
                            <p class="text-gray-900 font-bold mt-0.5">{{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Jadwal Pelaksanaan</p>
                            <p class="text-gray-900 font-bold mt-0.5">
                                {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('d F Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('d F Y') : '-') }}
                                @if($bimtek->tanggal_selesai_aktual)
                                    - {{ $bimtek->tanggal_selesai_aktual->format('d F Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Instruksi Aturan Unggah --}}
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-6 shadow-sm shadow-blue-50">
                <h4 class="font-bold text-blue-900 mb-2 flex items-center text-sm uppercase tracking-wider">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Petunjuk Resmi Unggah Berkas Syarat
                </h4>
                @php
                    $jenisDokumenLabel = collect($jenisDokumenWajib)
                        ->map(fn ($jenis) => \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title())
                        ->implode(', ');
                @endphp
                <ul class="text-xs text-blue-800 space-y-1.5 ml-7 font-semibold">
                    <li>• Ekstensi file digital yang diperbolehkan sistem: <span class="text-blue-950 font-bold">PDF, JPG, JPEG, atau PNG</span></li>
                    <li>• Batas ukuran maksimal file unggahan: <span class="text-blue-950 font-bold">2 Megabytes (2 MB)</span></li>
                    <li>• Komponen berkas mandatori kelas ini: <span class="text-blue-950 font-bold">{{ $jenisDokumenLabel }}</span></li>
                    <li>• Pastikan lembar pindaian/foto dokumen terlihat <span class="text-blue-950 font-bold">jelas, tajam, dan dapat terbaca</span> panitia pokja.</li>
                </ul>
            </div>

            {{-- Daftar Formulir Unggahan Dinamis --}}
            @foreach($jenisDokumenWajib as $jenis)
                @php
                    $dokumen = $dokumenMap[$jenis] ?? null;
                    $label = \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title();
                @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                    <div class="p-6">
                        <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Berkas {{ $label }}
                        </h3>

                        @if($dokumen)
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $dokumen->file_name }}</p>
                                        <p class="text-[11px] text-gray-400 font-medium mt-0.5">Diunggah: {{ $dokumen->uploaded_at->format('d M Y, H:i') }} WIB</p>
                                        <div class="mt-2">
                                            @if($dokumen->status === 'pending')
                                                <span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-800 rounded-md text-[10px] font-bold uppercase tracking-wide border border-yellow-200">Menunggu Verifikasi</span>
                                            @elseif($dokumen->status === 'approved')
                                                <span class="px-2.5 py-0.5 bg-green-100 text-green-800 rounded-md text-[10px] font-bold uppercase tracking-wide border border-green-200">✓ Disetujui</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-red-100 text-red-800 rounded-md text-[10px] font-bold uppercase tracking-wide border border-red-200">✗ Ditolak</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-2 shrink-0 font-bold text-xs">
                                        @if(str_ends_with($dokumen->file_path, '.pdf'))
                                            <a href="{{ route('bimtek.verifikasi-dokumen.preview', $dokumen->id) }}" 
                                               target="_blank" rel="noopener"
                                               class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-purple-600 rounded-lg hover:bg-gray-50 transition shadow-sm">Lihat</a>
                                        @endif
                                        <a href="{{ route('bimtek.verifikasi-dokumen.download', $dokumen->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-primary-600 rounded-lg hover:bg-gray-50 transition shadow-sm">Unduh</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Tampilkan form unggah jika berkas kosong atau ditolak panitia --}}
                        @if(!$dokumen || $dokumen->status === 'rejected')
                            <form action="{{ route('bimtek.verifikasi-dokumen.upload', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-dashed border-gray-100">
                                @csrf
                                <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                                
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                        Pilih Berkas Lampiran {{ $label }} Baru <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                                           class="w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none p-2 text-xs font-semibold">
                                    @error('file')
                                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl font-bold text-xs uppercase tracking-wide hover:bg-primary-700 transition shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Mulai Unggah Berkas
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>