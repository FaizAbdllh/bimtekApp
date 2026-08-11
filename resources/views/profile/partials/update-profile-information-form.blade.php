<section class="space-y-6">
    <header class="border-b border-gray-100 pb-4">
        <h2 class="text-base font-bold text-gray-900 uppercase tracking-wide">
            Informasi Profil Pengguna
        </h2>

        <p class="mt-1.5 text-xs text-gray-400 font-medium leading-relaxed max-w-2xl">
            Perbarui informasi profil akun dan alamat email pendaftaran Anda secara mandiri. Pastikan data identitas kedaerahan Anda sudah sesuai dengan berkas kedinasan aktif.
        </p>
    </header>

    {{-- Form Pembantu Trigger Kirim Ulang Verifikasi Email --}}
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- Form Utama Eksekusi Pembaruan Data Diri --}}
    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5 max-w-xl">
        @csrf
        @method('patch')

        {{-- Input Nama Lengkap --}}
        <div>
            <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Nama Lengkap Anda <span class="text-red-500">*</span>
            </label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   value="{{ old('name', $user->name) }}" 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Masukkan nama lengkap beserta gelar akademik">
            
            @if($errors->has('name'))
                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $errors->first('name') }}</p>
            @endif
        </div>

        {{-- Input Alamat Email --}}
        <div>
            <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Alamat Email Aktif <span class="text-red-500">*</span>
            </label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   value="{{ old('email', $user->email) }}" 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                   required 
                   autocomplete="username"
                   placeholder="nama@email.com">
            
            @if($errors->has('email'))
                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $errors->first('email') }}</p>
            @endif

            {{-- Alur Validasi Khusus Jika Email User Belum Lulus Verifikasi Akun --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-100 rounded-xl space-y-2 text-xs font-semibold shadow-inner">
                    <p class="text-amber-800 flex items-center gap-1">
                        <span>⚠ Status: Alamat email Anda belum lolos verifikasi sistem.</span>
                        <button form="send-verification" class="text-primary-600 hover:text-primary-800 underline transition font-bold">
                            Klik disini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-green-600 font-bold">
                            ✓ Tautan verifikasi baru yang segar telah berhasil dikirimkan ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Input Identitas NIP Mandiri --}}
        <div>
            <label for="nip" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Nomor Induk Pegawai (NIP)
            </label>
            <input id="nip" 
                   name="nip" 
                   type="text" 
                   value="{{ old('nip', $user->nip) }}" 
                   maxlength="18"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                   placeholder="Masukkan 18 digit NIP Anda (Opsional)">
            
            @if($errors->has('nip'))
                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $errors->first('nip') }}</p>
            @endif
        </div>

        {{-- Input Asal Instansi Mandiri --}}
        <div>
            <label for="asal_instansi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Asal Satuan Instansi Kerja / Sekolah
            </label>
            <input id="asal_instansi" 
                   name="asal_instansi" 
                   type="text" 
                   value="{{ old('asal_instansi', $user->asal_instansi) }}" 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                   placeholder="Contoh: SDN 05 Padang Pasir (Opsional)">
            
            @if($errors->has('asal_instansi'))
                <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $errors->first('asal_instansi') }}</p>
            @endif
        </div>

        {{-- Informasi Tingkat Hak Akses Peran (Read-Only State Badge) --}}
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1.5">Tingkat Hak Akses / Peran Anda</label>
            <div class="inline-flex px-3 py-1 bg-gray-50 border border-gray-200 text-gray-700 font-bold text-xs rounded-xl shadow-inner select-none uppercase tracking-wide">
                {{ $user->role->nama_peran ?? 'Anggota Eksternal' }}
            </div>
            <p class="text-[10px] text-gray-400 font-medium mt-1.5">* Tingkat kewenangan peran bersifat permanen. Hubungi tim teknis Admin IT jika terdapat kesalahan penugasan posisi.</p>
        </div>

        {{-- Kelompok Aksi Tombol Simpan & Status Hasil --}}
        <div class="flex items-center gap-4 pt-2 font-bold text-xs uppercase tracking-wide">
            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition shadow-sm">
                Simpan Profil
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-bold text-green-600 bg-green-50 border border-green-100 px-3 py-1.5 rounded-lg normal-case shadow-sm"
                >
                    ✓ Perubahan informasi data profil Anda berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>