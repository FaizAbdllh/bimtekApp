@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl">
        {{-- REFAKTORISASI: Menambahkan defensive fallback judul perencanaan jika judul_final kosong --}}
        <h2 class="text-xl font-bold text-gray-900 mb-2">Registrasi Mandiri Peserta Eksternal</h2>
        <p class="text-sm text-gray-500 mb-6 font-medium">Kegiatan: <span class="text-primary-600 font-semibold">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-sm font-semibold shadow-sm mb-5">
                {{ session('success') }}
            </div>
        @endif

        {{-- REFAKTORISASI: Menggunakan properti id eksplisit untuk menjamin keamanan routing UUID --}}
        <form action="{{ route('bimtek.daftar.register', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @if(request()->query('code'))
                <input type="hidden" name="invite_code" value="{{ request()->query('code') }}">
            @endif

            {{-- Input Nama --}}
            <div>
                <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Lengkap beserta Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5" 
                    placeholder="Contoh: Dr. Salma, M.Pd." required>
                @error('name') 
                    <div class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</div> 
                @enderror
            </div>

            {{-- Input Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Alamat Email Aktif <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5" 
                    placeholder="nama@instansi.sch.id" required>
                @error('email') 
                    <div class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</div> 
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Input NIP --}}
                <div>
                    <label for="nip" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">NIP / NIK (Opsional)</label>
                    <input type="text" name="nip" id="nip" value="{{ old('nip') }}" 
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5"
                        placeholder="1982xxxxxxxxxxxxxx">
                    @error('nip') 
                        <div class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- Input Asal Instansi --}}
                <div>
                    <label for="asal_instansi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Asal Instansi Sekolah / Lembaga</label>
                    <input type="text" name="asal_instansi" id="asal_instansi" value="{{ old('asal_instansi') }}" 
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5"
                        placeholder="Contoh: SMAN 1 Padang">
                    @error('asal_instansi') 
                        <div class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</div> 
                    @enderror
                </div>
            </div>

            {{-- Blok Unggah Berkas Persyaratan Kelulusan (Muncul jika dikonfigurasi panitia) --}}
            @if($bimtek->butuh_verifikasi_dokumen)
                <div class="bg-gray-50 border border-gray-100 p-5 rounded-2xl shadow-inner shadow-gray-50">
                    <p class="font-bold text-xs uppercase tracking-wider text-gray-800 mb-1">Lampiran Berkas Mandatori Dokumen Persyaratan</p>
                    <p class="text-[11px] text-gray-400 font-medium mb-4">Mohon melampirkan berkas digital resmi dalam ekstensi format PDF, JPG, atau PNG (Ukuran Max 2MB).</p>
                    
                    <div class="space-y-4">
                        @foreach($jenisDokumenWajib as $jenis)
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    Unggah {{ $jenis === 'sppd' ? 'SPPD (Surat Perintah Perjalanan Dinas)' : ucwords(str_replace('_', ' ', $jenis)) }} <span class="text-red-500">*</span>
                                </label>
                                
                                {{-- REFAKTORISASI STYLING INPUT FILE: Penyelarasan style input berkas --}}
                                <input type="file" name="dokumen[{{ $jenis }}]" required
                                    class="block w-full border border-gray-300 rounded-xl p-2 bg-white text-xs font-semibold text-gray-500 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500">
                                
                                @error('dokumen.'.$jenis) 
                                    <div class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</div> 
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($bimtek->invite_code)
                <div class="p-3 bg-amber-50 border border-amber-100 text-amber-800 text-xs font-medium rounded-xl leading-relaxed">
                    <strong>Pemberitahuan Sistem:</strong> Registrasi kelas ini menggunakan kode token undangan resmi kedinasan BBPMP Provinsi Sumatera Barat.
                </div>
            @endif

            {{-- Form Actions Buttons --}}
            <div class="flex items-center gap-3 pt-3 border-t border-gray-50">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-primary-600 text-white font-bold text-xs uppercase tracking-wide rounded-xl hover:bg-primary-700 transition shadow-sm">
                    Kirim Pendaftaran
                </button>
                <a href="{{ url()->previous() }}" class="text-xs font-bold text-gray-500 hover:text-gray-800 transition">Batal / Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection