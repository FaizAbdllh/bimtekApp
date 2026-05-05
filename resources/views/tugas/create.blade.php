<x-app-layout>
    <x-slot name="header">
        Buat Tugas Baru
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Back --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.tugas.index', $bimtek) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Tugas
                </a>
            </div>

            {{-- Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Buat Tugas Baru</h2>
                    <p class="text-sm text-gray-500">{{ $bimtek->judul_final }}</p>
                </div>

                <form action="{{ route('bimtek.tugas.store', $bimtek) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    {{-- Judul Tugas --}}
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               id="judul" 
                               value="{{ old('judul') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('judul') border-red-500 @enderror"
                               placeholder="Masukkan judul tugas"
                               required>
                        @error('judul')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Tugas
                        </label>
                        <textarea name="deskripsi" 
                                  id="deskripsi" 
                                  rows="5"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('deskripsi') border-red-500 @enderror"
                                  placeholder="Jelaskan detail tugas yang harus dikerjakan peserta...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="deadline" 
                               id="deadline" 
                               value="{{ old('deadline') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('deadline') border-red-500 @enderror"
                               required>
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Peserta tidak dapat mengumpulkan tugas setelah deadline.</p>
                    </div>

                    {{-- File Instruksi --}}
                    <div>
                        <label for="file_instruksi" class="block text-sm font-medium text-gray-700 mb-2">
                            File Instruksi (Opsional)
                        </label>
                        <input type="file" 
                               name="file_instruksi" 
                               id="file_instruksi"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('file_instruksi') border-red-500 @enderror">
                        @error('file_instruksi')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (max. 20MB)</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('bimtek.tugas.index', $bimtek) }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 transition-colors">
                            Buat Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
