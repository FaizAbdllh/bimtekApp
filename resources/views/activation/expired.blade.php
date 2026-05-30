@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-md mx-auto bg-white shadow p-6 rounded">
        <h2 class="text-xl font-semibold mb-4">Token Kadaluarsa</h2>
        <p>Token aktivasi sudah kadaluarsa. Silakan minta panitia untuk membuat token baru.</p>
        <a href="{{ url('/') }}" class="text-blue-600">Kembali</a>
    </div>
</div>
@endsection
