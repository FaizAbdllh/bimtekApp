@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-md mx-auto bg-white shadow p-6 rounded text-center">
        <h2 class="text-xl font-semibold mb-4 text-green-600">Registrasi Berhasil</h2>
        
        <p class="text-gray-700 mb-2">{{ $message }}</p>
        <p class="text-sm text-gray-600 mb-6">Email Terdaftar: <span class="font-medium text-gray-900">{{ $email }}</span></p>
        
        {{-- KOTAK INSTRUKSI UTAMA --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded text-left text-sm mb-6">
            <p class="font-semibold mb-1">Langkah Selanjutnya:</p>
            <p>Data pendaftaran Anda telah berhasil disimpan ke dalam sistem.</p>
            
            <ul class="list-disc list-inside mt-2 space-y-1 text-xs">
                <li>Panitia akan melakukan verifikasi data diri serta keabsahan berkas Surat Tugas Anda.</li>
                <li>Jika dinyatakan valid, Panitia akan menerbitkan dan membagikan <strong>Token Aktivasi Akun</strong> Anda (biasanya dibagikan via WhatsApp Group kegiatan atau saat registrasi fisik di lokasi).</li>
            </ul>
        </div>

        {{-- Mengarahkan kembali ke beranda, bukan langsung ke form aktivasi --}}
        <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
            &larr; Kembali ke Beranda
        </a>
    </div>
</div>
@endsection