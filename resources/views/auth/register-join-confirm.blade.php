<x-guest-layout>
    <div class="max-w-md mx-auto bg-white overflow-hidden shadow-sm border border-gray-100 p-6 rounded-xl">
        <h2 class="text-xl font-bold text-gray-800">
            Gabung ke Bimtek
        </h2>

        <p class="mt-3 text-sm text-gray-700">
            Anda sedang login sebagai
            <strong class="font-semibold">{{ auth()->user()->email }}</strong>.
        </p>

        <p class="mt-3 text-sm text-gray-700">
            Klik tombol berikut untuk mendaftarkan akun Anda sebagai peserta:
        </p>

        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm font-medium text-gray-900">
            {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
        </div>

        <form method="POST" action="{{ route('register.join') }}" class="mt-6">
            @csrf
            <input type="hidden" name="invite_code" value="{{ $inviteCode }}">

            <button type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 hover:bg-primary-800 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Gabung dan Lanjutkan
            </button>
        </form>

        <a href="{{ route('dashboard') }}"
           class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
            Batal
        </a>
    </div>
</x-guest-layout>