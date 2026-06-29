<section class="space-y-6">
    <header class="border-b border-gray-100 pb-4">
        <h2 class="text-base font-bold text-gray-900 uppercase tracking-wide">
            Perbarui Kata Sandi Akun
        </h2>

        <p class="mt-1.5 text-xs text-gray-400 font-medium leading-relaxed max-w-2xl">
            Pastikan akun akses Anda tetap aman dengan menggunakan kombinasi kata sandi yang panjang, unik, dan acak untuk mencegah tindakan penyalahgunaan hak akses data.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5 max-w-xl">
        @csrf
        @method('put')

        {{-- Input Kata Sandi Saat Ini --}}
        <div>
            <label for="update_password_current_password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Kata Sandi Saat Ini <span class="text-red-500">*</span>
            </label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                   autocomplete="current-password"
                   placeholder="Masukkan kata sandi lama Anda">
            
            @if($errors->updatePassword->has('current_password'))
                <p class="mt-1.5 text-xs text-red-600 font-semibold">
                    {{ $errors->updatePassword->first('current_password') }}
                </p>
            @endif
        </div>

        {{-- Input Kata Sandi Baru --}}
        <div>
            <label for="update_password_password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Kata Sandi Baru <span class="text-red-500">*</span>
            </label>
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                   autocomplete="new-password"
                   placeholder="Masukkan kata sandi baru Anda">
            
            @if($errors->updatePassword->has('password'))
                <p class="mt-1.5 text-xs text-red-600 font-semibold">
                    {{ $errors->updatePassword->first('password') }}
                </p>
            @endif
        </div>

        {{-- Input Konfirmasi Kata Sandi Baru --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Ulangi Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
            </label>
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                   autocomplete="new-password"
                   placeholder="Ulangi kata sandi baru di atas">
            
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1.5 text-xs text-red-600 font-semibold">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </p>
            @endif
        </div>

        {{-- Kelompok Aksi Simpan & Notifikasi Status Sesi --}}
        <div class="flex items-center gap-4 pt-2 font-bold text-xs uppercase tracking-wide">
            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition shadow-sm">
                Simpan Perubahan
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-bold text-green-600 bg-green-50 border border-green-100 px-3 py-1.5 rounded-lg normal-case shadow-sm"
                >
                    ✓ Perubahan kata sandi berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>