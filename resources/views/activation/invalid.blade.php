@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-md mx-auto bg-white shadow p-6 rounded">
        <h2 class="text-xl font-semibold mb-4">Token Tidak Valid</h2>
        <p>Token aktivasi tidak valid atau sudah digunakan.</p>
        <a href="{{ url('/') }}" class="text-blue-600">Kembali</a>
    </div>
</div>
@endsection
