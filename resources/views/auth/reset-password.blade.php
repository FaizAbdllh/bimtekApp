<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-xl">
        
        {{-- Header Reset Password --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-primary-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Atur Ulang Kata Sandi</h2>
            <p class="text-sm text-gray-500 leading-relaxed">
                Tautan token berhasil divalidasi. Silakan masukkan email dan buat kata sandi baru untuk memulihkan akses.
            </p>
        </div>

        {{-- Form Reset Password --}}
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700">Konfirmasi Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-500 @enderror" placeholder="nama@email.com" required autofocus autocomplete="username">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Password Baru --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi Baru <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-500 @enderror" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Ulangi Kata Sandi Baru <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 @error('password_confirmation') border-red-500 @enderror" placeholder="Ulangi kata sandi baru Anda" required autocomplete="new-password">
                @error('password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-2">
                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Perbarui Kata Sandi & Masuk
                </button>
            </div>
        </form>

        {{-- Footer --}}
        <div class="mt-6 text-center border-t border-gray-200 pt-4">
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                &larr; Batalkan & Kembali ke Login
            </a>
        </div>
    </div>
</x-guest-layout>
