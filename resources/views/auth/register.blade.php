<x-guest-layout>
    <div class="max-w-md mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
        
        {{-- Header Visual Registrasi --}}
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Registrasi Peserta Luar</h2>
            
            @if($bimtek->butuh_verifikasi_dokumen)
                <p class="text-sm text-gray-500 mt-1">
                    Pengajuan Kelas: <span class="font-semibold text-gray-800">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span>
                </p>
            @else
                <p class="text-sm text-gray-500 mt-1">Daftar akun baru untuk mengikuti Bimbingan Teknis BBPMP Sumbar</p>
            @endif
        </div>

        {{-- Tangkap Pesan Error dari Session (State Machine / Sistem) --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-start">
                
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Tangkap Pesan Info (Misal: NIP sudah terdaftar) --}}
        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-lg flex items-start">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        {{-- Form Pendaftaran Mandiri --}}
        <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data" class="space-y-5">
        {{-- Form Pendaftaran Mandiri --}}
        <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Hidden Input untuk passing kode undangan --}}
            <input type="hidden" name="invite_code" value="{{ $inviteCode }}">

            {{-- Input Nama Lengkap --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('name') border-red-500 @enderror" placeholder="Masukkan nama lengkap beserta gelar" required autofocus>
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Input Alamat Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700">
                    Alamat Email Aktif <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('email') border-red-500 @enderror" placeholder="contoh@email.com" required>
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Input Identitas NIP --}}
            <div>
                <label for="nip" class="block text-sm font-semibold text-gray-700">
                    Nomor Induk Pegawai (NIP) <span class="text-xs font-normal text-gray-500">(Isi '-' jika non-ASN)</span>
                </label>
                <input type="text" name="nip" id="nip" value="{{ old('nip') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('nip') border-red-500 @enderror" placeholder="1995xxxxxxxxxxxxxx" required>
                @error('nip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Input Asal Instansi / Sekolah --}}
            <div>
                <label for="asal_instansi" class="block text-sm font-semibold text-gray-700">
                    Asal Instansi Kedinasan / Sekolah <span class="text-red-500">*</span>
                </label>
                <input type="text" name="asal_instansi" id="asal_instansi" value="{{ old('asal_instansi') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('asal_instansi') border-red-500 @enderror" placeholder="Contoh: SDN 01 Padang" required>
                @error('asal_instansi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Loop Berkas Menggunakan Relasi Tabel syarat_dokumens --}}
            @if($bimtek->butuh_verifikasi_dokumen && $bimtek->syaratDokumens->isNotEmpty())
                <div class="pt-5 border-t border-gray-200 space-y-5">
                    @foreach($bimtek->syaratDokumens as $syarat)
                        <div>
                            <label for="dokumen_{{ $syarat->id }}" class="block text-sm font-semibold text-gray-700">
                                Upload {{ $syarat->nama_dokumen }} 
                                @if($syarat->is_wajib) <span class="text-red-500">*</span> @endif
                            </label>
                            <input type="file" name="dokumen[{{ $syarat->id }}]" id="dokumen_{{ $syarat->id }}" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-800 hover:file:bg-primary-200 @error('dokumen.'.$syarat->id) border-red-500 @enderror" {{ $syarat->is_wajib ? 'required' : '' }}>
                            
                            @if($syarat->deskripsi_syarat)
                                <p class="mt-1 text-sm text-gray-500 leading-relaxed">{{ $syarat->deskripsi_syarat }}</p>
                            @endif
                            @error('dokumen.'.$syarat->id) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                    <p class="text-xs text-gray-500 leading-relaxed">* Format berkas dokumen yang didukung: PDF, JPG, atau PNG dengan ukuran maksimal masing-masing 5MB.</p>
                </div>
            @endif

            {{-- 💡 PERBAIKAN: Form Kata Sandi dikeluarkan agar selalu muncul --}}
            <div class="pt-5 border-t border-gray-200">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Buat Kata Sandi Akun <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('password') border-red-500 @enderror" placeholder="Minimal 8 karakter" required>
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">
                    Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Ulangi kembali kata sandi Anda" required>
            </div>

            {{-- Tombol Kirim Pendaftaran --}}
            <div class="pt-2">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    {{ $bimtek->butuh_verifikasi_dokumen ? 'Daftar & Ajukan Berkas' : 'Daftar Akun Baru' }}
                </button>
            </div>
        </form>

        {{-- Footer Pindah Jalur ke Login --}}
        <div class="mt-6 text-center border-t border-gray-200 pt-5">
            <p class="text-sm text-gray-500 mb-2">Sudah memiliki akun pendaftaran?</p>
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                &larr; Kembali ke Login
            </a>
        </div>
        
    </div>
</x-guest-layout>