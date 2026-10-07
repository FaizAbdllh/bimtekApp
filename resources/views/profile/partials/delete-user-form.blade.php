<section class="space-y-6">
    <header class="border-b border-gray-200 pb-4">
        <h2 class="text-lg font-bold text-gray-800">
            Hapus Permanen Akun Pengguna
        </h2>

        <p class="mt-1 text-sm text-gray-500 leading-relaxed max-w-2xl">
            Setelah akun Anda dihapus, seluruh data profil, riwayat kehadiran bimbingan teknis, lampiran dokumen persyaratan, serta rekaman nilai tugas akan dimusnahkan secara permanen dari pangkalan data DIPA BBPMP Sumatera Barat. Sebelum melanjutkan, pastikan Anda telah mengunduh semua berkas atau informasi penting yang sekiranya masih diperlukan.
        </p>
    </header>

    {{-- Tombol Pemicu Jendela Konfirmasi Modal --}}
    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
    >
        Hapus Akun Saya
    </button>

    {{-- Komponen Jendela Dialog Konfirmasi (Modal) --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white rounded-xl">
            @csrf
            @method('delete')

            <div class="text-center sm:text-left">
                <h2 class="text-lg font-bold text-gray-800">
                    Apakah Anda benar-benar yakin ingin menghapus akun?
                </h2>

                <p class="mt-1 text-sm text-gray-500 leading-relaxed">
                    Tindakan ini bersifat destruktif dan tidak dapat dibatalkan (*irreversible*). Demi keamanan validasi kedaerahan, silakan masukkan kata sandi aktif Anda untuk mengonfirmasi bahwa Anda adalah pemilik sah dari akun yang akan dihapus ini.
                </p>
            </div>

            {{-- Input Konfirmasi Kata Sandi --}}
            <div class="mt-5 max-w-md">
                <label for="password" class="sr-only">Kata Sandi</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @if($errors->userDeletion->has('password')) border-red-500 @endif"
                    placeholder="Masukkan kata sandi aktif Anda untuk konfirmasi"
                    required
                />

                @if($errors->userDeletion->has('password'))
                    <p class="mt-1 text-sm text-red-600">
                        {{ $errors->userDeletion->first('password') }}
                    </p>
                @endif
            </div>

            {{-- Kelompok Tombol Kendali Modal --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-gray-200 pt-4">
                <button 
                    type="button" 
                    x-on:click="$dispatch('close')"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                >
                    Batalkan
                </button>

                <button 
                    type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    Ya, Hapus Permanen
                </button>
            </div>
        </form>
    </x-modal>
</section>