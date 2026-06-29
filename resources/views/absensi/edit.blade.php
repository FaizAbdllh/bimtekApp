<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    {{-- REFAKTORISASI: Menggunakan parameter ID eksplisit dan menambahkan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">Absensi</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-800 font-bold">Edit Sesi</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Edit Sesi Absensi
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- REFAKTORISASI: Kestabilan pengiriman UUID pada parameter rute update --}}
                <form action="{{ route('bimtek.absensi.update', [$bimtek->id, $sesi->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-6 space-y-6">
                        {{-- Ringkasan Informasi Kelas Bimtek terkait --}}
                        <div class="bg-gray-50/80 rounded-xl border border-gray-100 p-4 shadow-inner shadow-gray-50">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Bimtek</h3>
                            {{-- REFAKTORISASI: Fallback judul rencana --}}
                            <p class="text-gray-900 font-bold text-sm">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                        </div>

                        {{-- Form Input Nama Sesi --}}
                        <div>
                            <label for="nama_sesi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Nama Sesi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_sesi" 
                                   id="nama_sesi"
                                   value="{{ old('nama_sesi', $sesi->nama_sesi) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm font-medium @error('nama_sesi') border-red-500 @enderror"
                                   placeholder="Contoh: Hari 1 - Sesi Pagi"
                                   required>
                            @error('nama_sesi')
                                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Form Pilihan Opsi Status Sesi Absensi --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Status Sesi <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Opsi Terbuka --}}
                                <label class="relative flex cursor-pointer rounded-xl border bg-white p-4 shadow-sm focus:outline-none transition-all duration-200 @error('status') border-red-500 @enderror">
                                    <input type="radio" name="status" value="terbuka" class="sr-only" {{ old('status', $sesi->status) === 'terbuka' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-bold text-gray-900">Terbuka</span>
                                            <span class="mt-1 text-xs text-gray-400 font-medium leading-relaxed">Peserta dapat melakukan absensi secara langsung melalui sistem.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden flex-shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>

                                {{-- Opsi Ditutup --}}
                                <label class="relative flex cursor-pointer rounded-xl border bg-white p-4 shadow-sm focus:outline-none transition-all duration-200 @error('status') border-red-500 @enderror">
                                    <input type="radio" name="status" value="ditutup" class="sr-only" {{ old('status', $sesi->status) === 'ditutup' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-bold text-gray-900">Ditutup</span>
                                            <span class="mt-1 text-xs text-gray-400 font-medium leading-relaxed">Gerbang absensi dikunci sementara, proses pengisian kehadiran ditangguhkan.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden flex-shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol Pengendali Form --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl shadow-inner shadow-gray-50">
                        {{-- REFAKTORISASI: Penyelarasan ID parameter pembatalan kembali --}}
                        <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}" class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 uppercase tracking-wide transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
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