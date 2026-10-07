<section class="space-y-6">
    <header class="border-b border-gray-200 pb-4">
        <h2 class="text-lg font-bold text-gray-800">
            Informasi Profil Pengguna
        </h2>

        <p class="mt-1 text-sm text-gray-500 leading-relaxed max-w-2xl">
            Perbarui informasi profil akun dan alamat email pendaftaran Anda secara mandiri. Pastikan data identitas kedaerahan Anda sudah sesuai dengan berkas kedinasan aktif.
        </p>
    </header>

    

    {{-- Form Utama Eksekusi Pembaruan Data Diri --}}
    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5 max-w-xl">
        @csrf
        @method('patch')

        {{-- Input Nama Lengkap --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700">
                Nama Lengkap Anda <span class="text-red-500">*</span>
            </label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   value="{{ old('name', $user->name) }}" 
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('name') border-red-500 @enderror" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Masukkan nama lengkap beserta gelar akademik">
            
            @if($errors->has('name'))
                <p class="mt-1 text-sm text-red-600">{{ $errors->first('name') }}</p>
            @endif
        </div>

        {{-- Input Alamat Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">
                Alamat Email Aktif <span class="text-red-500">*</span>
            </label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   value="{{ old('email', $user->email) }}" 
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('email') border-red-500 @enderror" 
                   required 
                   autocomplete="username"
                   placeholder="nama@email.com">
            
            @if($errors->has('email'))
                <p class="mt-1 text-sm text-red-600">{{ $errors->first('email') }}</p>
            @endif

            {{-- Alur Validasi Khusus Jika Email User Belum Lulus Verifikasi Akun --}}
            {{-- @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-100 rounded-lg space-y-2 text-sm text-yellow-800">
                    <p class="flex items-center gap-1">
                        <span>⚠ Status: Alamat email Anda belum lolos verifikasi sistem.</span>
                        <button form="send-verification" class="font-medium text-primary-600 hover:text-primary-800 underline transition-colors">
                            Klik disini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-green-800 font-semibold">
                            ✓ Tautan verifikasi baru yang segar telah berhasil dikirimkan ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif --}}
        </div>

        {{-- Input Identitas NIP Mandiri --}}
        <div>
            <label for="nip" class="block text-sm font-semibold text-gray-700">
                Nomor Induk Pegawai (NIP)
            </label>
            <input id="nip" 
                   name="nip" 
                   type="text" 
                   value="{{ old('nip', $user->nip) }}" 
                   maxlength="18"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('nip') border-red-500 @enderror" 
                   placeholder="Masukkan 18 digit NIP Anda (Opsional)">
            
            @if($errors->has('nip'))
                <p class="mt-1 text-sm text-red-600">{{ $errors->first('nip') }}</p>
            @endif
        </div>

        {{-- Input Asal Instansi Mandiri --}}
        <div>
            <label for="asal_instansi" class="block text-sm font-semibold text-gray-700">
                Asal Satuan Instansi Kerja / Sekolah
            </label>
            <input id="asal_instansi" 
                   name="asal_instansi" 
                   type="text" 
                   value="{{ old('asal_instansi', $user->asal_instansi) }}" 
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('asal_instansi') border-red-500 @enderror" 
                   placeholder="Contoh: SDN 05 Padang Pasir (Opsional)">
            
            @if($errors->has('asal_instansi'))
                <p class="mt-1 text-sm text-red-600">{{ $errors->first('asal_instansi') }}</p>
            @endif
        </div>

        {{-- Informasi Tingkat Hak Akses Peran (Read-Only State Badge) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700">Tingkat Hak Akses / Peran Anda</label>
            <div class="mt-1 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 select-none">
                {{ $user->role->nama_peran ?? 'Anggota Eksternal' }}
            </div>
            <p class="mt-1.5 text-sm text-gray-500">* Tingkat kewenangan peran bersifat permanen. Hubungi tim teknis Admin IT jika terdapat kesalahan penugasan posisi.</p>
        </div>

        {{-- Kelompok Aksi Tombol Simpan & Status Hasil --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Simpan Profil
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-800 bg-green-50 border border-green-100 px-3 py-2 rounded-lg"
                >
                    Perubahan informasi data profil Anda berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>