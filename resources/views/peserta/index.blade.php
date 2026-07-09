<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-800 font-bold">Kelola Peserta</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Manajemen Anggota & Panitia Kelas
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Page Header dengan Deretan Tombol Utama --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm w-fit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Detail
                    </a>
                    <div class="mt-3 text-sm">
                        <p class="text-gray-900 font-semibold">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $bimtek->peserta->count() }} Anggota Terdaftar Resmi</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 font-bold text-xs uppercase tracking-wide">
                    @if($canManage)
                        <a href="{{ route('bimtek.peserta.export', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Ekspor CSV
                        </a>
                        <button type="button" 
                            x-data 
                            @click="$dispatch('open-modal', 'import-peserta')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Impor CSV
                        </button>
                        <button type="button" 
                            x-data 
                            @click="$dispatch('open-modal', 'tambah-peserta')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Manual
                        </button>
                    @endif
                </div>
            </div>

            {{-- Banner Notifikasi Hasil Unggah Massal (Bulk Import Success State) --}}
            @if(session('new_users') && count(session('new_users')) > 0)
                <div class="mb-6 p-5 bg-blue-50/60 border border-blue-100 text-blue-800 rounded-2xl shadow-sm text-xs space-y-3">
                    <p class="font-bold text-blue-900 uppercase tracking-wide text-[11px]">Daftar Kredensial Akun Baru Hasil Impor Massal:</p>
                    <div class="overflow-x-auto rounded-xl border border-blue-100/60 bg-white">
                        <table class="w-full text-left">
                            <thead class="bg-blue-50 text-blue-900 font-bold border-b border-blue-100/60">
                                <tr>
                                    <th class="px-4 py-2">Nama Lengkap</th>
                                    <th class="px-4 py-2">Alamat Email</th>
                                    <th class="px-4 py-2 w-32">Kunci Sandi Awal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-blue-50 font-medium text-gray-700">
                                @foreach(session('new_users') as $newUser)
                                    <tr>
                                        <td class="px-4 py-2 font-bold text-gray-900">{{ $newUser['name'] }}</td>
                                        <td class="px-4 py-2">{{ $newUser['email'] }}</td>
                                        <td class="px-4 py-2"><code class="bg-blue-50 border border-blue-100 px-2 py-0.5 rounded font-mono font-bold text-blue-800 text-[11px]">{{ $newUser['password'] }}</code></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[10px] text-primary-600 font-semibold italic">* Mohon segera menyalin dan mendistribusikan data kata sandi awal di atas kepada masing-masing peserta.</p>
                </div>
            @endif

            {{-- Banner Notifikasi Sukses Pembuatan Single User Manual --}}
            @if(session('new_user_password'))
                <div class="mb-6 p-4 bg-blue-50/60 border border-blue-100 text-blue-800 rounded-2xl shadow-sm text-xs font-semibold">
                    <p class="flex items-center gap-1.5 leading-relaxed">
                        <span>✓ Akun peserta baru berhasil ditambahkan. Kode akses aktivasi awal:</span>
                        <code class="bg-blue-100/80 border border-blue-200 px-2 py-0.5 rounded font-mono text-blue-900 font-bold text-sm">{{ session('new_user_password') }}</code>
                    </p>
                    <p class="text-[10px] text-gray-400 mt-1 font-medium">* Simpan dan teruskan kode rahasia di atas agar peserta dapat merampungkan verifikasi login pertama mereka.</p>
                </div>
            @endif

            {{-- Banner Peringatan Error Log Impor Berkas --}}
            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 rounded-2xl shadow-sm text-xs">
                    <p class="font-bold text-red-900 uppercase tracking-wide text-[10px] mb-2">Beberapa Baris Data CSV Gagal Diimpor:</p>
                    <ul class="list-disc list-inside space-y-1 font-semibold text-red-700">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Ringkasan Metrik Anggota Kelas --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Peserta</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $bimtek->peserta->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penanggung Jawab (PIC)</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $bimtek->pic ? 1 : 0 }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jajaran Panitia</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $bimtek->panitia->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menunggu Aktivasi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $pesertaPendingCount }}</p>
                </div>
            </div>

            {{-- DATATABLE CARD INDEKS PESERTA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="pesertaTable()">
                <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Daftar Anggota Kelas Terdaftar</h3>
                        </div>
                        <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide">
                            {{-- Input Bar Pencarian Real-Time --}}
                            <div class="relative">
                                <input type="text" 
                                       x-model="search" 
                                       placeholder="Cari nama / email / NIP..."
                                       class="w-full sm:w-64 pl-9 pr-4 py-1.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-xs font-semibold shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            {{-- Tombol Salin Akses URL Pendaftaran --}}
                            <button type="button" 
                                    onclick="navigator.clipboard.writeText('{{ url('/login') }}'); alert('Tautan halaman login & gerbang aktivasi mandiri berhasil disalin ke clipboard!');"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-100 transition shadow-sm"
                                    title="Salin tautan pintu masuk untuk dibagikan ke grup koordinasi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 00-2 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 4h5m-5 4h5m-3 4h3"/>
                                </svg>
                                Tautan Login
                            </button>

                            @if($canManage)
                                {{-- Aksi Massal (Bulk Actions Controls) --}}
                                <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 font-bold normal-case" x-text="selectedIds.length + ' Baris Terpilih'"></span>
                                    <button type="button" 
                                            @click="bulkDelete()"
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 border border-red-200 text-red-700 rounded-xl hover:bg-red-100 transition shadow-sm shadow-red-50">
                                        Hapus Massal
                                    </button>
                                </div>
                                <div class="ml-1 shadow-sm rounded-xl overflow-hidden">
                                    {{-- 💡 PERBAIKAN 1: Logika perhitungan dipindah langsung membaca dari tabel users --}}
                                    @php
                                        $allPeserta = $bimtek->peserta;
                                        $aktifUserIds = $allPeserta->where('is_active', true)->pluck('id')->toArray();
                                        $pendingPesertaIds = $allPeserta->where('is_active', false)->pluck('id')->toArray();
                                    @endphp

                                    <button type="button" 
                                            :disabled="selectedIds.length === 0"
                                            @click="
                                                const checkedBoxes = Array.from(document.querySelectorAll('.peserta-checkbox:checked'));
                                                const activeCount = checkedBoxes.filter(cb => cb.getAttribute('data-aktif') === 'true').length;

                                                if (checkedBoxes.length > 0 && activeCount === checkedBoxes.length) {
                                                    alert('Aksi Ditolak Sistem!\n\nSeluruh akun peserta yang Anda centang sudah memiliki status Aktif.');
                                                    return;
                                                }

                                                if (activeCount > 0) {
                                                    if (!confirm('Peringatan Otoritas:\n\nBeberapa nama yang Anda centang sudah berstatus Aktif. Sistem hanya akan memproses ulang token untuk nama yang Belum Aktivasi saja. Lanjutkan?')) {
                                                        return;
                                                    }
                                                }
                                                $dispatch('open-modal', 'generate-tokens');
                                            "
                                            class="inline-flex items-center px-4 py-2 font-bold text-xs uppercase tracking-wide transition shadow-sm border"
                                            :class="selectedIds.length === 0 
                                                ? 'bg-gray-50 text-gray-400 cursor-not-allowed border-gray-200 shadow-none' 
                                                : 'bg-blue-600 text-white border-transparent hover:bg-blue-700'">
                                        Rilis Token Aktivasi
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tabel Render Indeks Data --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                            <tr>
                                @if($canManage)
                                    <th class="px-6 py-3 text-center w-12 sticky left-0 bg-gray-50 z-10 border-r border-gray-100">
                                        <input type="checkbox" 
                                               @change="toggleAll($event.target.checked)"
                                               :checked="allSelected"
                                               x-ref="selectAllCheckbox"
                                               {{ $bimtek->peserta->isEmpty() ? 'disabled' : '' }}
                                               class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 cursor-pointer shadow-sm">
                                    </th>
                                @endif
                                <th class="px-4 py-3 text-center w-14">No</th>
                                <th class="px-6 py-3 text-left pl-6">Nama</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-center w-40">NIP</th>
                                <th class="px-6 py-3 text-left pl-6">Asal Instansi</th>
                                <th class="px-4 py-3 text-center w-36">Status Akun</th>
                                <th class="px-4 py-3 text-center w-36">Status Berkas</th>
                                @if($canManage)
                                    <th class="px-6 py-3 text-right pr-6 w-24 border-l border-gray-100 bg-gray-50">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                            @forelse($bimtek->peserta->sortBy('name') as $index => $peserta)
                                {{-- 💡 PERBAIKAN 2: Data dibaca langsung dari objek $peserta bawaan database users --}}
                                @php
                                    $hasActiveStatus = $peserta->is_active;

                                    $bolehGenerateToken = true;
                                    if ($bimtek->butuh_verifikasi_dokumen) {
                                        $jenisWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];
                                        $jumlahWajib = is_array($jenisWajib) ? count($jenisWajib) : 2;

                                        $jumlahApproved = \App\Models\DokumenPersyaratanPeserta::where('user_id', $peserta->id)
                                            ->where('bimtek_id', $bimtek->id)
                                            ->where('status', 'Approved')
                                            ->count();

                                        $bolehGenerateToken = ($jumlahApproved >= $jumlahWajib);
                                    }
                                @endphp
                                
                                <tr class="hover:bg-gray-50/50 transition-colors" 
                                    x-show="!search || '{{ strtolower($peserta->name . ' ' . $peserta->email . ' ' . ($peserta->nip ?? '')) }}'.includes(search.toLowerCase())" x-cloak>
                                    
                                    @if($canManage)
                                        <td class="px-6 py-4 text-center align-middle sticky left-0 bg-white border-r border-gray-50 z-10">
                                            <input type="checkbox"
                                                   value="{{ $peserta->id }}"
                                                   data-boleh-token="{{ $bolehGenerateToken ? 'true' : 'false' }}"
                                                   data-aktif="{{ $hasActiveStatus ? 'true' : 'false' }}"
                                                   data-nama="{{ $peserta->name }}"
                                                   @change="toggleSelection(@js($peserta->id))"
                                                   :checked="selectedIds.includes(@js($peserta->id))"
                                                   class="peserta-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 cursor-pointer shadow-sm">
                                        </td>
                                    @endif
                                    
                                    <td class="px-4 py-4 text-center text-gray-400 font-medium align-middle">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 pl-6 whitespace-nowrap font-bold text-gray-900">
                                        {{ $peserta->name ?? '-' }}
                                        @if($peserta->no_hp)
                                            <span class="block text-[10px] text-gray-400 font-medium mt-0.5">{{ $peserta->no_hp }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">{{ $peserta->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-semibold text-gray-800">{{ $peserta->nip ?? '-' }}</td>
                                    <td class="px-6 py-4 pl-6 text-gray-600 font-semibold">{{ $peserta->asal_instansi ?? '-' }}</td>
                                    
                                    <td class="px-4 py-4 text-center whitespace-nowrap align-middle">
                                        @if($hasActiveStatus)
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">Aktif</span>
                                        @else
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">Belum Aktivasi</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 text-center whitespace-nowrap align-middle">
                                        @if($bimtek->butuh_verifikasi_dokumen)
                                            @if($bolehGenerateToken)
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">Sah Verified</span>
                                            @else
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">Koreksi Berkas</span>
                                            @endif
                                        @else
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-400 border border-gray-100">Bebas Syarat</span>
                                        @endif
                                    </td>

                                    @if($canManage)
                                        <td class="px-6 py-4 text-right pr-6 align-middle border-l border-gray-50 bg-gray-50/50 whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1">
                                                <form action="{{ route('bimtek.peserta.destroy', [$bimtek->id, $peserta->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-white hover:shadow-sm rounded-lg transition" 
                                                            title="Keluarkan Peserta"
                                                            onclick="return confirm('Apakah Anda yakin ingin mengeluarkan {{ $peserta->name }} dari daftar kepesertaan kelas bimtek?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                
                                                {{-- 💡 PERBAIKAN 3: Pembatalan token diarahkan langsung menggunakan model ID User hasil pembaruan Routing web.php --}}
                                                @if(!$peserta->is_active && $peserta->activation_token)
                                                    <form action="{{ route('activation-tokens.revoke', [$bimtek->id, $peserta->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-white hover:shadow-sm rounded-lg transition" title="Batalkan/Revoke Token Masa Berlaku" onclick="return confirm('Apakah Anda yakin ingin membatalkan token rilis untuk {{ $peserta->name }}?')">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canManage ? 9 : 7 }}" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <h3 class="text-base font-bold text-gray-900 mb-0.5">Lembar Anggota Masih Kosong</h3>
                                        <p class="text-xs text-gray-400 mb-4">Belum ada draf data nama peserta eksternal yang diinput ke dalam kelas penugasan ini.</p>
                                        @if($canManage)
                                            <div class="mt-4 flex flex-wrap items-center justify-center gap-2 font-bold text-xs uppercase tracking-wide">
                                                <button type="button" x-data @click="$dispatch('open-modal', 'tambah-peserta')" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
                                                    Input Manual Perdana
                                                </button>
                                                <form action="{{ route('bimtek.generate-invite', $bimtek->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                                                        Terbitkan Link Registrasi
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Hidden Form Pemicu Penghapusan Massal --}}
                @if($canManage)
                    <form id="bulk-delete-form" action="{{ route('bimtek.peserta.bulk-destroy', $bimtek->id) }}" method="POST" style="display: none;">
                        @csrf
                        <div id="bulk-delete-inputs"></div>
                    </form>
                @endif
            </div>

            {{-- FOOTER CARDS DISPLAY: Jajaran Struktur PIC & Jajaran Panitia Tim Kerja --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 text-sm">
                
                {{-- Panel Informasi Tunggal PIC --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penanggung Jawab Utama Kelas (PIC)</h3>
                    </div>
                    <div class="p-6">
                        @if($bimtek->pic)
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($bimtek->pic->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 block">{{ $bimtek->pic->name }}</p>
                                    <p class="text-xs text-gray-400 font-medium block mt-0.5">{{ $bimtek->pic->email }}</p>
                                </div>
                                <span class="ml-auto inline-flex px-2.5 py-0.5 bg-blue-100 text-blue-800 rounded-md text-[10px] font-bold uppercase border border-blue-200">PIC Utama</span>
                            </div>
                        @else
                            <p class="text-gray-400 text-xs font-medium text-center py-4">Belum ada delegasi nama koordinator utama untuk kelas ini.</p>
                        @endif
                    </div>
                </div>

                {{-- Panel Informasi List Anggota Jajaran Panitia --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Anggota Tim Pelaksana (Panitia Pokja)</h3>
                    </div>
                    <div class="p-6">
                        @forelse($bimtek->panitia as $panitia)
                            <div class="flex items-center gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
                                <div class="h-10 w-10 rounded-full bg-green-50 text-green-700 border border-green-100 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($panitia->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 block truncate">{{ $panitia->name }}</p>
                                    <p class="text-xs text-gray-400 font-medium block mt-0.5 truncate">{{ $panitia->email }}</p>
                                </div>
                                <span class="inline-flex px-2.5 py-0.5 bg-green-100 text-green-800 rounded-md text-[10px] font-bold uppercase border border-green-200 shrink-0">Panitia</span>
                                @if($canManage)
                                    <form action="{{ route('bimtek.peserta.change-role', [$bimtek->id, $panitia->id]) }}" method="POST" class="inline shrink-0">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="peran" value="peserta">
                                        <button type="submit" 
                                                class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-gray-100 rounded-lg transition" 
                                                title="Demote Hak Akses Menjadi Peserta Biasa"
                                                onclick="return confirm('Ubah struktur penugasan kedinasan {{ $panitia->name }} menjadi Anggota Peserta biasa?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-400 text-xs font-medium text-center py-4">Belum ada delegasi daftar staf operasional kedinasan untuk kelas ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL COMPONENT WINDOWS CONTROL AREA --}}

    {{-- Modal 1: Penerbitan Token Aktivasi Berjalan Massal --}}
    <x-modal name="generate-tokens" :show="false" maxWidth="md">
        {{-- 💡 PERBAIKAN 4: Action form diarahkan menggunakan rute terpusat baru --}}
        <form id="generate-tokens-form" action="{{ Route::has('activation-tokens.generate-batch') ? route('activation-tokens.generate-batch', $bimtek->id) : url('') }}" method="POST" class="p-6 bg-white">
            @csrf
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-50 pb-2 mb-3">Rilis Token Otorisasi Massal</h3>
            <p class="text-xs text-gray-500 leading-relaxed mb-4">Sistem akan membangkitkan kombinasi token aktivasi unik untuk baris nama peserta terpilih yang berstatus belum aktivasi saja.</p>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Masa Berlaku Token Kedaluwarsa (Hari)</label>
                <input type="number" name="days" min="1" max="365" class="w-32 px-3 py-1.5 border border-gray-300 rounded-xl font-semibold text-sm focus:ring-primary-500" placeholder="7">
            </div>
            <div id="generate-hidden-inputs"></div>
            <div class="flex justify-end gap-2 font-bold text-xs uppercase tracking-wide pt-3 border-t border-gray-50">
                <button type="button" x-data @click="$dispatch('close-modal', 'generate-tokens')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="submit" onclick="return submitGenerateBatch()" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-sm">Mulai Generate</button>
            </div>
        </form>
    </x-modal>

    @if($canManage)
        {{-- Modal 2: Formulir Penambahan Anggota Manual & Sinkronisasi User Data --}}
        <x-modal name="tambah-peserta" :show="false" maxWidth="lg">
            <div class="p-6 bg-white space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Tambahkan Kepesertaan Baru</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Lakukan pembentukan akun mentah baru atau pasangkan akun user platform yang sudah terdaftar.</p>
                </div>

                {{-- Jalur Formulir A: Daftarkan Entitas Akun Segar Baru --}}
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Opsi 1: Entri Akun Individu Baru</h4>
                    <form action="{{ route('bimtek.peserta.store-new', $bimtek->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm font-semibold text-gray-700">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Nama Anggota <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full rounded-xl border-gray-300 text-xs font-medium" placeholder="Nama lengkap & gelar">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required class="w-full rounded-xl border-gray-300 text-xs font-medium" placeholder="nama@instansi.sch.id">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Identitas NIP (Opsional)</label>
                                <input type="text" name="nip" class="w-full rounded-xl border-gray-300 text-xs font-medium" placeholder="1995xxxxxxxxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Asal Sekolah (Opsional)</label>
                                <input type="text" name="asal_instansi" class="w-full rounded-xl border-gray-300 text-xs font-medium" placeholder="Contoh: SMPN 2 Padang">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 font-bold text-xs uppercase tracking-wide">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">Simpan Akun</button>
                        </div>
                    </form>
                </div>

                {{-- Jalur Formulir B: Sinkronisasikan User yang Telah Eksis di Database --}}
                <div class="space-y-3 pt-6 border-t border-gray-200">
                    <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Opsi 2: Sinkronisasikan Anggota Berjalan</h4>
                    <form action="{{ route('bimtek.peserta.store', $bimtek->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            @if($availableUsers->isEmpty())
                                <p class="text-xs text-gray-400 font-medium py-4 text-center bg-gray-50 border border-dashed rounded-xl">Seluruh user master di database internal sistem sudah terdaftar di dalam kelas ini.</p>
                            @else
                                <div class="border border-gray-200 rounded-xl max-h-48 overflow-y-auto divide-y divide-gray-100 shadow-inner">
                                    @foreach($availableUsers as $user)
                                        <label class="flex items-center px-4 py-2.5 hover:bg-gray-50/50 cursor-pointer transition-colors">
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 cursor-pointer">
                                            <div class="ml-3 text-xs">
                                                <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                                <p class="text-[10px] text-gray-400 font-semibold">{{ $user->email }} &middot; Level: <span class="text-gray-500 font-bold uppercase">{{ $user->role?->nama_peran ?? 'Anggota Luar' }}</span></p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-end gap-2 font-bold text-xs uppercase tracking-wide">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm" @disabled($availableUsers->isEmpty())>Sinkronkan Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </x-modal>

        {{-- Modal 3: Impor Spreadsheet CSV Hasil Rekap Sekolah Luar --}}
        <x-modal name="import-peserta" :show="false" maxWidth="md">
            <form action="{{ route('bimtek.peserta.import', $bimtek->id) }}" method="POST" enctype="multipart/form-data" class="p-6 bg-white space-y-4">
                @csrf
                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-0.5">Impor Peserta Via Spreadsheet</h3>
                    <p class="text-xs text-gray-400 font-medium">Lakukan impor data keanggotaan kelas dalam format ekstensi berkas *.CSV murni.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Pilih Berkas Lampiran CSV</label>
                    <input type="file" name="file" accept=".csv,text/csv" required class="w-full rounded-xl border-gray-300 p-2 text-xs font-semibold bg-gray-50 cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <div class="mt-2 text-right">
                        <a href="{{ route('bimtek.peserta.download-template') }}" class="text-[11px] font-bold text-primary-600 hover:text-primary-800 hover:underline">
                            ↓ Unduh Template Struktur Baku CSV
                        </a>
                    </div>
                </div>
                <div class="flex justify-end gap-2 font-bold text-xs uppercase tracking-wide border-t border-gray-50 pt-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text white rounded-xl hover:bg-primary-700 transition shadow-sm">Mulai Impor</button>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- SCRIPT CONTROLLER DRIVER: Alpine.js Real-time Datatable Engine --}}
    <script>
        function pesertaTable() {
            return {
                search: '',
                selectedIds: [],
                allPesertaIds: @json($pendingPesertaIds),
                get allSelected() {
                    return this.allPesertaIds.length > 0 && this.selectedIds.length === this.allPesertaIds.length;
                },
                init() {
                    this.$watch('selectedIds', () => {
                        if (this.$refs.selectAllCheckbox) {
                            this.$refs.selectAllCheckbox.checked = this.allSelected;
                            this.$refs.selectAllCheckbox.indeterminate = 
                                this.selectedIds.length > 0 && this.selectedIds.length < this.allPesertaIds.length;
                        }
                    });
                },
                toggleSelection(id) {
                    const index = this.selectedIds.indexOf(id);
                    if (index > -1) {
                        this.selectedIds.splice(index, 1);
                    } else {
                        this.selectedIds.push(id);
                    }
                },
                toggleAll(checked) {
                    if (checked) {
                        this.selectedIds = this.allPesertaIds.slice();
                    } else {
                        this.selectedIds = [];
                    }
                },
                bulkDelete() {
                    if (this.selectedIds.length === 0) {
                        alert('Silakan memilih minimal 1 baris nama peserta dari daftar untuk dihapus.');
                        return;
                    }
                    
                    if (confirm('PERINGATAN OTORITAS:\n\nApakah Anda yakin ingin mengeluarkan secara massal ' + this.selectedIds.length + ' peserta yang Anda pilih dari kelas bimtek?')) {
                        const inputsContainer = document.getElementById('bulk-delete-inputs');
                        inputsContainer.innerHTML = '';
                        
                        this.selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'user_ids[]';
                            input.value = id;
                            inputsContainer.appendChild(input);
                        });
                        
                        document.getElementById('bulk-delete-form').submit();
                    }
                }
            }
        }
    </script>
    
    {{-- SCRIPT CONTROLLER DRIVER 2: Asynchronous Token Management Engine --}}
    <script>
        async function generateToken(btn) {
            const url = btn.dataset.url;
            btn.disabled = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                let data = {};
                try { data = await res.json(); } catch(e) {}
                if (res.ok) {
                    if (data.raw_token) {
                        try { await navigator.clipboard.writeText(data.raw_token); } catch(e) {}
                        const aktivasiUrl = `${window.location.origin}/aktivasi`;
                        const printHtml = `<!doctype html><html><head><meta charset="utf-8"><title>Token Aktivasi</title></head>
                        <body>
                            <h2>Instruksi Aktivasi Akun</h2>
                            <p>Aktivasi di: ${aktivasiUrl}</p>
                            <p>Token Anda: <strong>${data.raw_token}</strong></p>
                            <script>window.onload=function(){window.print();};<\/script>
                        </body></html>`;
                        const w = window.open('', '_blank');
                        if (w) {
                            w.document.write(printHtml);
                            w.document.close();
                            w.focus();
                        } else {
                            alert('Token aktivasi: ' + data.raw_token + '\n(Disalin ke clipboard)');
                        }
                    } else if (data.sent_email) {
                        alert('Token aktivasi platform berhasil didistribusikan langsung via email pendaftaran peserta.');
                    } else {
                        alert('Token aktivasi berhasil diregistrasi.');
                    }
                } else {
                    alert('Gagal menerbitkan token otonom: ' + (data.message || res.statusText));
                }
            } catch (err) {
                alert('Terjadi error jaringan internal saat melakukan generate token.');
            } finally {
                btn.disabled = false;
            }
        }

        function submitGenerateBatch() {
            const checkboxes = Array.from(document.querySelectorAll('.peserta-checkbox:checked'));
            const container = document.getElementById('generate-hidden-inputs');
            container.innerHTML = '';

            checkboxes.forEach(cb => {
                if (cb.getAttribute('data-aktif') === 'false') {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'peserta_ids[]';
                    input.value = cb.value;
                    container.appendChild(input);
                }
            });

            return true;
        }
    </script>
</x-app-layout>