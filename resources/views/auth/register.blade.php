<x-guest-layout>
    <div class="p-6 sm:p-8">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Registrasi Peserta Eksternal</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar untuk mengikuti Bimbingan Teknis BBPMP Sumbar</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700" />
                <x-text-input id="name" 
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name"
                    placeholder="Masukkan nama lengkap anda" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                <x-text-input id="email" 
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username"
                    placeholder="Masukkan email aktif anda" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Asal Instansi -->
            <div class="mb-4">
                <x-input-label for="asal_instansi" :value="__('Asal Instansi')" class="text-gray-700" />
                <x-text-input id="asal_instansi" 
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" 
                    type="text" 
                    name="asal_instansi" 
                    :value="old('asal_instansi')" 
                    required 
                    placeholder="Contoh: SDN 01 Padang" />
                <x-input-error :messages="$errors->get('asal_instansi')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
                <x-text-input id="password" 
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700" />
                <x-text-input id="password_confirmation" 
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Ulangi password anda" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200">
                Daftar
            </button>
        </form>

        <div class="mt-6 text-center border-t border-gray-200 pt-6">
            <p class="text-sm text-gray-600 mb-2">Sudah punya akun?</p>
            <a href="{{ route('login') }}" class="inline-block text-primary-600 hover:text-primary-800 font-medium">
                ← Kembali ke halaman login
            </a>
        </div>
    </div>
</x-guest-layout>
