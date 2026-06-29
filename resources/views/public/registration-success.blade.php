@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl text-center">
        {{-- Ikon Sukses Visual --}}
        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-green-100 shadow-sm shadow-green-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h2 class="text-xl font-bold text-green-600 mb-2">Registrasi Berhasil</h2>
        
        <p class="text-sm text-gray-700 mb-2 leading-relaxed font-medium">{{ $message }}</p>
        <p class="text-xs text-gray-400 font-medium mb-6">Email Terdaftar: <span class="font-bold text-gray-900">{{ $email }}</span></p>
        
        {{-- Kotak Alur Langkah Selanjutnya --}}
        <div class="bg-blue-50/60 border border-blue-100 text-blue-800 p-5 rounded-xl text-left text-xs space-y-2 mb-6 shadow-inner shadow-blue-50">
            <p class="font-bold text-blue-900 uppercase tracking-wide text-[11px]">Langkah Selanjutnya:</p>
            <p class="font-medium text-gray-700">Data pendaftaran administrasi Anda telah berhasil disimpan ke dalam sistem DIPA Bimtek.</p>
            
            <ul class="list-disc list-inside mt-2 space-y-2 font-medium text-gray-600">
                <li>
                    <span class="text-blue-900 font-bold">Verifikasi Panitia:</span> Tim Pokja BBPMP Sumbar akan melakukan pemeriksaan berkas dan keabsahan dokumen persyaratan digital yang telah Anda lampirkan.
                </li>
                <li>
                    <span class="text-blue-900 font-bold">Token Aktivasi Akun:</span> Jika data dinyatakan sah, Panitia akan menerbitkan token aktivasi yang didistribusikan melalui WhatsApp Group koordinasi atau secara fisik saat registrasi di lokasi kegiatan.
                </li>
            </ul>
        </div>

        {{-- Kembali ke Beranda --}}
        <div class="pt-4 border-t border-gray-50">
            <a href="{{ url('/') }}" class="inline-flex w-full justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-wide rounded-xl transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection