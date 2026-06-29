<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        {{-- Header Visual Selamat Datang --}}
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Login Pengguna</h2>
            <p class="text-xs text-gray-400 font-medium mt-1">Silakan masuk menggunakan alamat email dan kata sandi Anda</p>
        </div>

        {{-- Banner Status Sesi --}}
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-100 text-green-700 rounded-xl text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form Login Otentikasi --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Input Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Alamat Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('email') border-red-500 @enderror" 
                       placeholder="Masukkan alamat email resmi Anda"
                       required 
                       autofocus 
                       autocomplete="username">
                @error('email')
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
                       placeholder="Masukkan kata sandi Anda"
                       required 
                       autocomplete="current-password">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Opsi Ingat Saya & Lupa Password --}}
            <div class="flex items-center justify-between text-xs font-semibold pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none text-gray-600">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer shadow-sm">
                    <span class="ml-2">Ingat saya di perangkat ini</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-primary-600 hover:text-primary-800 transition-colors" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            {{-- Tombol Submit Masuk --}}
            <div class="pt-2">
                <button type="submit" class="w-full inline-flex justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
                    Masuk ke Aplikasi
                </button>
            </div>
        </form>

        {{-- SEKTOR VISUAL: Penyelamat Alur Aktivasi Mandiri Peserta Luar --}}
        <div class="mt-6 text-center border-t border-gray-100 pt-5 space-y-3.5">
            <div class="bg-gray-50/60 border border-gray-100 rounded-2xl p-4 shadow-inner shadow-gray-50/50">
                <p class="text-xs text-gray-500 font-medium mb-1.5 leading-relaxed">
                    Baru mendaftar dan memegang <span class="text-gray-800 font-bold">Token Aktivasi</span> dari Panitia Pokja?
                </p>
                <a class="text-xs font-bold uppercase tracking-wide text-primary-600 hover:text-primary-800 transition-colors inline-flex items-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg shadow-sm" href="{{ route('activation.form') }}">
                    Aktivasi Akun Anda Disini &rarr;
                </a>
            </div>

            <div class="text-[10px] text-gray-400 font-medium leading-relaxed space-y-0.5">
                <p>Akun aparatur internal BBPMP Sumbar diterbitkan otomatis oleh sistem.</p>
                <p>Hubungi Unit Kerja Admin IT jika Anda mengalami kendala hak akses masuk.</p>
            </div>
        </div>
        
    </div>
</x-guest-layout>