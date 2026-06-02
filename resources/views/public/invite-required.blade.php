@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-md mx-auto bg-white shadow p-6 rounded">
        <h2 class="text-xl font-semibold mb-4">Kode Undangan Diperlukan</h2>
        <p class="text-gray-700 mb-4">
            Pendaftaran untuk bimtek ini memerlukan kode undangan dari panitia. Silakan buka link undangan yang dibagikan atau masukkan kode yang benar.
        </p>
        @if($bimtek->invite_code)
            <p class="text-sm text-gray-500 mb-4">Kode undangan sudah aktif untuk bimtek ini.</p>
        @endif
        <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-700">Kembali ke beranda</a>
    </div>
</div>
@endsection