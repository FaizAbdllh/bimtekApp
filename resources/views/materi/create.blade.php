<x-app-layout>
    <x-slot name="header">
        Upload Materi
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Back --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.materi.index', $bimtek) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Materi
                </a>
            </div>

            {{-- Bimtek Info --}}
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-primary-800">
                    <span class="font-medium">Bimtek:</span> {{ $bimtek->judul_final }}
                </p>
            </div>

            {{-- Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Upload Materi Baru</h2>

                    <form action="{{ route('bimtek.materi.store', $bimtek) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Judul --}}
                        <div class="mb-4">
                            <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">
                                Judul Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('judul') border-red-500 @enderror"
                                   placeholder="Contoh: Modul 1 - Pengenalan Sistem"
                                   required>
                            @error('judul')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tipe --}}
                        <div class="mb-4">
                            <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe <span class="text-red-500">*</span>
                            </label>
                            <select name="tipe" id="tipe" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tipe') border-red-500 @enderror"
                                    required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($tipeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipe') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tipe')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">
                                <strong>Materi:</strong> Konten pembelajaran utama. <strong>Panduan:</strong> Petunjuk teknis/panduan penggunaan.
                            </p>
                        </div>

                        {{-- File Upload --}}
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
                                File <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary-400 transition @error('file') border-red-500 @enderror"
                                 x-data="{ fileName: '' }">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none">
                                            <span>Pilih file</span>
                                            <input id="file" name="file" type="file" class="sr-only" required
                                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                   @change="fileName = $event.target.files[0]?.name || ''">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (maks. 20MB)</p>
                                    <p x-show="fileName" x-text="'File dipilih: ' + fileName" class="text-sm text-primary-600 font-medium mt-2"></p>
                                </div>
                            </div>
                            @error('file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('bimtek.materi.index', $bimtek) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                Upload Materi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
