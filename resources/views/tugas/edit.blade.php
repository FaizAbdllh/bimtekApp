<x-app-layout>
    <x-slot name="header">
        Edit Tugas
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Back --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.tugas.show', [$bimtek, $tugas]) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Tugas
                </a>
            </div>

            {{-- Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Edit Tugas</h2>
                    <p class="text-sm text-gray-500">{{ $tugas->judul }}</p>
                </div>

                <form action="{{ route('bimtek.tugas.update', [$bimtek, $tugas]) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Judul Tugas --}}
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               id="judul" 
                               value="{{ old('judul', $tugas->judul) }}"
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
                                  placeholder="Jelaskan detail tugas yang harus dikerjakan peserta...">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
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
                               value="{{ old('deadline', $tugas->deadline->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('deadline') border-red-500 @enderror"
                               required>
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        @if($tugas->isDeadlinePassed())
                            <p class="mt-1 text-sm text-amber-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Deadline sudah terlewat. Anda dapat memperpanjang jika diperlukan.
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Peserta tidak dapat mengumpulkan tugas setelah deadline.</p>
                        @endif
                    </div>

                    {{-- File Instruksi --}}
                    <div>
                        <label for="file_instruksi" class="block text-sm font-medium text-gray-700 mb-2">
                            File Instruksi
                        </label>
                        
                        {{-- Current File --}}
                        @if($tugas->file_instruksi_path)
                            <div class="mb-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">File instruksi saat ini</p>
                                        <p class="text-xs text-gray-500">{{ basename($tugas->file_instruksi_path) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('bimtek.tugas.preview-instruksi', [$bimtek, $tugas]) }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-700">Lihat</a>
                                    <a href="{{ route('bimtek.tugas.download-instruksi', [$bimtek, $tugas]) }}" class="text-sm text-primary-600 hover:text-primary-700">Download</a>
                                </div>
                            </div>
                        @endif

                        <input type="file" 
                               name="file_instruksi" 
                               id="file_instruksi"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('file_instruksi') border-red-500 @enderror">
                        @error('file_instruksi')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">{{ $tugas->file_instruksi_path ? 'Upload file baru untuk mengganti.' : 'Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (max. 20MB)' }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-6 border-t">
                        <button type="button" 
                                onclick="if(confirm('Yakin ingin menghapus tugas ini? Semua pengumpulan akan ikut terhapus.')) document.getElementById('delete-form').submit();" 
                                class="px-4 py-2.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            Hapus Tugas
                        </button>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('bimtek.tugas.show', [$bimtek, $tugas]) }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Form --}}
    <form id="delete-form" action="{{ route('bimtek.tugas.destroy', [$bimtek, $tugas]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-app-layout>
