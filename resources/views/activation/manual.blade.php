@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        {{-- Flash Notification Error Alert --}}
        @if(session('error'))
            <div class="bg-red-50 border border-red-100 text-red-700 p-4 rounded-xl text-xs font-semibold mb-5 flex items-start gap-2 shadow-sm">
                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ======================================================== --}}
        {{-- TAHAP 2: FORM INPUT PASSWORD (AKUN TERVERIFIKASI)       --}}
        {{-- ======================================================== --}}
        @if(session('token_verified') || ($errors->has('password') && old('token')) || (!empty($email) && !empty($token)))
            <div class="text-center mb-6">
                {{-- Ikon Sukses Valid --}}
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-green-100">
                    ✓
                </div>
                <h2 class="text-xl font-bold text-green-600 mb-1">Token Terverifikasi Valid</h2>
                <p class="text-xs text-gray-400 font-medium leading-relaxed">
                    Sistem mendeteksi otorisasi sah. Silakan tetapkan kata sandi baru untuk mengaktifkan akun pendaftaran Anda.
                </p>
            </div>

            <form action="{{ route('activation.set-password') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Kumpulan Data Hidden State --}}
                <input type="hidden" name="email" value="{{ old('email', session('verified_email', $email ?? '')) }}">
                <input type="hidden" name="token" value="{{ old('token', session('verified_token', $token ?? '')) }}">

                {{-- Tampilan Email Disabled Viewer --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1.5">Alamat Email Pendaftar</label>
                    <input type="text" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-500 cursor-not-allowed shadow-inner" value="{{ old('email', session('verified_email', $email ?? '')) }}" disabled>
                </div>

                {{-- Input Password Baru --}}
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Buat Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required placeholder="Gunakan minimal 8 karakter gabungan">
                    @error('password') 
                        <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Input Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Ulangi Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required placeholder="Ulangi kembali password di atas">
                </div>

                {{-- Button Kirim Set Password --}}
                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm shadow-green-50">
                        Simpan Password & Masuk Aplikasi
                    </button>
                </div>
            </form>

        {{-- ======================================================== --}}
        {{-- TAHAP 1: FORM INPUT TOKEN AWAL (AKSES MANUAL)          --}}
        {{-- ======================================================== --}}
        @html
        @else
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-1">Aktivasi Akun Mandiri</h2>
                <p class="text-xs text-gray-400 font-medium leading-relaxed">
                    Silakan masukkan email kedinasan terdaftar dan kode token rahasia dari pihak panitia Pokja BBPMP Sumatera Barat.
                </p>
            </div>

            <form action="{{ route('activation.verify') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Input Email Pendaftaran --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Alamat Email Pendaftaran <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required placeholder="Contoh: nama@instansi.sch.id">
                    @error('email') 
                        <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Input Kode Token --}}
                <div>
                    <label for="token" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Kode Token Aktivasi <span class="text-red-500">*</span></label>
                    <input type="text" name="token" id="token" value="{{ old('token') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm uppercase text-center tracking-widest font-black text-gray-800" required placeholder="KODE-TOKEN-X7Y9">
                    @error('token') 
                        <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Button Kirim Check Validasi Token --}}
                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
                        Cek Validasi Token
                    </button>
                </div>
            </form>
        @endif

    </div>
</div>
@endsection