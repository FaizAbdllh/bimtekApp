@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl text-center">
        {{-- Ikon Peringatan Visual --}}
        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100 text-xl font-bold">
            !
        </div>
        
        <h2 class="text-xl font-bold text-gray-900 mb-1">Kode Undangan Diperlukan</h2>
        {{-- REFAKTORISASI: Menampilkan judul kegiatan agar peserta tahu konteks kelas yang dikunci --}}
        <p class="text-xs text-gray-400 font-medium mb-4">Kegiatan: <span class="text-gray-700 font-semibold">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>
        
        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
            Akses pendaftaran mandiri untuk bimbingan teknis ini dikunci dan memerlukan token atau kode undangan valid dari Panitia Pokja BBPMP Provinsi Sumatera Barat. Silakan gunakan tautan rujukan resmi atau hubungi panitia terkait.
        </p>

        @if($bimtek->invite_code)
            <div class="mb-6 p-3 bg-gray-50 border border-gray-100 rounded-xl text-xs text-gray-500 font-medium">
                Sistem mengonfirmasi pengaman gerbang kode undangan untuk kelas ini aktif.
            </div>
        @endif

        {{-- Akses Tombol Navigasi --}}
        <div class="pt-4 border-t border-gray-50">
            <a href="{{ url('/') }}" class="inline-flex w-full justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-wide rounded-xl transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection