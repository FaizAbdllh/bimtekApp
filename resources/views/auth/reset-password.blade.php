<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        {{-- Header Visual Atur Ulang Sandi --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-primary-100 shadow-sm shadow-primary-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Atur Ulang Kata Sandi</h2>
            <p class="text-xs text-gray-400 font-medium leading-relaxed">
                Tautan token berhasil divalidasi. Silakan masukkan alamat email Anda dan buat kata sandi baru untuk memulihkan akses masuk ke sistem.
            </p>
        </div>

        {{-- Form Eksekusi Penyimpanan Sandi Baru --}}
        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            {{-- Token Rahasia Reset Bawaan Rute Laravel --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Input Konfirmasi Alamat Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Konfirmasi Alamat Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email', $request->email) }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('email') border-red-500 @enderror" 
                       placeholder="nama@email.com"
                       required 
                       autofocus 
                       autocomplete="username">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Kata Sandi Baru --}}
            <div>
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Kata Sandi Baru <span class="text-red-500">*</span>
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

            {{-- Input Konfirmasi Kata Sandi Baru --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Ulangi Kata Sandi Baru <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('password_confirmation') border-red-500 @enderror" 
                       placeholder="Ulangi kata sandi baru Anda"
                       required 
                       autocomplete="new-password">
                @error('password_confirmation')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Submit Pembaruan Kata Sandi --}}
            <div class="pt-2">
                <button type="submit" class="w-full inline-flex justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
                    Perbarui Kata Sandi & Masuk
                </button>
            </div>
        </form>

        {{-- Footer Pindah Jalur --}}
        <div class="mt-6 text-center border-t border-gray-100 pt-4">
            <a href="{{ route('login') }}" class="text-xs font-bold uppercase tracking-wide text-gray-400 hover:text-gray-600 transition-colors">
                &larr; Batalkan & Kembali ke Login
            </a>
        </div>
        
    </div>
</x-guest-layout>