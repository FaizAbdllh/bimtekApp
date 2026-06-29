<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Tombol Navigasi Kembali --}}
            <a href="{{ route('admin.users.index') }}" class="p-2.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors shrink-0 shadow-sm border border-gray-200 bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Tambah Pengguna Baru
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Registrasikan akun master pegawai internal atau eksternal sistem</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Main Form Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 border-b border-gray-50 pb-3">Formulir Isian Data Profil Akun</h3>

                    <form method="POST" class="space-y-5" action="{{ route('admin.users.store') }}">
                        @csrf

                        {{-- Input Nama Pengguna --}}
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Nama Lengkap Pengguna <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('name') border-red-500 @enderror"
                                   placeholder="Masukkan nama lengkap beserta gelar">
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Alamat Email --}}
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Alamat Email Resmi <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('email') border-red-500 @enderror"
                                   placeholder="namapegawai@bbpmp.id atau nama@email.com">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Kata Sandi --}}
                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Kata Sandi Kunci Akses <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('password') border-red-500 @enderror"
                                   placeholder="Gunakan minimal 8 karakter gabungan">
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-[11px] text-gray-400 font-medium">Pastikan panjang sandi memenuhi standar keamanan enkripsi pangkalan data.</p>
                        </div>

                        {{-- Input NIP Kedaerahan --}}
                        <div>
                            <label for="nip" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Nomor Induk Pegawai (NIP)
                            </label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip') }}" maxlength="18"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('nip') border-red-500 @enderror"
                                   placeholder="Contoh: 1995xxxxxxxxxxxxxx (Opsional)">
                            @error('nip')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Asal Instansi / Sekolah --}}
                        <div>
                            <label for="asal_instansi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Nama Asal Instansi Kedinasan / Sekolah
                            </label>
                            <input type="text" name="asal_instansi" id="asal_instansi" value="{{ old('asal_instansi') }}"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('asal_instansi') border-red-500 @enderror"
                                   placeholder="Contoh: Dinas Pendidikan Prov. Sumbar / SDN 01 Padang (Opsional)">
                            @error('asal_instansi')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Penugasan Tingkat Role --}}
                        <div>
                            <label for="role_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Tingkat Hak Akses / Peran <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" id="role_id" required
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-semibold bg-white py-2.5 @error('role_id') border-red-500 @enderror">
                                <option value="">-- Pilih Level Kewenangan Peran --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nama_peran }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Baris Pengendali Aksi Form --}}
                        <div class="flex items-center justify-end gap-2 pt-5 border-t border-gray-100 font-bold text-xs uppercase tracking-wide">
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Akun User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>