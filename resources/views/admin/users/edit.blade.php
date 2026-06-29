<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Tombol Navigasi Kembali --}}
            <a href="{{ route('admin.users.index') }}" class="p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors shrink-0 shadow-sm border border-gray-200 bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Koreksi Data Pengguna
                </h2>
                <p class="text-xs font-mono text-gray-400 mt-0.5">UUID: {{ $user->id }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Main Form Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 border-b border-gray-50 pb-3">Profil Pengguna: <span class="text-gray-800 font-bold normal-case">{{ $user->name }}</span></h3>

                    {{-- REFAKTORISASI: Kestabilan pengiriman UUID parameter rute update --}}
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-5">
                        @csrf
                        @value
                        @method('PUT')

                        {{-- Input Nama Lengkap --}}
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Nama Lengkap Pengguna <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus
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
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('email') border-red-500 @enderror"
                                   placeholder="contoh@email.com">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Ganti Kata Sandi (Opsional) --}}
                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Kata Sandi Baru <span class="text-gray-400 font-medium">(Ganti Opsional)</span>
                            </label>
                            <input type="password" name="password" id="password"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('password') border-red-500 @enderror"
                                   placeholder="Biarkan kosong jika tidak ingin mengubah password aktif">
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-[11px] text-gray-400 font-medium">Isi kolom di atas hanya jika Anda ingin mereset/mengganti sandi lama. Minimal 8 karakter.</p>
                        </div>

                        {{-- Input NIP Kedaerahan --}}
                        <div>
                            <label for="nip" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Nomor Induk Pegawai (NIP)
                            </label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip', $user->nip) }}" maxlength="18"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('nip') border-red-500 @enderror"
                                   placeholder="Nomor Induk Pegawai (opsional)">
                            @error('nip')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Asal Instansi --}}
                        <div>
                            <label for="asal_instansi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Asal Instansi / Sekolah Penugasan
                            </label>
                            <input type="text" name="asal_instansi" id="asal_instansi" value="{{ old('asal_instansi', $user->asal_instansi) }}"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-medium py-2.5 @error('asal_instansi') border-red-500 @enderror"
                                   placeholder="Nama instansi/sekolah asal (opsional)">
                            @error('asal_instansi')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Penugasan Kewenangan Role --}}
                        <div>
                            <label for="role_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Hak Akses / Peran Akun <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" id="role_id" required
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-semibold bg-white py-2.5 @error('role_id') border-red-500 @enderror"
                                    @if($user->id === auth()->id()) disabled @endif>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->nama_peran }}
                                    </option>
                                @endforeach
                            </select>
                            
                            {{-- Jaring Pengaman Proteksi Pengubahan Role Mandiri --}}
                            @if($user->id === auth()->id())
                                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                <div class="mt-2 p-3 bg-amber-50 border border-amber-100 text-amber-800 rounded-xl text-xs font-semibold flex items-center gap-1.5 shadow-inner shadow-amber-50">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Sistem mengunci elemen: Anda tidak diperkenankan mendegradasi/mengubah level peran kepunyaan akun Anda sendiri.</span>
                                </div>
                            @endif
                            @error('role_id')
                                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Panel Rekam Informasi Log Waktu Pangkalan Data --}}
                        <div class="mb-6 p-4 bg-gray-50/80 rounded-xl border border-gray-100 shadow-inner shadow-gray-50">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Metadata Record Keanggotaan</h4>
                            <div class="text-xs text-gray-500 space-y-1 font-semibold">
                                <p>Dibuat Sistem: <span class="text-gray-700">{{ $user->created_at->format('d M Y, H:i') }} WIB</span></p>
                                <p>Terakhir Diupdate: <span class="text-gray-700">{{ $user->updated_at->format('d M Y, H:i') }} WIB</span></p>
                            </div>
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
                                Perbarui Akun User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>