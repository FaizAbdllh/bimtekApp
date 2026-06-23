@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-md mx-auto bg-white shadow p-6 rounded">
        
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4 text-sm">{{ session('error') }}</div>
        @endif

        {{-- ======================================================== --}}
        {{-- TAHAP 2: FORM INPUT PASSWORD --}}
        {{-- ======================================================== --}}
        {{-- PERBAIKAN: Form password langsung terbuka jika token terverifikasi lewat session, --}}
        {{-- terjadi eror validasi password, ATAU jika link membawa parameter email & token dari email panitia --}}
        @if(session('token_verified') || ($errors->has('password') && old('token')) || (!empty($email) && !empty($token)))
            <h2 class="text-xl font-semibold mb-2 text-center text-green-600">Token Terdeteksi</h2>
            <p class="text-sm text-gray-500 text-center mb-6">
                Silakan buat password baru untuk mengaktifkan akun Anda.
            </p>

            <form action="{{ route('activation.set-password') }}" method="post">
                @csrf
                {{-- PERBAIKAN: Menggabungkan data dari old(), session(), dan query parameter URL ($email / $token) --}}
                <input type="hidden" name="email" value="{{ old('email', session('verified_email', $email ?? '')) }}">
                <input type="hidden" name="token" value="{{ old('token', session('verified_token', $token ?? '')) }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Anda</label>
                    <input type="text" class="w-full bg-gray-100 border rounded p-2 text-sm text-gray-500 cursor-not-allowed" value="{{ old('email', session('verified_email', $email ?? '')) }}" disabled>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" class="w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 text-sm" required placeholder="Minimal 8 karakter">
                    @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 text-sm" required placeholder="Ulangi password baru">
                </div>

                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2.5 rounded-lg font-medium text-sm hover:bg-green-700 transition shadow">
                    Simpan Password & Masuk Aplikasi
                </button>
            </form>

        {{-- ======================================================== --}}
        {{-- TAHAP 1: FORM VERIFIKASI EMAIL & TOKEN (Akses Manual Tanpa Link) --}}
        {{-- ======================================================== --}}
        @else
            <h2 class="text-xl font-semibold mb-2 text-center">Aktivasi Akun Peserta</h2>
            <p class="text-sm text-gray-500 text-center mb-6">
                Masukkan email pendaftaran dan token dari panitia untuk memverifikasi akun Anda.
            </p>

            <form action="{{ route('activation.verify') }}" method="post">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Pendaftaran</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 text-sm" required placeholder="nama@email.com">
                    @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Token Aktivasi</label>
                    <input type="text" name="token" value="{{ old('token') }}" class="w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 font-mono text-sm uppercase text-center tracking-widest" required placeholder="X7Y9-K2M1">
                    @error('token') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg font-medium text-sm hover:bg-blue-700 transition shadow">
                    Cek Validasi Token
                </button>
            </form>
        @endif

    </div>
</div>
@endsection