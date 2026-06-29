<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        {{-- Header Visual Jaring Pengaman --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-amber-100 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Konfirmasi Kata Sandi</h2>
            <p class="text-xs text-gray-400 font-medium leading-relaxed">
                Anda mencoba memasuki area sensitif sistem DIPA Bimtek. Demi keamanan, silakan konfirmasi kata sandi akun Anda terlebih dahulu sebelum melanjutkan.
            </p>
        </div>

        {{-- Form Aksi Validasi Password --}}
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            {{-- Input Kata Sandi --}}
            <div>
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Kata Sandi Akun Anda <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                       required 
                       autocomplete="current-password"
                       placeholder="Masukkan kata sandi aktif Anda"
                       autofocus>
                
                @if($errors->has('password'))
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">
                        {{ $errors->first('password') }}
                    </p>
                @endif
            </div>

            {{-- Tombol Eksekusi Aksi --}}
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-2 font-bold text-xs uppercase tracking-wide">
                <a href="{{ url()->previous() ?? url('/') }}" class="w-full sm:w-auto text-center px-4 py-2.5 text-gray-500 hover:text-gray-800 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition shadow-sm">
                    Verifikasi Akses
                </button>
            </div>
        </form>
    </div>
</div>
@endsection