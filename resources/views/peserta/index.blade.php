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
                
                {{-- Aksi Sisi Kanan Header: Tambah Manual & Ekspor CSV --}}
                <div class="flex flex-wrap items-center gap-2 font-bold text-xs uppercase tracking-wide">
                    @if($canManage)
                        
                        {{-- 1. TOMBOL DROPDOWN TAMBAH MANUAL (BARU) --}}
                        <div x-data="{ openDropdown: false }" class="relative">
                            <button @click="openDropdown = !openDropdown" @click.away="openDropdown = false" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-primary-600 rounded-xl hover:bg-gray-50 transition shadow-sm font-bold text-xs uppercase tracking-wide">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Manual
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Isi Dropdown --}}
                            <div x-show="openDropdown" x-transition.opacity x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden normal-case tracking-normal">
                                <button type="button" @click="$dispatch('open-modal', 'modal-tambah-eksisting'); openDropdown = false" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition font-medium">
                                    Pilih dari Database
                                </button>
                                <button type="button" @click="$dispatch('open-modal', 'modal-tambah-baru'); openDropdown = false" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition font-medium">
                                    Daftarkan Peserta Baru
                                </button>
                                <div class="border-t border-gray-100"></div>
                                <button type="button" @click="$dispatch('open-modal', 'modal-import-csv'); openDropdown = false" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition font-medium">
                                    Import File CSV
                                </button>
                            </div>
                        </div>

                        {{-- 2. TOMBOL EKSPOR CSV (Eksisting) --}}
                        <a href="{{ route('bimtek.peserta.export', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Ekspor Rekap CSV
                        </a>
                    @endif
                </div>
            </div>

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
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Belum Aktivasi Akun</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $bimtek->peserta->where('is_active', false)->count() }}</p>
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
                            
                            {{-- 💡 PERBAIKAN STRUKTUR: Menyalin Link Pendaftaran Mandiri Khusus Ber-code Kelas Ini --}}
                            @if($bimtek->invite_code)
                                <button type="button" 
                                        onclick="navigator.clipboard.writeText('{{ url('/register?code=' . $bimtek->invite_code) }}'); alert('Tautan Registrasi Mandiri Utusan Instansi berhasil disalin! Silakan sebarkan ke grup koordinator wilayah.');"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 text-primary-600 rounded-xl hover:bg-gray-100 transition shadow-sm"
                                        title="Salin tautan pintu registrasi mandiri untuk lampiran surat dinas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    Salin Link Undangan
                                </button>
                            @endif

                            @if($canManage)
                                {{-- Kontrol Aksi Hapus Massal --}}
                                <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 font-bold normal-case" x-text="selectedIds.length + ' Baris Terpilih'"></span>
                                    <button type="button" 
                                            @click="bulkDelete()"
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 border border-red-200 text-red-700 rounded-xl hover:bg-red-100 transition shadow-sm">
                                        Hapus Massal
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tabel Render Indeks Data --}}
                <div class="overflow-x-auto lg:overflow-x-visible">
                    <table class="w-full text-xs table-auto">
                        <thead class="bg-gray-50 text-gray-500 font-semibold uppercase tracking-wider border-b border-gray-100">
                            <tr>
                                @if($canManage)
                                    <th class="px-4 py-3 text-center w-10 sticky left-0 bg-gray-50 z-10 border-r border-gray-100">
                                        <input type="checkbox" 
                                            @change="toggleAll($event.target.checked)"
                                            :checked="allSelected"
                                            x-ref="selectAllCheckbox"
                                            {{ $bimtek->peserta->isEmpty() ? 'disabled' : '' }}
                                            class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 cursor-pointer shadow-sm">
                                    </th>
                                @endif
                                <th class="px-2 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3 text-left">Nama Peserta</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-3 py-3 text-center w-36">NIP</th>
                                <th class="px-4 py-3 text-left">Asal Instansi / Sekolah</th>
                                <th class="px-3 py-3 text-center w-28">Akses Akun</th>
                                <th class="px-3 py-3 text-center w-32">Status Verifikasi</th>
                                @if($canManage)
                                    <th class="px-4 py-3 text-right w-24 border-l border-gray-100 bg-gray-50">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                            @forelse($bimtek->peserta->sortBy('name') as $index => $peserta)
                                @php
                                    $assignment = DB::table('bimtek_pesertas')
                                        ->where('bimtek_id', $bimtek->id)
                                        ->where('user_id', $peserta->id)
                                        ->first();
                                        
                                    $statusVerifikasi = $assignment->status_verifikasi ?? 'pending';
                                @endphp
                                
                                <tr class="hover:bg-gray-50/50 transition-colors" 
                                    x-show="!search || '{{ strtolower($peserta->name . ' ' . $peserta->email . ' ' . ($peserta->nip ?? '')) }}'.includes(search.toLowerCase())" x-cloak>
                                    
                                    @if($canManage)
                                        <td class="px-4 py-3 text-center align-middle sticky left-0 bg-white border-r border-gray-50 z-10">
                                            <input type="checkbox"
                                                value="{{ $peserta->id }}"
                                                @change="toggleSelection(@js($peserta->id))"
                                                :checked="selectedIds.includes(@js($peserta->id))"
                                                class="peserta-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 cursor-pointer shadow-sm">
                                        </td>
                                    @endif
                                    
                                    <td class="px-2 py-3 text-center text-gray-400 font-medium align-middle">{{ $index + 1 }}</td>
                                    
                                    {{-- 💡 PERBAIKAN: Hapus whitespace-nowrap agar nama panjang otomatis membungkus ke bawah --}}
                                    <td class="px-4 py-3 align-middle font-bold text-gray-900 max-w-[180px] break-words">
                                        {{ $peserta->name ?? '-' }}
                                    </td>
                                    
                                    {{-- 💡 PERBAIKAN: Hapus whitespace-nowrap & gunakan break-all untuk email dinas yang panjang --}}
                                    <td class="px-4 py-3 align-middle text-gray-600 font-medium max-w-[160px] break-all">
                                        {{ $peserta->email }}
                                    </td>
                                    
                                    <td class="px-3 py-3 whitespace-nowrap text-center font-semibold text-gray-800 align-middle">{{ $peserta->nip ?? '-' }}</td>
                                    
                                    {{-- 💡 PERBAIKAN: Berikan batas max-width agar kolom instansi tidak melar merusak layout --}}
                                    <td class="px-4 py-3 text-gray-600 font-semibold align-middle max-w-[200px] break-words">{{ $peserta->asal_instansi ?? '-' }}</td>
                                    
                                    <td class="px-3 py-3 text-center whitespace-nowrap align-middle">
                                        @if($peserta->is_active)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">Aktif</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">Belum Aktivasi</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 text-center whitespace-nowrap align-middle">
                                        @if($bimtek->butuh_verifikasi_dokumen)
                                            @if($statusVerifikasi === 'verified')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">Sah Verified</span>
                                            @elseif($statusVerifikasi === 'rejected')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">Koreksi Berkas</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">Menunggu Review</span>
                                            @endif
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-400 border border-gray-100">Bebas Syarat</span>
                                        @endif
                                    </td>

                                    @if($canManage)
                                        <td class="px-4 py-3 text-right align-middle border-l border-gray-50 bg-gray-50/50 whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('bimtek.verifikasi-dokumen.index', $bimtek->id) }}" class="px-2.5 py-1 bg-white border border-gray-300 text-gray-700 rounded-lg text-[11px] font-bold shadow-sm hover:bg-gray-50 transition">
                                                    Periksa Berkas
                                                </a>

                                                <form action="{{ route('bimtek.peserta.destroy', [$bimtek->id, $peserta->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white hover:shadow-sm rounded-lg transition" 
                                                            onclick="return confirm('Apakah Anda yakin ingin mengeluarkan {{ $peserta->name }}?')">
                                                        <svg class="w-3.5 3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canManage ? 9 : 7 }}" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        <p class="text-xs text-gray-400">Belum ada peserta yang mendaftar.</p>
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
                            </div>
                        @empty
                            <p class="text-gray-400 text-xs font-medium text-center py-4">Belum ada delegasi daftar staf operasional kedinasan untuk kelas ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
        {{-- ================= KUMPULAN MODAL TAMBAH MANUAL ================= --}}
            @if($canManage)
                
                {{-- MODAL 1: TAMBAH EKSISTING --}}
                <div x-data="{ open: false }" @open-modal.window="if ($event.detail === 'modal-tambah-eksisting') open = true" x-cloak>
                    <div x-show="open" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            
                            <div x-show="open" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                                <form action="{{ route('bimtek.peserta.store', $bimtek->id) }}" method="POST">
                                    @csrf
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4" id="modal-title">Tambah Peserta dari Database</h3>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Pilih User</label>
                                            <select name="user_ids[]" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm h-48" multiple required>
                                                @foreach($availableUsers as $user)
                                                    <option value="{{ $user->id }}" class="py-1 border-b border-gray-50">{{ $user->name }} - {{ $user->email }}</option>
                                                @endforeach
                                            </select>
                                            <p class="mt-2 text-xs text-gray-500">Tips: Tahan tombol <b>Ctrl</b> (Windows) atau <b>Cmd</b> (Mac) di keyboard untuk memilih lebih dari satu nama sekaligus.</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-primary-600 hover:bg-gray-50 sm:ml-3 sm:w-auto transition uppercase tracking-wide">Tambahkan ke Kelas</button>
                                        <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL 2: DAFTARKAN PESERTA BARU --}}
                <div x-data="{ open: {{ $errors->has('name') || $errors->has('email') || $errors->has('nip') ? 'true' : 'false' }} }" @open-modal.window="if ($event.detail === 'modal-tambah-baru') open = true" x-cloak>
                    <div x-show="open" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            
                            <div x-show="open" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                                <form action="{{ route('bimtek.peserta.store-new', $bimtek->id) }}" method="POST">
                                    @csrf
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4">Daftarkan Akun Peserta Baru</h3>
                                        
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                                <input type="text" name="name" value="{{ old('name') }}" class="w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 text-sm" required placeholder="Cth: Ahmad Hidayat, M.Pd">
                                                @error('name') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                                <input type="email" name="email" value="{{ old('email') }}" class="w-full border {{ $errors->has('email') ? 'border-red-500 bg-red-50' : 'border-gray-300' }} rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 text-sm" required placeholder="Cth: ahmad@gmail.com">
                                                @error('email') <p class="text-red-600 text-xs mt-1.5 font-bold">Peringatan: {{ $message }} Silakan gunakan menu "Pilih dari Database" untuk akun ini.</p> @enderror
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP <span class="text-xs font-normal text-gray-400">(Opsional)</span></label>
                                                    <input type="text" name="nip" value="{{ old('nip') }}" class="w-full border {{ $errors->has('nip') ? 'border-red-500' : 'border-gray-300' }} rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 text-sm">
                                                    @error('nip') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-1">Instansi <span class="text-xs font-normal text-gray-400">(Opsional)</span></label>
                                                    <input type="text" name="asal_instansi" value="{{ old('asal_instansi') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 text-sm">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 p-3 bg-blue-50 rounded-xl border border-blue-100 flex gap-2 text-blue-800 text-xs font-medium">
                                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>Sistem akan otomatis membuatkan password secara acak dan langsung mengirimkannya ke email peserta.</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-primary-600 hover:bg-gray-50 sm:ml-3 sm:w-auto transition uppercase tracking-wide">Buat Akun & Daftarkan</button>
                                        <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL 3: IMPORT FILE CSV --}}
                <div x-data="{ open: false }" @open-modal.window="if ($event.detail === 'modal-import-csv') open = true" x-cloak>
                    <div x-show="open" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            
                            <div x-show="open" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                                <form action="{{ route('bimtek.peserta.import', $bimtek->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">Import Daftar Peserta (CSV)</h3>
                                        <p class="text-xs text-gray-500 mb-4">Pastikan file CSV Anda memiliki format kolom (*Header*): <b>Nama, Email, NIP, Instansi</b>.</p>
                                        
                                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative">
                                            <input type="file" name="file" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="mt-1 text-sm text-gray-600 font-bold">Klik untuk memilih file CSV</p>
                                            <p class="mt-1 text-xs text-gray-400">Maksimal ukuran: 2MB</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-primary-600 hover:bg-gray-50 sm:ml-3 sm:w-auto transition uppercase tracking-wide">Mulai Import</button>
                                        <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{-- ================= AKHIR KUMPULAN MODAL ================= --}}
    </div>

    {{-- SCRIPT CONTROLLER DRIVER: Alpine.js Real-time Datatable Engine --}}
    <script>
        function pesertaTable() {
            return {
                search: '',
                selectedIds: [],
                allPesertaIds: @json($bimtek->peserta->pluck('id')->toArray()),
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
</x-app-layout>