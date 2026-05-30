@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-2xl mx-auto bg-white shadow p-6 rounded">
        <h2 class="text-xl font-semibold mb-4">Daftar Peserta: {{ $bimtek->judul }}</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <form action="{{ route('bimtek.daftar.register', $bimtek) }}" method="post" enctype="multipart/form-data">
            @csrf

            @if(request()->query('code'))
                <input type="hidden" name="invite_code" value="{{ request()->query('code') }}">
            @endif

            <div class="mb-3">
                <label class="block text-sm font-medium">Nama</label>
                <input name="name" value="{{ old('name') }}" class="mt-1 block w-full border rounded p-2" required>
                @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Email (opsional)</label>
                <input name="email" value="{{ old('email') }}" class="mt-1 block w-full border rounded p-2">
                @error('email') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">NIP (opsional)</label>
                <input name="nip" value="{{ old('nip') }}" class="mt-1 block w-full border rounded p-2">
                @error('nip') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Asal Instansi (opsional)</label>
                <input name="asal_instansi" value="{{ old('asal_instansi') }}" class="mt-1 block w-full border rounded p-2">
                @error('asal_instansi') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            @if($bimtek->butuh_verifikasi_dokumen)
                <div class="mb-4">
                    <p class="font-medium">Unggah dokumen yang diminta:</p>
                    @foreach($jenisDokumenWajib as $jenis)
                        <div class="mt-2">
                            <label class="block text-sm">{{ ucwords(str_replace('_', ' ', $jenis)) }}</label>
                            <input type="file" name="dokumen[{{ $jenis }}]" class="mt-1 block w-full" required>
                            @error('dokumen.'.$jenis) <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                        </div>
                    @endforeach
                </div>
            @endif

            @if($bimtek->invite_code)
                <div class="mt-3 text-sm text-gray-600">
                    Pendaftaran untuk acara ini memerlukan kode undangan. Jika Anda menerima link dari instansi, buka link tersebut atau masukkan kode pada field yang diberikan pada link.
                </div>
            @endif

            <div class="flex items-center gap-3">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Daftar dan Upload</button>
                <a href="{{ url()->previous() }}" class="text-sm text-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
