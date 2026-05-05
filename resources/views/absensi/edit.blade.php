<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.show', $bimtek) }}" class="hover:text-primary-600">{{ Str::limit($bimtek->judul_final, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek) }}" class="hover:text-primary-600">Absensi</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-900 font-medium">Edit Sesi</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Edit Sesi Absensi
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <form action="{{ route('bimtek.absensi.update', [$bimtek, $sesi]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-6">
                        {{-- Info Bimtek --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Bimtek</h3>
                            <p class="text-gray-900 font-medium">{{ $bimtek->judul_final }}</p>
                        </div>

                        {{-- Nama Sesi --}}
                        <div>
                            <label for="nama_sesi" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Sesi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_sesi" 
                                   id="nama_sesi"
                                   value="{{ old('nama_sesi', $sesi->nama_sesi) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('nama_sesi') border-red-500 @enderror"
                                   placeholder="Contoh: Hari 1 - Sesi Pagi"
                                   required>
                            @error('nama_sesi')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status Sesi <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none @error('status') border-red-500 @enderror">
                                    <input type="radio" name="status" value="terbuka" class="sr-only" {{ old('status', $sesi->status) === 'terbuka' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Terbuka</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Peserta dapat melakukan absensi</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>

                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none @error('status') border-red-500 @enderror">
                                    <input type="radio" name="status" value="ditutup" class="sr-only" {{ old('status', $sesi->status) === 'ditutup' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Ditutup</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Absensi tidak dapat dilakukan</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-primary-600 hidden" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-xl">
                        <a href="{{ route('bimtek.absensi.show', [$bimtek, $sesi]) }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Radio button styling
        document.querySelectorAll('input[name="status"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="status"]').forEach(r => {
                    const label = r.closest('label');
                    const icon = label.querySelector('svg');
                    if (r.checked) {
                        label.classList.add('border-primary-600', 'ring-2', 'ring-primary-600');
                        icon.classList.remove('hidden');
                    } else {
                        label.classList.remove('border-primary-600', 'ring-2', 'ring-primary-600');
                        icon.classList.add('hidden');
                    }
                });
            });
            // Initial state
            if (radio.checked) {
                const label = radio.closest('label');
                const icon = label.querySelector('svg');
                label.classList.add('border-primary-600', 'ring-2', 'ring-primary-600');
                icon.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
