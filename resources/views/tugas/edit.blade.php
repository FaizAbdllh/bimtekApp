<x-app-layout>
    <x-slot name="header">
        Edit Tugas Pembelajaran
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Navigasi Kembali --}}
            <div class="mb-4">
                {{-- REFAKTORISASI: Kestabilan UUID parameter rute kembali ke detail tugas --}}
                <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Tugas
                </a>
            </div>

            {{-- Main Form Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 uppercase tracking-wide text-xs">Koreksi Informasi Lembar Kerja</h2>
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="text-xs text-gray-400 font-semibold mt-0.5">Bimtek: {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                </div>

                {{-- REFAKTORISASI: Kestabilan pengiriman UUID pada parameter rute update --}}
                <form action="{{ route('bimtek.tugas.update', [$bimtek->id, $tugas->id]) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Input Judul Tugas --}}
                    <div>
                        <label for="judul" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Judul Lembar Kerja / Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               id="judul" 
                               value="{{ old('judul', $tugas->judul) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-medium @error('judul') border-red-500 @enderror"
                               required>
                        @error('judul')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Rincian Deskripsi Instruksi Kerja --}}
                    <div>
                        <label for="deskripsi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Rincian Instruksi & Ketentuan Kerja
                        </label>
                        <textarea name="deskripsi" 
                                  id="deskripsi" 
                                  rows="5"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-medium @error('deskripsi') border-red-500 @enderror"
                                  placeholder="Jelaskan detail tugas yang harus dikerjakan peserta...">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Batas Waktu (Deadline) --}}
                    <div>
                        <label for="deadline" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Batas Waktu Pengumpulan (Deadline) <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="deadline" 
                               id="deadline" 
                               value="{{ old('deadline', $tugas->deadline ? $tugas->deadline->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-semibold @error('deadline') border-red-500 @enderror"
                               required>
                        @error('deadline')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                        
                        @if($tugas->isDeadlinePassed())
                            <div class="mt-2 p-3 bg-amber-50 border border-amber-100 text-amber-800 rounded-xl text-xs font-semibold flex items-center gap-1.5 shadow-inner">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>Perhatian: Batas waktu pengumpulan awal telah terlewati. Anda dapat memundurkan jadwal jika diperlukan perpanjangan kelas.</span>
                            </div>
                        @else
                            <p class="mt-1.5 text-[11px] text-gray-400 font-medium">Sistem otomatis menolak unggahan jawaban peserta setelah waktu batas terlewati.</p>
                        @endif
                    </div>

                    {{-- Input Unggah File Pendukung Lembar Kerja --}}
                    <div>
                        <label for="file_instruksi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Dokumen Lampiran Panduan Tugas
                        </label>
                        
                        {{-- Deteksi Berkas Aktif Saat Ini --}}
                        @if($tugas->file_instruksi_path)
                            <div class="mb-3 p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between shadow-inner shadow-gray-50">
                                <div class="flex items-center min-w-0 mr-4">
                                    <svg class="w-7 h-7 text-gray-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Berkas Panduan Aktif:</p>
                                        <p class="text-sm font-bold text-gray-900 truncate mt-0.5">{{ basename($tugas->file_instruksi_path) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 font-bold text-xs shrink-0">
                                    {{-- REFAKTORISASI: Kestabilan parameter ID rute pratinjau dan unduh --}}
                                    <a href="{{ route('bimtek.tugas.preview-instruksi', [$bimtek->id, $tugas->id]) }}" target="_blank" rel="noopener" class="text-purple-600 hover:text-purple-800">Pratinjau</a>
                                    <a href="{{ route('bimtek.tugas.download-instruksi', [$bimtek->id, $tugas->id]) }}" class="text-primary-600 hover:text-primary-800">Unduh</a>
                                </div>
                            </div>
                        @endif

                        <input type="file" 
                               name="file_instruksi" 
                               id="file_instruksi"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                               class="w-full border border-gray-300 rounded-xl bg-gray-50 text-xs font-semibold text-gray-500 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 p-2">
                        @error('file_instruksi')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-[10px] text-gray-400 font-medium">{{ $tugas->file_instruksi_path ? 'Pilih berkas baru jika ingin menimpa/mengganti dokumen instruksi lama.' : 'Format yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (Batas Ukuran Maks. 20MB)' }}</p>
                    </div>

                    {{-- Tombol Aksi Gabungan (Termasuk trigger hapus zona bahaya) --}}
                    <div class="flex items-center justify-between pt-5 border-t border-gray-100 font-bold text-xs uppercase tracking-wide">
                        <button type="button" 
                                onclick="if(confirm('PERINGATAN PERMANEN: Apakah Anda yakin ingin menghapus total lembar penugasan ini? Seluruh berkas file unggahan jawaban peserta akan ikut hangus dari pangkalan data!')) document.getElementById('delete-form').submit();" 
                                class="px-4 py-2.5 text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition shadow-sm">
                            Hapus Tugas
                        </button>
                        <div class="flex items-center gap-2">
                            {{-- REFAKTORISASI: Penyelarasan ID parameter rute pembatalan kembali --}}
                            <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
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