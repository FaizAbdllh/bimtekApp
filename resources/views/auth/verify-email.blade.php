<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        {{-- Header Visual Petunjuk Verifikasi Email --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-blue-100 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.68a2 2 0 012.22 0l8 5.68A2 2 0 0121 10.07V19a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l8 6 8-6"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Verifikasi Email Anda</h2>
            <p class="text-xs text-gray-400 font-medium leading-relaxed">
                Terima kasih telah melakukan registrasi akun! Sebelum memulai aktivitas di dalam sistem, mohon konfirmasikan alamat email Anda dengan mengklik tautan verifikasi yang baru saja kami kirimkan ke kotak masuk Anda.
            </p>
        </div>

        {{-- Banner Status Berhasil Mengirim Ulang Tautan Token (Session Status) --}}
        @if (session('status') == 'verification-link-sent')
            <div class="mb-5 p-4 bg-green-50 border border-green-100 text-green-800 rounded-xl text-xs font-semibold shadow-sm flex items-start gap-2">
                <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Tautan verifikasi baru yang segar telah berhasil dikirimkan kembali ke alamat email yang Anda daftarkan. Silakan periksa folder kotak masuk atau spam email Anda.</span>
            </div>
        @endif

        {{-- Baris Panel Form Aksi Interaktif --}}
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-50 pt-5 font-bold text-xs uppercase tracking-wide">
            
            {{-- Form 1: Kirim Ulang Email Verifikasi --}}
            <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition shadow-sm">
                    Kirim Ulang Link Verifikasi
                </button>
            </form>

            {{-- Form 2: Putus Sesi Keluar (Logout) --}}
            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center">
                @csrf
                <button type="submit" class="w-full sm:w-auto text-center px-4 py-2.5 text-gray-400 hover:text-gray-700 transition">
                    Keluar (Log Out)
                </button>
            </form>
            
        </div>
    </div>
</x-guest-layout>