<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium">
                    <li>
                        <a href="{{ route('bimtek.index') }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">
                            Bimtek
                        </a>
                    </li>

                    <li>
                        <span class="mx-1 text-gray-400">/</span>
                    </li>

                    {{-- REFAKTORISASI: Menggunakan parameter ID eksplisit dan menambahkan fallback judul rencana --}}
                    <li>
                        <a href="{{ route('bimtek.show', $bimtek->id) }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">
                            {{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}
                        </a>
                    </li>

                    <li>
                        <span class="mx-1 text-gray-400">/</span>
                    </li>

                    <li>
                        <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">
                            Absensi
                        </a>
                    </li>

                    <li>
                        <span class="mx-1 text-gray-400">/</span>
                    </li>

                    <li class="text-gray-700">
                        Buat Sesi
                    </li>
                </ol>
            </nav>

            <h2 class="text-xl font-bold text-gray-800">
                Buat Sesi Absensi Baru
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">

            {{-- Form Header --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                <h3 class="text-lg font-bold text-primary-800">
                    Informasi Sesi Absensi
                </h3>

                <p class="mt-1 text-sm text-primary-800">
                    Tentukan label sesi dan status gerbang presensi yang akan digunakan.
                </p>
            </div>

            {{-- REFAKTORISASI: Kestabilan pengiriman UUID pada parameter rute store --}}
            <form action="{{ route('bimtek.absensi.store', $bimtek->id) }}" method="POST">
                @csrf

                <div class="p-6 space-y-5">

                    {{-- Ringkasan Informasi Kelas Bimtek terkait --}}
                    <div class="bg-gray-50 rounded-lg border border-gray-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Nama Kelas Bimtek
                        </p>

                        {{-- REFAKTORISASI: Fallback judul rencana --}}
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                        </p>
                    </div>

                    {{-- Form Input Nama Sesi --}}
                    <div>
                        <label for="nama_sesi" class="block text-sm font-semibold text-gray-700">
                            Label / Nama Sesi Absensi
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                            name="nama_sesi"
                            id="nama_sesi"
                            value="{{ old('nama_sesi') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('nama_sesi') border-red-500 @enderror"
                            placeholder="Contoh: Hari 1 - Sesi Pagi (08:00 - 12:00)"
                            required>

                        @error('nama_sesi')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-1.5 text-sm text-gray-500 leading-relaxed">
                            Gunakan nama yang jelas sebagai representasi lembar presensi sesi ini.
                        </p>
                    </div>

                    {{-- Form Pilihan Opsi Status Sesi Absensi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">
                            Status Gerbang Sesi
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Opsi Terbuka --}}
                            <label
                                class="relative flex cursor-pointer rounded-lg border border-gray-200 bg-white p-4 hover:bg-gray-100 transition-colors @error('status') border-red-500 @enderror">

                                <input type="radio"
                                    name="status"
                                    value="terbuka"
                                    class="sr-only"
                                    {{ old('status', 'terbuka') === 'terbuka' ? 'checked' : '' }}>

                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-semibold text-gray-900">
                                            Langsung Buka (Terbuka)
                                        </span>

                                        <span class="mt-1 text-sm text-gray-500 leading-relaxed">
                                            Gerbang presensi aktif, peserta dapat langsung scan QR atau melakukan absensi.
                                        </span>
                                    </span>
                                </span>

                                <svg class="w-5 h-5 text-primary-600 hidden flex-shrink-0 ml-2"
                                    viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>

                            {{-- Opsi Ditutup --}}
                            <label
                                class="relative flex cursor-pointer rounded-lg border border-gray-200 bg-white p-4 hover:bg-gray-100 transition-colors @error('status') border-red-500 @enderror">

                                <input type="radio"
                                    name="status"
                                    value="ditutup"
                                    class="sr-only"
                                    {{ old('status') === 'ditutup' ? 'checked' : '' }}>

                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-semibold text-gray-900">
                                            Simpan Draf (Ditutup)
                                        </span>

                                        <span class="mt-1 text-sm text-gray-500 leading-relaxed">
                                            Sesi disimpan terlebih dahulu ke sistem, pembukaan gerbang diaktifkan nanti saat acara dimulai.
                                        </span>
                                    </span>
                                </span>

                                <svg class="w-5 h-5 text-primary-600 hidden flex-shrink-0 ml-2"
                                    viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>
                        </div>

                        @error('status')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Tombol Pengendali Form Kendali Sesi --}}
                <div class="flex items-center justify-between bg-gray-50 border-t border-gray-200 p-4">

                    {{-- REFAKTORISASI: Penyelarasan ID parameter pembatalan kembali --}}
                    <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Sesi Absensi
                    </button>
                </div>
            </form>
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
                        label.classList.add(
                            'border-primary-600',
                            'ring-2',
                            'ring-primary-600',
                            'bg-primary-50'
                        );
                        icon.classList.remove('hidden');
                    } else {
                        label.classList.remove(
                            'border-primary-600',
                            'ring-2',
                            'ring-primary-600',
                            'bg-primary-50'
                        );
                        icon.classList.add('hidden');
                    }
                });
            });

            // Sinkronisasi status checked inisial perulangan data
            if (radio.checked) {
                const label = radio.closest('label');
                const icon = label.querySelector('svg');

                label.classList.add(
                    'border-primary-600',
                    'ring-2',
                    'ring-primary-600',
                    'bg-primary-50'
                );
                icon.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>