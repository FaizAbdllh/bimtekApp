<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">
            Upload Dokumen Persyaratan
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">

        {{-- Tombol Kembali --}}
        <div class="mb-6">
            <a href="{{ route('bimtek.show', $bimtek->id) }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Detail Bimtek
            </a>
        </div>
        
        {{-- Judul Bimtek --}}
        <div class="mb-2">
            <h3 class="text-lg font-bold text-gray-800 leading-snug">
                {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
            </h3>
        </div>

        {{-- Status Banner Pemeriksaan Berkas --}}
        @php
            $statusColors = [
                'invited' => 'bg-primary-50 border-primary-100 text-primary-800',
                'pending' => 'bg-yellow-50 border-yellow-100 text-yellow-800',
                'verified' => 'bg-green-50 border-green-100 text-green-800',
                'rejected' => 'bg-red-50 border-red-100 text-red-800',
            ];
            $statusLabels = [
                'invited' => 'Belum Mengunggah Dokumen Persyaratan',
                'pending' => 'Dokumen Menunggu Proses Verifikasi',
                'verified' => 'Seluruh Berkas Telah Diverifikasi',
                'rejected' => 'Ada Berkas yang Ditolak - Mohon Lakukan Unggah Ulang Berkas',
            ];

            $rejectedDocs = $dokumenMap->filter(fn($doc) => $doc->status === 'rejected');
        @endphp

        <div class="mb-6 p-3 rounded-lg border text-sm {{ $statusColors[$assignment->status_verifikasi ?? 'pending'] ?? 'bg-gray-100 border-gray-200 text-gray-800' }}">
            <div class="flex items-start">
                <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>

                <div class="flex-1">
                    <p class="font-semibold">Status Berkas</p>
                    <p class="mt-0.5">{{ $statusLabels[$assignment->status_verifikasi ?? 'pending'] ?? 'Pending' }}</p>

                    {{-- Menampilkan Daftar Catatan Koreksi Jika Ada yang Ditolak --}}
                    @if(($assignment->status_verifikasi ?? '') === 'rejected' && $rejectedDocs->count() > 0)
                        <div class="mt-3 p-3 bg-white rounded-lg border border-red-100">
                            <p class="font-semibold text-red-800 mb-1">Catatan koreksi berkas dari panitia:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($rejectedDocs as $doc)
                                    @php
                                        $syaratItem = $syaratDokumens->firstWhere('id', $doc->syarat_dokumen_id);
                                    @endphp
                                    <li>
                                        <span class="font-medium text-gray-900">{{ $syaratItem->nama_dokumen ?? 'Dokumen Syarat' }}</span>
                                        @if($doc->catatan_verifikasi)
                                            <span class="block ml-5 mt-0.5 text-red-600">Alasan: {{ $doc->catatan_verifikasi }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Instruksi Aturan Unggah --}}
        <div class="mb-6 p-3 rounded-lg border border-primary-100 bg-primary-50 text-sm text-primary-800">
            <div class="flex items-start">
                <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>

                <div class="flex-1">
                    <p class="font-semibold">Petunjuk Resmi Unggah Berkas Syarat</p>

                    <ul class="mt-1 list-disc list-inside space-y-1">
                        <li>Ekstensi file digital yang diperbolehkan sistem: <strong class="font-semibold">PDF, JPG, JPEG, atau PNG</strong></li>
                        <li>Batas ukuran maksimal file unggahan: <strong class="font-semibold">5 Megabytes (5 MB)</strong></li>
                        <li>Pastikan lembar pindaian/foto dokumen terlihat <strong class="font-semibold">jelas, tajam, dan dapat terbaca</strong> panitia pokja.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Daftar Formulir Unggahan Dinamis Berdasarkan Tabel syarat_dokumens --}}
        @foreach($syaratDokumens as $syarat)
            @php
                $dokumen = $dokumenMap[$syarat->id] ?? null;
            @endphp

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-6">
                {{-- Header Kartu: Nama Dokumen + Status --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50 flex items-center justify-between gap-4">
                    <h3 class="text-lg font-bold text-primary-800">
                        {{ $syarat->nama_dokumen }}
                        @if($syarat->is_wajib) <span class="text-red-500 ml-1">*</span> @endif
                    </h3>

                    @if($dokumen)
                        @if($dokumen->status === 'pending')
                            <span class="inline-flex items-center shrink-0 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu Verifikasi</span>
                        @elseif($dokumen->status === 'approved')
                            <span class="inline-flex items-center shrink-0 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Disetujui</span>
                        @else
                            <span class="inline-flex items-center shrink-0 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>
                        @endif
                    @else
                        <span class="inline-flex items-center shrink-0 px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Belum Diunggah</span>
                    @endif
                </div>

                <div class="p-6 space-y-5">
                    {{-- Deskripsi Khusus (Hanya tampil jika diisi admin) --}}
                    @if($syarat->deskripsi_syarat)
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $syarat->deskripsi_syarat }}</p>
                    @endif

                    {{-- Jika dokumen sudah pernah diunggah --}}
                    @if($dokumen)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $dokumen->file_name }}</p>
                                    <p class="mt-1 text-sm text-gray-500">Diunggah: {{ \Carbon\Carbon::parse($dokumen->uploaded_at)->format('d M Y, H:i') }} WIB</p>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    @if(str_ends_with($dokumen->file_path, '.pdf'))
                                        <a href="{{ route('bimtek.verifikasi-dokumen.preview', ['bimtek' => $bimtek->id, 'userId' => auth()->id(), 'syaratId' => $syarat->id]) }}"
                                           target="_blank" rel="noopener"
                                           class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                            Pratinjau
                                        </a>
                                    @endif

                                    <a href="{{ route('bimtek.verifikasi-dokumen.download', ['bimtek' => $bimtek->id, 'userId' => auth()->id(), 'syaratId' => $syarat->id]) }}"
                                       class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Alasan penolakan langsung di kartu --}}
                        @if($dokumen->status === 'rejected' && $dokumen->catatan_verifikasi)
                            <div class="p-3 rounded-lg border border-red-100 bg-red-50 text-sm text-red-800">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>

                                    <div class="flex-1">
                                        <p class="font-semibold">Alasan penolakan</p>
                                        <p class="mt-1">{{ $dokumen->catatan_verifikasi }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Tampilkan form unggah ulang jika dokumen belum ada atau ditolak panitia --}}
                    @if(!$dokumen || $dokumen->status === 'rejected')
                        <form action="{{ route('bimtek.verifikasi-dokumen.upload', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            {{-- 💡 Menggunakan ID syarat dokumen sesuai validasi Controller --}}
                            <input type="hidden" name="syarat_dokumen_id" value="{{ $syarat->id }}">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700">
                                    {{ $dokumen ? 'Unggah Perbaikan / Revisi Berkas Baru' : 'Pilih Berkas Lampiran' }} <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                                       class="mt-1 block w-full text-sm text-gray-700 rounded-lg border border-gray-300 @error('file') border-red-500 @enderror bg-white shadow-sm cursor-pointer focus:border-primary-500 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-800 hover:file:bg-primary-200 file:cursor-pointer file:transition-colors">
                                <p class="mt-1 text-xs text-gray-500">PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.</p>
                                @error('file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Unggah Berkas
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
</x-app-layout>