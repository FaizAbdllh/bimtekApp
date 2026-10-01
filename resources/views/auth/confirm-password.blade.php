<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-xl">
        
        {{-- Header Konfirmasi --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-yellow-50 text-yellow-700 rounded-full flex items-center justify-center mx-auto mb-3 border border-yellow-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Konfirmasi Kata Sandi</h2>
            <p class="text-sm text-gray-500 leading-relaxed">
                Anda mencoba memasuki area sensitif sistem. Demi keamanan, silakan konfirmasi kata sandi akun Anda terlebih dahulu.
            </p>
        </div>

        {{-- Form Konfirmasi --}}
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi Akun <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-500 @enderror" placeholder="Masukkan kata sandi aktif Anda" required autofocus autocomplete="current-password">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ url()->previous() ?? url('/') }}" class="w-full sm:w-auto text-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center px-5 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Verifikasi Akses
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
