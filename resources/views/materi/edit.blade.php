<x-app-layout>
    <x-slot name="header">
        Edit Materi Pembelajaran
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Navigasi Kembali --}}
            <div class="mb-6">
                <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Materi
                </a>
            </div>

            {{-- Ringkasan Informasi Kelas Bimtek terkait --}}
            <div class="mb-6 p-3 bg-primary-50 border border-primary-100 text-primary-800 text-sm rounded-lg">
                <span class="font-semibold">Kelas Pelaksanaan:</span> {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
            </div>

            {{-- Main Form Card Wrapper --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <h3 class="text-lg font-bold text-primary-800">Koreksi Informasi Modul Pembelajaran</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('bimtek.materi.update', [$bimtek->id, $materi->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Input Judul Materi --}}
                        <div>
                            <label for="judul" class="block text-sm font-semibold text-gray-700">
                                Judul / Label Materi Pembelajaran <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul', $materi->judul) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('judul') border-red-500 @enderror"
                                   required>
                            @error('judul')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Klasifikasi Tipe Dokumen --}}
                        <div>
                            <label for="tipe" class="block text-sm font-semibold text-gray-700">
                                Klasifikasi Kelompok Dokumen <span class="text-red-500">*</span>
                            </label>
                            <select name="tipe" id="tipe" 
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('tipe') border-red-500 @enderror"
                                    required>
                                @foreach($tipeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipe', $materi->tipe) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tipe')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Penunjuk Lampiran Dokumen Aktif Saat Ini --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Berkas Digital Aktif Saat Ini</label>
                            <div class="mt-1 flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                                @php
                                    $ext = pathinfo($materi->file_path, PATHINFO_EXTENSION);
                                    $iconColor = match(strtolower($ext)) {
                                        'pdf' => 'text-red-600 bg-red-100',
                                        'doc', 'docx' => 'text-primary-600 bg-primary-100',
                                        'ppt', 'pptx' => 'text-orange-600 bg-orange-100',
                                        'xls', 'xlsx' => 'text-green-600 bg-green-100',
                                        default => 'text-gray-600 bg-gray-100',
                                    };
                                @endphp
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3 {{ $iconColor }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 truncate">{{ $materi->file_name ?? basename($materi->file_path) }}</p>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ strtoupper($ext) }} Berkas</p>
                                </div>
                                <a href="{{ route('bimtek.materi.download', [$bimtek->id, $materi->id]) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 shrink-0">
                                    Unduh Berkas
                                </a>
                            </div>
                        </div>

                        {{-- Input Ganti Berkas Lampiran Baru (Optional) --}}
                        <div>
                            <label for="file" class="block text-sm font-semibold text-gray-700">
                                Perbarui / Ganti File Lampiran <span class="font-normal text-gray-500">(Opsional)</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 py-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary-500 bg-gray-50 transition-colors @error('file') border-red-500 @enderror"
                                 x-data="{ fileName: '' }">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-500 justify-center">
                                        <label for="file" class="relative cursor-pointer font-medium text-primary-600 hover:text-primary-800 transition-colors focus-within:outline-none">
                                            <span>Pilih dokumen baru</span>
                                            <input id="file" name="file" type="file" class="sr-only"
                                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                   @change="fileName = $event.target.files[0]?.name || ''">
                                        </label>
                                        <p class="pl-1">untuk mengganti berkas lama</p>
                                    </div>
                                    <p class="text-xs text-gray-500">Kosongkan jika tidak ingin merubah file berkas aktif (Batas Ukuran Maks. 20MB)</p>
                                    <p x-show="fileName" x-cloak x-text="'Berkas baru terpilih: ' + fileName" class="mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-800 w-fit mx-auto"></p>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi Kendali Form Perubahan --}}
                        <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                            <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>