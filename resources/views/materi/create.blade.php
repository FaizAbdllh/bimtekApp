<x-app-layout>
    <x-slot name="header">
        Upload Materi Pembelajaran
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

            {{-- Main Form Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <h3 class="text-lg font-bold text-primary-800">Registrasi Modul Bahan Ajar Baru</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('bimtek.materi.store', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        {{-- Input Judul Materi --}}
                        <div>
                            <label for="judul" class="block text-sm font-semibold text-gray-700">
                                Judul / Label Materi Pembelajaran <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('judul') border-red-500 @enderror"
                                   placeholder="Contoh: Modul 1 - Pengenalan Aplikasi Manajemen Bimtek"
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
                                <option value="">-- Pilih Tipe Berkas --</option>
                                @foreach($tipeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipe') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tipe')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-sm text-gray-500 leading-relaxed">
                                <strong class="font-semibold">Materi:</strong> Konten bahan tayang/pembelajaran utama narasumber. <br><strong class="font-semibold">Panduan:</strong> Lembar instruksi kerja/petunjuk teknis penggunaan sistem.
                            </p>
                        </div>

                        {{-- Input Zona Drop Berkas (File Upload Box dengan Alpine.js Interaktif) --}}
                        <div>
                            <label for="file" class="block text-sm font-semibold text-gray-700">
                                Lampiran Berkas Digital <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 py-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary-500 bg-gray-50 transition-colors @error('file') border-red-500 @enderror"
                                 x-data="{ fileName: '' }">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-500 justify-center">
                                        <label for="file" class="relative cursor-pointer font-medium text-primary-600 hover:text-primary-800 transition-colors focus-within:outline-none">
                                            <span>Pilih dokumen</span>
                                            <input id="file" name="file" type="file" class="sr-only" required
                                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                   @change="fileName = $event.target.files[0]?.name || ''">
                                        </label>
                                        <p class="pl-1">atau drag and drop berkas</p>
                                    </div>
                                    <p class="text-xs text-gray-500">Ekstensi: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (Batas Ukuran Maks. 20MB)</p>
                                    <p x-show="fileName" x-cloak x-text="'Berkas terpilih: ' + fileName" class="mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-800 w-fit mx-auto"></p>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi Kendali Form --}}
                        <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                            <a href="{{ route('bimtek.materi.index', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Mulai Upload Materi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>