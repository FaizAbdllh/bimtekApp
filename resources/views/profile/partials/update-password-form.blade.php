<section class="space-y-6">
    <header class="border-b border-gray-200 pb-4">
        <h2 class="text-lg font-bold text-gray-800">
            Perbarui Kata Sandi Akun
        </h2>

        <p class="mt-1 text-sm text-gray-500 leading-relaxed max-w-2xl">
            Pastikan akun akses Anda tetap aman dengan menggunakan kombinasi kata sandi yang panjang, unik, dan acak untuk mencegah tindakan penyalahgunaan hak akses data.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5 max-w-xl">
        @csrf
        @method('put')

        {{-- Input Kata Sandi Saat Ini --}}
        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-gray-700">
                Kata Sandi Saat Ini <span class="text-red-500">*</span>
            </label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @if($errors->updatePassword->has('current_password')) border-red-500 @endif" 
                   autocomplete="current-password"
                   placeholder="Masukkan kata sandi lama Anda">
            
            @if($errors->updatePassword->has('current_password'))
                <p class="mt-1 text-sm text-red-600">
                    {{ $errors->updatePassword->first('current_password') }}
                </p>
            @endif
        </div>

        {{-- Input Kata Sandi Baru --}}
        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-gray-700">
                Kata Sandi Baru <span class="text-red-500">*</span>
            </label>
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @if($errors->updatePassword->has('password')) border-red-500 @endif" 
                   autocomplete="new-password"
                   placeholder="Masukkan kata sandi baru Anda">
            
            @if($errors->updatePassword->has('password'))
                <p class="mt-1 text-sm text-red-600">
                    {{ $errors->updatePassword->first('password') }}
                </p>
            @endif
        </div>

        {{-- Input Konfirmasi Kata Sandi Baru --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-700">
                Ulangi Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
            </label>
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @if($errors->updatePassword->has('password_confirmation')) border-red-500 @endif" 
                   autocomplete="new-password"
                   placeholder="Ulangi kata sandi baru di atas">
            
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1 text-sm text-red-600">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </p>
            @endif
        </div>

        {{-- Kelompok Aksi Simpan & Notifikasi Status Sesi --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Simpan Perubahan
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-800 bg-green-50 border border-green-100 px-3 py-2 rounded-lg"
                >
                    Perubahan kata sandi berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>