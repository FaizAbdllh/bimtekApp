<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">

        {{-- Header Visual Petunjuk Pemulihan --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-primary-100 shadow-sm shadow-primary-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Pemulihan Kata Sandi</h2>
            <p class="text-xs text-gray-400 font-medium leading-relaxed">
                Lupa kata sandi Anda? Tidak masalah. Silakan masukkan alamat email terdaftar Anda. Sistem akan memvalidasi dan mengirimkan tautan pemulihan akun resmi (*password reset link*) langsung menuju kotak masuk email Anda.
            </p>
        </div>

        {{-- Banner Notifikasi Sukses Pengiriman Link ke Email (Session Status) --}}
        @if (session('status'))
            <div class="mb-5 p-4 bg-green-50 border border-green-100 text-green-800 rounded-xl text-xs font-semibold shadow-sm flex items-start gap-2">
                <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Form Permintaan Tautan Reset Password --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            {{-- Input Email Address --}}
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Alamat Email Terdaftar <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       id="email"
                       value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('email') border-red-500 @enderror"
                       required
                       autofocus
                       placeholder="nama@email.com">

                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol Pengendali Navigasi Form --}}
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 font-bold text-xs uppercase tracking-wide">
                <a href="{{ route('login') }}" class="w-full sm:w-auto text-center px-4 py-2.5 text-gray-400 hover:text-gray-700 transition">
                    Kembali ke Login
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition shadow-sm">
                    Kirim Tautan Pemulihan
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>