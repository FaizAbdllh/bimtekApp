<x-app-layout>
    <x-slot name="header">
        Edit Materi Pembelajaran
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Navigasi Kembali --}}
            <div class="mb-4">
                {{-- REFAKTORISASI: Kestabilan UUID parameter rute kembali --}}
                <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Materi
                </a>
            </div>

            {{-- Ringkasan Informasi Kelas Bimtek terkait --}}
            <div class="bg-primary-50/60 border border-primary-100 rounded-xl p-4 mb-6 shadow-inner shadow-primary-50">
                <p class="text-xs font-bold text-primary-800 uppercase tracking-wider">
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <span class="text-primary-600">Kelas Pelaksanaan:</span> {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                </p>
            </div>

            {{-- Main Form Card Wrapper --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 uppercase tracking-wide text-xs border-b border-gray-50 pb-3">Koreksi Informasi Modul Pembelajaran</h2>

                    {{-- REFAKTORISASI: Kestabilan pengiriman UUID pada parameter rute update --}}
                    <form action="{{ route('bimtek.materi.update', [$bimtek->id, $materi->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Input Judul Materi --}}
                        <div>
                            <label for="judul" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Judul / Label Materi Pembelajaran <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul', $materi->judul) }}"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('judul') border-red-500 @enderror"
                                   required>
                            @error('judul')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Klasifikasi Tipe Dokumen --}}
                        <div>
                            <label for="tipe" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Klasifikasi Kelompok Dokumen <span class="text-red-500">*</span>
                            </label>
                            <select name="tipe" id="tipe" 
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-semibold bg-white py-2.5 @error('tipe') border-red-500 @enderror"
                                    required>
                                @foreach($tipeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipe', $materi->tipe) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tipe')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Penunjuk Lampiran Dokumen Aktif Saat Ini --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Berkas Digital Aktif Saat Ini</label>
                            <div class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-200/80 shadow-inner shadow-gray-50">
                                @php
                                    $ext = pathinfo($materi->file_path, PATHINFO_EXTENSION);
                                    $iconColor = match(strtolower($ext)) {
                                        'pdf' => 'text-red-500',
                                        'doc', 'docx' => 'text-blue-500',
                                        'ppt', 'pptx' => 'text-orange-500',
                                        'xls', 'xlsx' => 'text-green-500',
                                        default => 'text-gray-500',
                                    };
                                @endphp
                                <svg class="w-8 h-8 {{ $iconColor }} mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $materi->file_name ?? basename($materi->file_path) }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ strtoupper($ext) }} Berkas</p>
                                </div>
                                {{-- REFAKTORISASI: Kestabilan ID parameter rute download berkas lama --}}
                                <a href="{{ route('bimtek.materi.download', [$bimtek->id, $materi->id]) }}" class="ml-4 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-primary-600 hover:text-primary-800 transition shadow-sm shrink-0">
                                    Unduh Berkas
                                </a>
                            </div>
                        </div>

                        {{-- Input Ganti Berkas Lampiran Baru (Optional) --}}
                        <div>
                            <label for="file" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Perbarui / Ganti File Lampiran <span class="text-gray-400 font-medium">(Opsional)</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-6 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-primary-400 bg-gray-50/30 transition-all duration-200 @error('file') border-red-500 @enderror"
                                 x-data="{ fileName: '' }">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-bold text-primary-600 hover:text-primary-500 focus-within:outline-none">
                                            <span>Pilih dokumen baru</span>
                                            <input id="file" name="file" type="file" class="sr-only"
                                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                   @change="fileName = $event.target.files[0]?.name || ''">
                                        </label>
                                        <p class="pl-1 font-medium text-gray-400">untuk mengganti berkas lama</p>
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-medium">Kosongkan jika tidak ingin merubah file berkas aktif (Batas Ukuran Maks. 20MB)</p>
                                    <p x-show="fileName" x-cloak x-text="'Berkas baru terpilih: ' + fileName" class="text-xs text-primary-600 font-bold mt-2 bg-primary-50 px-3 py-1 rounded-lg border border-primary-100 w-fit mx-auto"></p>
                                </div>
                            </div>
                            @error('file')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi Kendali Form Perubahan --}}
                        <div class="flex justify-end gap-2 border-t border-gray-50 pt-4 font-bold text-xs uppercase tracking-wide">
                            {{-- REFAKTORISASI: Penyelarasan ID parameter rute pembatalan kembali --}}
                            <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>