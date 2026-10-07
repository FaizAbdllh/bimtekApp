<x-app-layout>
    <x-slot name="header">
        Buat Tugas Pengayaan Baru
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Navigasi --}}
            <div class="mb-6">
                <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                   <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Tugas
                </a>
            </div>

            {{-- Formulir --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">

                {{-- Header Form --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <h2 class="text-lg font-bold text-primary-800">
                        Formulir Tugas Pengayaan
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Kegiatan:
                        <span class="font-medium text-gray-900">
                            {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                        </span>
                    </p>
                </div>

                <form action="{{ route('bimtek.tugas.store', $bimtek->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="p-6 space-y-5">

                        {{-- Judul --}}
                        <div>
                            <label for="judul"
                                   class="block text-sm font-semibold text-gray-700">
                                Judul Lembar Kerja / Tugas
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text"
                                   name="judul"
                                   id="judul"
                                   value="{{ old('judul') }}"
                                   placeholder="Contoh: Tugas Mandiri 1 - Penyusunan Draf Analisis"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('judul') border-red-500 @enderror"
                                   required>

                            @error('judul')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="deskripsi"
                                   class="block text-sm font-semibold text-gray-700">
                                Rincian Instruksi & Ketentuan Kerja
                            </label>

                            <textarea name="deskripsi"
                                      id="deskripsi"
                                      rows="5"
                                      placeholder="Jelaskan instruksi pengerjaan, format penulisan, dan indikator penilaian tugas..."
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm resize-y @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>

                            @error('deskripsi')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Deadline --}}
                        <div>
                            <label for="deadline"
                                   class="block text-sm font-semibold text-gray-700">
                                Batas Waktu Pengumpulan
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="datetime-local"
                                   name="deadline"
                                   id="deadline"
                                   value="{{ old('deadline') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('deadline') border-red-500 @enderror"
                                   required>

                            @error('deadline')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-1 text-sm text-gray-500">
                                Pengumpulan akan ditutup secara otomatis setelah batas waktu terlewati.
                            </p>
                        </div>

                        {{-- Lampiran --}}
                        <div>
                            <label for="file_instruksi"
                                   class="block text-sm font-semibold text-gray-700">
                                Lampiran Dokumen / Template
                                <span class="text-xs font-normal text-gray-500">(Opsional)</span>
                            </label>

                            <input type="file"
                                   name="file_instruksi"
                                   id="file_instruksi"
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-800 hover:file:bg-primary-200">

                            @error('file_instruksi')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-1 text-xs text-gray-500">
                                PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR. Maksimal 20MB.
                            </p>
                        </div>

                        {{-- Aksi --}}
                        <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                            <a href="{{ route('bimtek.tugas.index', $bimtek->id) }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Batal
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Rilis Lembar Tugas
                            </button>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>