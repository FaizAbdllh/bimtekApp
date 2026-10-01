<x-app-layout>
    <x-slot name="header">
        Edit Tugas Pembelajaran
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Navigasi Kembali --}}
            <div class="mb-6">
                {{-- REFAKTORISASI: Kestabilan UUID parameter rute kembali ke detail tugas --}}
                <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Tugas
                </a>
            </div>

            {{-- Main Form Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <h2 class="text-lg font-bold text-primary-800">Koreksi Informasi Lembar Kerja</h2>
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="mt-1 text-sm text-gray-500">Bimtek: <span class="font-medium text-gray-900">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>
                </div>

                {{-- REFAKTORISASI: Kestabilan pengiriman UUID pada parameter rute update --}}
                <form action="{{ route('bimtek.tugas.update', [$bimtek->id, $tugas->id]) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Input Judul Tugas --}}
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700">
                            Judul Lembar Kerja / Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               id="judul" 
                               value="{{ old('judul', $tugas->judul) }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('judul') border-red-500 @enderror"
                               required>
                        @error('judul')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Rincian Deskripsi Instruksi Kerja --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700">
                            Rincian Instruksi & Ketentuan Kerja
                        </label>
                        <textarea name="deskripsi" 
                                  id="deskripsi" 
                                  rows="5"
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('deskripsi') border-red-500 @enderror"
                                  placeholder="Jelaskan detail tugas yang harus dikerjakan peserta...">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Batas Waktu (Deadline) --}}
                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-gray-700">
                            Batas Waktu Pengumpulan (Deadline) <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="deadline" 
                               id="deadline" 
                               value="{{ old('deadline', $tugas->deadline ? $tugas->deadline->format('Y-m-d\TH:i') : '') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('deadline') border-red-500 @enderror"
                               required>
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        @if($tugas->isDeadlinePassed())
                            <div class="mt-2 p-3 rounded-lg border border-yellow-100 bg-yellow-50 text-yellow-800 text-sm flex items-start">
                                <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>Perhatian: Batas waktu pengumpulan awal telah terlewati. Anda dapat memundurkan jadwal jika diperlukan perpanjangan kelas.</span>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Sistem otomatis menolak unggahan jawaban peserta setelah waktu batas terlewati.</p>
                        @endif
                    </div>

                    {{-- Input Unggah File Pendukung Lembar Kerja --}}
                    <div>
                        <label for="file_instruksi" class="block text-sm font-semibold text-gray-700">
                            Dokumen Lampiran Panduan Tugas
                        </label>
                        
                        {{-- Deteksi Berkas Aktif Saat Ini --}}
                        @if($tugas->file_instruksi_path)
                            <div class="mt-1 mb-3 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between">
                                <div class="flex items-center min-w-0 mr-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3 text-gray-600 bg-gray-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Berkas Panduan Aktif:</p>
                                        <p class="text-sm font-medium text-gray-900 truncate mt-0.5">{{ basename($tugas->file_instruksi_path) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    {{-- REFAKTORISASI: Kestabilan parameter ID rute pratinjau dan unduh --}}
                                    <a href="{{ route('bimtek.tugas.preview-instruksi', [$bimtek->id, $tugas->id]) }}" target="_blank" rel="noopener" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Pratinjau</a>
                                    <a href="{{ route('bimtek.tugas.download-instruksi', [$bimtek->id, $tugas->id]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">Unduh</a>
                                </div>
                            </div>
                        @endif

                        <input type="file" 
                               name="file_instruksi" 
                               id="file_instruksi"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                               class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-700 shadow-sm cursor-pointer focus:border-primary-500 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-800 hover:file:bg-primary-200">
                        @error('file_instruksi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $tugas->file_instruksi_path ? 'Pilih berkas baru jika ingin menimpa/mengganti dokumen instruksi lama.' : 'Format yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (Batas Ukuran Maks. 20MB)' }}</p>
                    </div>

                    {{-- Tombol Aksi Gabungan (Termasuk trigger hapus zona bahaya) --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <button type="button" 
                                onclick="if(confirm('PERINGATAN PERMANEN: Apakah Anda yakin ingin menghapus total lembar penugasan ini? Seluruh berkas file unggahan jawaban peserta akan ikut hangus dari pangkalan data!')) document.getElementById('delete-form').submit();" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            Hapus Tugas
                        </button>
                        <div class="flex items-center space-x-3">
                            {{-- REFAKTORISASI: Penyelarasan ID parameter rute pembatalan kembali --}}
                            <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Formulir Tersembunyi Eksekusi Penghapusan Destruktif --}}
    {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute destroy --}}
    <form id="delete-form" action="{{ route('bimtek.tugas.destroy', [$bimtek->id, $tugas->id]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-app-layout>