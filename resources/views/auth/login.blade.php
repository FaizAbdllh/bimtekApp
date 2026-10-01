<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-xl">
        
        {{-- Header Login --}}
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Login Pengguna</h2>
            <p class="text-sm text-gray-500 mt-1">Silakan masuk menggunakan alamat email dan kata sandi Anda</p>
        </div>

        {{-- Banner Status --}}
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-100 text-sm text-green-800 rounded-lg">
                {{ session('status') }}
            </div>
        @endif
        
        {{-- Pesan Sukses --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-100 text-sm text-green-800 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Pesan Info --}}
        @if (session('info'))
            <div class="mb-4 p-3 bg-primary-50 border border-primary-100 text-sm text-primary-800 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        {{-- Form Login --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-500 @enderror" placeholder="Masukkan alamat email resmi Anda" required autofocus autocomplete="username">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-500 @enderror" placeholder="Masukkan kata sandi Anda" required autocomplete="current-password">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Ingat Saya & Lupa Password --}}
            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center cursor-pointer text-gray-600">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2">Ingat saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-primary-600 hover:text-primary-800 transition-colors">Lupa password?</a>
                @endif
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-2">
                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Masuk ke Aplikasi
                </button>
            </div>
        </form>

        {{-- Footer --}}
        <div class="mt-6 text-center border-t border-gray-200 pt-5">
            <p class="text-sm text-gray-500">Hubungi Admin IT jika Anda mengalami kendala akses masuk.</p>
        </div>
    </div>
</x-guest-layout>
