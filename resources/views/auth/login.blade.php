<x-guest-layout>
    <div class="p-6 sm:p-8">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Login User</h2>
            <p class="text-sm text-gray-500 mt-1">Silahkan login dengan username dan password yang anda miliki.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
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

            <!-- Password -->
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

            <!-- Remember Me -->
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

        <div class="mt-6 text-center border-t border-gray-200 pt-6">
            <p class="text-sm text-gray-500">
                Akun disediakan oleh Admin/Panitia Bimtek.
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Hubungi Admin jika belum memiliki akun.
            </p>
        </div>
    </div>
</x-guest-layout>
