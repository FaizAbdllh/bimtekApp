@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        <div class="text-center mb-6">
            {{-- Ikon Pengaman --}}
            <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-primary-100 shadow-sm shadow-primary-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Set Password Baru</h2>
            <p class="text-xs text-gray-400 font-medium truncate">Email Terdaftar: <span class="text-gray-700 font-bold">{{ $email }}</span></p>
        </div>

        <form action="{{ route('activation.set-password') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Input NIP (Opsional) --}}
            <div>
                <label for="nip" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                    Nomor Induk Pegawai / NIP <span class="text-gray-400 font-medium">(Opsional)</span>
                </label>
                <input type="text" name="nip" id="nip" value="{{ old('nip') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Contoh: 1995xxxxxxxxxxxxxx">
                @error('nip') 
                    <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Input Password Baru --}}
            <div>
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                    Kata Sandi Akun Baru <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                       placeholder="Minimal gunakan 8 karakter"
                       required>
                @error('password') 
                    <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Input Konfirmasi Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                    Ulangi Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                       placeholder="Ulangi kata sandi baru"
                       required>
            </div>

            {{-- Tombol Pengendali Aksi --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50 font-bold text-xs uppercase tracking-wide">
                <a href="{{ url('/') }}" class="px-4 py-2.5 text-gray-500 hover:text-gray-800 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition shadow-sm">
                    Simpan & Masuk Aplikasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection