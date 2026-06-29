<x-app-layout>
    <x-slot name="header">
        Buat Tugas Pengayaan Baru
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Navigasi Kembali --}}
            <div class="mb-4">
                {{-- REFAKTORISASI: Kestabilan UUID parameter rute kembali --}}
                <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Tugas
                </a>
            </div>

            {{-- Main Form Card Wrapper --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 uppercase tracking-wide text-xs">Formulir Perilisan Tugas Kelas</h2>
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="text-xs text-gray-400 font-semibold mt-0.5">Kegiatan: <span class="text-primary-600">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>
                </div>

                {{-- REFAKTORISASI: Kestabilan pengiriman UUID pada parameter rute store --}}
                <form action="{{ route('bimtek.tugas.store', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf

                    {{-- Input Judul Tugas --}}
                    <div>
                        <label for="judul" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Judul Lembar Kerja / Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               id="judul" 
                               value="{{ old('judul') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-medium @error('judul') border-red-500 @enderror"
                               placeholder="Contoh: Tugas Mandiri 1 - Penyusunan Draf Analisis"
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
                                  placeholder="Jelaskan instruksi pengerjaan, format penulisan, dan indikator penilaian tugas secara detail bagi peserta...">{{ old('deskripsi') }}</textarea>
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
                               value="{{ old('deadline') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-semibold @error('deadline') border-red-500 @enderror"
                               required>
                        @error('deadline')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-[11px] text-gray-400 font-medium">Sistem otomatis mengunci gerbang unggah lembar jawaban setelah waktu deadline terlewati.</p>
                    </div>

                    {{-- Input Unggah File Pendukung Instruksi --}}
                    <div>
                        <label for="file_instruksi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Lampiran Dokumen Lembar Kerja / template (Opsional)
                        </label>
                        <input type="file" 
                               name="file_instruksi" 
                               id="file_instruksi"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                               class="w-full border border-gray-300 rounded-xl bg-gray-50 text-xs font-semibold text-gray-500 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 p-2">
                        @error('file_instruksi')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-[10px] text-gray-400 font-medium">Format yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (Batas Ukuran Maks. 20MB)</p>
                    </div>

                    {{-- Tombol Aksi Kendali Form --}}
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 font-bold text-xs uppercase tracking-wide">
                        {{-- REFAKTORISASI: Penyelarasan ID parameter rute pembatalan kembali --}}
                        <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
                            Rilis Lembar Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>