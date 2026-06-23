@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-md mx-auto bg-white shadow p-6 rounded">
        <h2 class="text-xl font-semibold mb-4">Set Password Baru</h2>
        <p class="text-sm text-gray-600 mb-4">Email: {{ $email }}</p>

        <form action="{{ route('activation.set-password') }}" method="post">
            @csrf

            <div class="mb-3">
                <label class="block text-sm">NIP (opsional)</label>
                <input name="nip" value="{{ old('nip') }}" class="mt-1 block w-full border rounded p-2">
                @error('nip') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm">Password Baru</label>
                <input type="password" name="password" class="mt-1 block w-full border rounded p-2" required>
                @error('password') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded p-2" required>
            </div>

            <div class="flex items-center gap-3">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan & Masuk</button>
                <a href="{{ url('/') }}" class="text-sm text-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection