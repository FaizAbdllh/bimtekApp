<x-guest-layout>
    <div class="p-6 sm:p-8">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Login User</h2>
            <p class="text-sm text-gray-500 mt-1">Silahkan login dengan username dan password yang anda miliki.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                <x-text-input id="email" 
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="Masukkan email anda" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
                <x-text-input id="password" 
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="Masukkan password anda" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mb-6">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-primary-600 hover:text-primary-800" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200">
                Masuk
            </button>
        </form>

        {{-- PERBAIKAN UTAMA: Penyelamat Alur Aktivasi Mandiri Peserta --}}
        <div class="mt-6 text-center border-t border-gray-200 pt-6 space-y-3">
            <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                <p class="text-xs text-gray-600 font-medium mb-1">
                    Sudah mendaftar mandiri & menerima Token dari Panitia?
                </p>
                <a class="text-sm font-bold text-primary-600 hover:text-primary-800 hover:underline inline-flex items-center gap-1" href="{{ route('activation.form') }}">
                    Aktivasi Akun Anda Di Sini &rarr;
                </a>
            </div>

            <div class="text-xs text-gray-400">
                <p>Akun internal BBPMP disediakan otomatis oleh sistem.</p>
                <p>Hubungi Admin IT jika mengalami kendala hak akses.</p>
            </div>
        </div>
    </div>
</x-guest-layout>