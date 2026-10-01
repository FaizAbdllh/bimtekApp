<x-app-layout>
    <x-slot name="header">
        Koreksi Data Pengguna
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Navigasi --}}
            <div class="mb-6">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    Kembali ke Daftar Pengguna
                </a>
            </div>

            {{-- Formulir --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">

                {{-- Header Form --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                    <h2 class="text-lg font-bold text-primary-800">
                        Formulir Koreksi Data Pengguna
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Pengguna:
                        <span class="font-medium text-gray-900">
                            {{ $user->name }}
                        </span>
                    </p>
                </div>

                <form method="POST"
                      action="{{ route('admin.users.update', $user->id) }}">

                    @csrf
                    @method('PUT')

                    <div class="p-6 space-y-5">

                        {{-- Nama --}}
                        <div>
                            <label for="name"
                                   class="block text-sm font-semibold text-gray-700">
                                Nama Lengkap Pengguna
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Masukkan nama lengkap beserta gelar"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('name') border-red-500 @enderror"
                                   required
                                   autofocus>

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email"
                                   class="block text-sm font-semibold text-gray-700">
                                Alamat Email Resmi
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="contoh@email.com"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('email') border-red-500 @enderror"
                                   required>

                            @error('email')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password"
                                   class="block text-sm font-semibold text-gray-700">
                                Kata Sandi Baru
                                <span class="text-xs font-normal text-gray-500">(Opsional)</span>
                            </label>

                            <input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="Kosongkan jika tidak ingin mengubah kata sandi"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('password') border-red-500 @enderror">

                            @error('password')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-1 text-sm text-gray-500">
                                Isi hanya jika kata sandi pengguna ingin diganti. Minimal 8 karakter.
                            </p>
                        </div>

                        {{-- NIP --}}
                        <div>
                            <label for="nip"
                                   class="block text-sm font-semibold text-gray-700">
                                Nomor Induk Pegawai (NIP)
                            </label>

                            <input type="text"
                                   name="nip"
                                   id="nip"
                                   value="{{ old('nip', $user->nip) }}"
                                   maxlength="18"
                                   placeholder="Nomor Induk Pegawai (opsional)"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('nip') border-red-500 @enderror">

                            @error('nip')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Asal Instansi --}}
                        <div>
                            <label for="asal_instansi"
                                   class="block text-sm font-semibold text-gray-700">
                                Asal Instansi / Sekolah Penugasan
                            </label>

                            <input type="text"
                                   name="asal_instansi"
                                   id="asal_instansi"
                                   value="{{ old('asal_instansi', $user->asal_instansi) }}"
                                   placeholder="Nama instansi atau sekolah asal"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('asal_instansi') border-red-500 @enderror">

                            @error('asal_instansi')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Hak Akses --}}
                        <div>
                            <label for="role_id"
                                   class="block text-sm font-semibold text-gray-700">
                                Hak Akses / Peran Akun
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="role_id"
                                    id="role_id"
                                    required
                                    @if($user->id === auth()->id()) disabled @endif
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('role_id') border-red-500 @enderror">

                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->nama_peran }}
                                    </option>
                                @endforeach
                            </select>

                            @if($user->id === auth()->id())
                                <input type="hidden"
                                       name="role_id"
                                       value="{{ $user->role_id }}">

                                <p class="mt-2 text-sm text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                                    Peran akun Anda sendiri tidak dapat diubah melalui halaman ini.
                                </p>
                            @endif

                            @error('role_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Metadata --}}
                        <div class="pt-2">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="text-sm font-semibold text-gray-700">
                                    Informasi Record
                                </h4>

                                <div class="mt-2 space-y-1 text-sm text-gray-500">
                                    <p>
                                        Dibuat:
                                        <span class="text-gray-700">
                                            {{ $user->created_at->format('d M Y, H:i') }} WIB
                                        </span>
                                    </p>
                                    <p>
                                        Terakhir diperbarui:
                                        <span class="text-gray-700">
                                            {{ $user->updated_at->format('d M Y, H:i') }} WIB
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Batal
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Perbarui Data Pengguna
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>