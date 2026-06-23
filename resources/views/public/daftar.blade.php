@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-2xl mx-auto bg-white shadow p-6 rounded">
        {{-- PERBAIKAN: Mengubah $bimtek->judul menjadi $bimtek->judul_final --}}
        <h2 class="text-xl font-semibold mb-4">Registrasi Peserta: {{ $bimtek->judul_final }}</h2>

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
                <label class="block text-sm font-medium">Email </label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border rounded p-2" required placeholder="Masukkan email aktif Anda">
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
                <div class="mb-4 bg-gray-50 border border-gray-200 p-4 rounded-lg">
                    <p class="font-semibold text-sm text-gray-800 mb-2">Unggah Dokumen Persyaratan:</p>
                    <p class="text-xs text-gray-500 mb-3">*Mohon unggah dokumen resmi dalam format PDF/Gambar sesuai ketentuan.</p>
                    
                    @foreach($jenisDokumenWajib as $jenis)
                        <div class="mt-3">
                            {{-- Mengubah nama berkas seperti 'surat_tugas' menjadi 'Surat Tugas' atau 'sppd' menjadi 'Sppd' (bisa disesuaikan di controller) --}}
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                {{ $jenis === 'sppd' ? 'SPPD (Surat Perintah Perjalanan Dinas)' : ucwords(str_replace('_', ' ', $jenis)) }}
                            </label>
                            
                            {{-- Penyelarasan style input file --}}
                            <input type="file" name="dokumen[{{ $jenis }}]" 
                                class="block w-full border border-gray-300 rounded-lg p-1.5 bg-white text-sm text-gray-600 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                required>
                            
                            @error('dokumen.'.$jenis) 
                                <div class="text-red-600 text-xs mt-1">{{ $message }}</div> 
                            @enderror
                        </div>
                    @endforeach
                </div>
            @endif

            @if($bimtek->invite_code)
                <div class="mt-3 text-sm text-gray-600 mb-4">
                    Pendaftaran untuk acara ini memerlukan kode undangan. Jika Anda menerima link dari instansi, buka link tersebut atau masukkan kode pada field yang diberikan pada link.
                </div>
            @endif

            <div class="flex items-center gap-3">
                {{-- PERBAIKAN: Mengubah teks tombol menjadi "Daftar" --}}
                <button class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">Daftar</button>
                <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection