<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="text-gray-500 hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    {{-- REFAKTORISASI: Menggunakan parameter ID eksplisit dan menambahkan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="text-gray-500 hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="text-gray-500 hover:text-primary-600 transition-colors">Absensi</a></li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    <li class="text-gray-700">Edit Sesi</li>
                </ol>
            </nav>
            <h2 class="text-xl font-bold text-gray-800">
                Edit Sesi Absensi
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                {{-- REFAKTORISASI: Kestabilan pengiriman UUID pada parameter rute update --}}
                <form action="{{ route('bimtek.absensi.update', [$bimtek->id, $sesi->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                        <h3 class="text-lg font-bold text-primary-800">Edit Sesi Absensi</h3>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Ringkasan Informasi Kelas Bimtek terkait --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-100 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Bimtek</h3>
                            {{-- REFAKTORISASI: Fallback judul rencana --}}
                            <p class="text-sm font-medium text-gray-900">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                        </div>

                        {{-- Form Input Nama Sesi --}}
                        <div>
                            <label for="nama_sesi" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Sesi <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="nama_sesi"
                                   id="nama_sesi"
                                   value="{{ old('nama_sesi', $sesi->nama_sesi) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('nama_sesi') border-red-500 @enderror"
                                   placeholder="Contoh: Hari 1 - Sesi Pagi"
                                   required>
                            @error('nama_sesi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Form Pilihan Opsi Status Sesi Absensi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Status Sesi <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Opsi Terbuka --}}
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-colors @error('status') border-red-500 @enderror">
                                    <input type="radio" name="status" value="terbuka" class="sr-only" {{ old('status', $sesi->status) === 'terbuka' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-semibold text-gray-900">Terbuka</span>
                                            <span class="mt-1 text-sm text-gray-500 leading-relaxed">Peserta dapat melakukan absensi secara langsung melalui sistem.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden flex-shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 0l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>

                                {{-- Opsi Ditutup --}}
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-colors @error('status') border-red-500 @enderror">
                                    <input type="radio" name="status" value="ditutup" class="sr-only" {{ old('status', $sesi->status) === 'ditutup' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-semibold text-gray-900">Ditutup</span>
                                            <span class="mt-1 text-sm text-gray-500 leading-relaxed">Gerbang absensi dikunci sementara, proses pengisian kehadiran ditangguhkan.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden flex-shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 0l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol Pengendali Form --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                        {{-- REFAKTORISASI: Penyelarasan ID parameter pembatalan kembali --}}
                        <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 hover:bg-primary-800 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script Handler Manajemen Desain Komponen Radio Terpilih --}}
    <script>
        document.querySelectorAll('input[name="status"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="status"]').forEach(r => {
                    const label = r.closest('label');
                    const icon = label.querySelector('svg');
                    if (r.checked) {
                        label.classList.add('border-primary-600', 'ring-2', 'ring-primary-600', 'bg-primary-50/10');
                        icon.classList.remove('hidden');
                    } else {
                        label.classList.remove('border-primary-600', 'ring-2', 'ring-primary-600', 'bg-primary-50/10');
                        icon.classList.add('hidden');
                    }
                });
            });
            // Sinkronisasi status checked inisial perulangan data
            if (radio.checked) {
                const label = radio.closest('label');
                const icon = label.querySelector('svg');
                label.classList.add('border-primary-600', 'ring-2', 'ring-primary-600', 'bg-primary-50/10');
                icon.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>