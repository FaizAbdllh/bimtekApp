<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        {{-- Header Visual Registrasi --}}
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Registrasi Peserta</h2>
            {{-- 💡 DINAMIS: Subtitle berubah otomatis jika dibuka lewat link pendaftaran mandiri --}}
            @if(isset($inviteCode))
                <p class="text-xs text-primary-600 font-bold mt-1">
                    Pendaftaran Mandiri Kelas: <span class="text-gray-800">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span>
                </p>
            @else
                <p class="text-xs text-gray-400 font-medium mt-1">Daftar akun baru untuk mengikuti Bimbingan Teknis BBPMP Sumbar</p>
            @endif
        </div>

        {{-- 💡 DINAMIS: Aksi form otomatis bergeser ke 'register.submit' jika mendeteksi link undangan --}}
        <form method="POST" action="{{ isset($inviteCode) ? route('register.submit') : route('register') }}" class="space-y-4">
            @csrf

            {{-- 💡 SUNTIKAN LOGIKA: Otomatis aktif menyuntikkan payload jika diakses via Link Pendaftaran Mandiri --}}
            @if(isset($inviteCode))
                <input type="hidden" name="invite_code" value="{{ $inviteCode }}">
                
                {{-- Banner Info Kontekstual Kelas --}}
                <div class="p-4 bg-blue-50/60 border border-blue-100 rounded-xl text-xs text-blue-800 font-semibold shadow-sm shadow-blue-50">
                    <p class="text-[10px] uppercase tracking-wide text-blue-900 font-bold mb-0.5">Konfirmasi Akses:</p>
                    <p class="leading-relaxed text-gray-600 font-medium">Setelah pembuatan akun selesai, sistem akan langsung memasukkan Anda ke dalam daftar peserta resmi kelas Bimtek di atas.</p>
                </div>
            @endif

            {{-- Input Nama Lengkap --}}
            <div>
                <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('name') border-red-500 @enderror" 
                       placeholder="Masukkan nama lengkap beserta gelar"
                       required 
                       autofocus 
                       autocomplete="name">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Alamat Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Alamat Email Aktif <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('email') border-red-500 @enderror" 
                       placeholder="contoh@email.com"
                       required 
                       autocomplete="username">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- 💡 TAMBAHAN BARU: Input Identitas NIP (Diselaraskan dengan skema tabel users) --}}
            <div>
                <label for="nip" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Nomor Induk Pegawai (NIP) <span class="text-gray-400 text-[10px] normal-case font-medium">(Opsional)</span>
                </label>
                <input type="text" 
                       name="nip" 
                       id="nip" 
                       value="{{ old('nip') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('nip') border-red-500 @enderror" 
                       placeholder="1995xxxxxxxxxxxxxx atau isi '-' jika non-ASN">
                @error('nip')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Asal Instansi / Sekolah --}}
            <div>
                <label for="asal_instansi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Asal Instansi Kedinasan / Sekolah <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="asal_instansi" 
                       id="asal_instansi" 
                       value="{{ old('asal_instansi') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('asal_instansi') border-red-500 @enderror" 
                       placeholder="Contoh: SDN 01 Padang atau SMKN 2 Pariaman"
                       required>
                @error('asal_instansi')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Kata Sandi --}}
            <div>
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Kata Sandi <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('password') border-red-500 @enderror" 
                       placeholder="Gunakan minimal 8 karakter"
                       required 
                       autocomplete="new-password">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Konfirmasi Kata Sandi --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                       placeholder="Ulangi kembali kata sandi Anda"
                       required 
                       autocomplete="new-password">
            </div>

            {{-- Tombol Kirim Pendaftaran --}}
            <div class="pt-2">
                <button type="submit" class="w-full inline-flex justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
                    {{ isset($inviteCode) ? 'Daftar & Masuk Kelas' : 'Daftar Akun Baru' }}
                </button>
            </div>
        </form>

        {{-- Footer Pindah Jalur ke Login --}}
        <div class="mt-6 text-center border-t border-gray-100 pt-5">
            <p class="text-xs text-gray-400 font-medium mb-2">Sudah memiliki akun pendaftaran?</p>
            <a href="{{ route('login') }}" class="text-xs font-bold uppercase tracking-wide text-primary-600 hover:text-primary-800 transition-colors">
                &larr; Kembali ke Login
            </a>
        </div>
        
    </div>
</x-guest-layout>