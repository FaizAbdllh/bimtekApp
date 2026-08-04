<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-sm border border-gray-100 p-6 sm:p-8 rounded-2xl shadow-gray-50/50">
        
        {{-- Header Visual Registrasi --}}
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Registrasi Peserta Luar</h2>
            
            {{-- 💡 DINAMIS: Subtitle berubah menyesuaikan jenis syarat Bimtek --}}
            @if($bimtek->butuh_verifikasi_dokumen)
                <p class="text-xs text-primary-600 font-bold mt-1">
                    Pengajuan Kelas: <span class="text-gray-800">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span>
                </p>
            @else
                <p class="text-xs text-gray-400 font-medium mt-1">Daftar akun baru untuk mengikuti Bimbingan Teknis BBPMP Sumbar</p>
            @endif
        </div>

        {{-- Form Pendaftaran Mandiri --}}
        <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Hidden Input untuk passing kode undangan --}}
            <input type="hidden" name="invite_code" value="{{ $inviteCode }}">

            {{-- Input Nama Lengkap --}}
            <div>
                <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('name') border-red-500 @enderror" 
                       placeholder="Masukkan nama lengkap beserta gelar"
                       required 
                       autofocus 
                       autocomplete="name">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Alamat Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Alamat Email Aktif <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('email') border-red-500 @enderror" 
                       placeholder="contoh@email.com"
                       required 
                       autocomplete="username">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Identitas NIP --}}
            <div>
                <label for="nip" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Nomor Induk Pegawai (NIP) <span class="text-gray-400 text-[10px] normal-case font-medium">(Isi '-' jika non-ASN)</span>
                </label>
                <input type="text" 
                       name="nip" 
                       id="nip" 
                       value="{{ old('nip') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('nip') border-red-500 @enderror" 
                       placeholder="1995xxxxxxxxxxxxxx">
                @error('nip')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Asal Instansi / Sekolah --}}
            <div>
                <label for="asal_instansi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                    Asal Instansi Kedinasan / Sekolah <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="asal_instansi" 
                       id="asal_instansi" 
                       value="{{ old('asal_instansi') }}" 
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('asal_instansi') border-red-500 @enderror" 
                       placeholder="Contoh: SDN 01 Padang atau BAPPEDA Padang Pariaman"
                       required>
                @error('asal_instansi')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- =====================================================================
                 💡 PERBAIKAN UTAMA: Loop Berkas Menggunakan Relasi Tabel syarat_dokumens
                 ===================================================================== --}}
            @if($bimtek->butuh_verifikasi_dokumen && $bimtek->syaratDokumens->isNotEmpty())
                
                @foreach($bimtek->syaratDokumens as $syarat)
                    <div class="pt-2">
                        <label for="dokumen_{{ $syarat->id }}" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Upload {{ $syarat->nama_dokumen }} 
                            @if($syarat->is_wajib) <span class="text-red-500">*</span> @endif
                        </label>
                        
                        {{-- Atribut name terformat menggunakan array ID: dokumen[ID_SYARAT] --}}
                        <input type="file" 
                               name="dokumen[{{ $syarat->id }}]" 
                               id="dokumen_{{ $syarat->id }}" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm font-medium bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 shadow-sm @error('dokumen.'.$syarat->id) border-red-500 @enderror" 
                               {{ $syarat->is_wajib ? 'required' : '' }}>
                        
                        {{-- Menampilkan deskripsi panduan unggah berkas dari database --}}
                        @if($syarat->deskripsi_syarat)
                            <p class="mt-1 text-[11px] text-gray-400 font-medium leading-relaxed">{{ $syarat->deskripsi_syarat }}</p>
                        @endif

                        @error('dokumen.'.$syarat->id)
                            <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
                
                <p class="mt-3 text-[10px] text-gray-400 font-medium leading-relaxed italic">* Format berkas dokumen yang didukung: PDF, JPG, atau PNG dengan ukuran maksimal masing-masing 2MB.</p>

            @else
                
                {{-- Form Pembuatan Password Akun Langsung (Hanya jika Bimtek Bebas Berkas) --}}
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                        Kata Sandi Akun Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm @error('password') border-red-500 @enderror" 
                           placeholder="Gunakan minimal 8 karakter"
                           required>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm" 
                           placeholder="Ulangi kembali kata sandi Anda"
                           required>
                </div>

            @endif

            {{-- Tombol Kirim Pendaftaran --}}
            <div class="pt-4">
                <button type="submit" class="w-full inline-flex justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
                    {{ $bimtek->butuh_verifikasi_dokumen ? 'Ajukan Berkas & Daftar' : 'Daftar Akun Baru' }}
                </button>
            </div>
        </form>

        {{-- Footer Pindah Jalur ke Login --}}
        <div class="mt-6 text-center border-t border-gray-100 pt-5">
            <p class="text-xs text-gray-400 font-medium mb-2">Sudah memiliki akun pendaftaran?</p>
            <a href="{{ route('login') }}" class="text-xs font-bold uppercase tracking-wide text-primary-600 hover:text-primary-800 transition-colors">
                &larr; Kembali ke Login
            </a>
        </div>
        
    </div>
</x-guest-layout>