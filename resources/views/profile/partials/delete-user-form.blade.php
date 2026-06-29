<section class="space-y-6">
    <header class="border-b border-gray-100 pb-4">
        <h2 class="text-base font-bold text-red-600 uppercase tracking-wide">
            Hapus Permanen Akun Pengguna
        </h2>

        <p class="mt-1.5 text-xs text-gray-400 font-medium leading-relaxed max-w-2xl">
            Setelah akun Anda dihapus, seluruh data profil, riwayat kehadiran bimbingan teknis, lampiran dokumen persyaratan, serta rekaman nilai tugas akan dimusnahkan secara permanen dari pangkalan data DIPA BBPMP Sumatera Barat. Sebelum melanjutkan, pastikan Anda telah mengunduh semua berkas atau informasi penting yang sekiranya masih diperlukan.
        </p>
    </header>

    {{-- Tombol Pemicu Jendela Konfirmasi Modal --}}
    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center px-4 py-2.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm"
    >
        Hapus Akun Saya
    </button>

    {{-- Komponen Jendela Dialog Konfirmasi (Modal) --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 bg-white rounded-2xl">
            @csrf
            @method('delete')

            <div class="text-center sm:text-left">
                <h2 class="text-lg font-bold text-gray-900 mb-1.5">
                    Apakah Anda benar-benar yakin ingin menghapus akun?
                </h2>

                <p class="text-xs text-gray-400 font-medium leading-relaxed">
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
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm"
                    placeholder="Masukkan kata sandi aktif Anda untuk konfirmasi"
                    required
                />

                @if($errors->userDeletion->has('password'))
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">
                        {{ $errors->userDeletion->first('password') }}
                    </p>
                @endif
            </div>

            {{-- Kelompok Tombol Kendali Modal --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-2 font-bold text-xs uppercase tracking-wide border-t border-gray-50 pt-4">
                <button 
                    type="button" 
                    x-on:click="$dispatch('close')"
                    class="w-full sm:w-auto text-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition"
                >
                    Batalkan
                </button>

                <button 
                    type="submit"
                    class="w-full sm:w-auto inline-flex justify-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition shadow-sm shadow-red-50"
                >
                    Ya, Hapus Permanen
                </button>
            </div>
        </form>
    </x-modal>
</section>