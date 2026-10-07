<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">
            Manajemen Anggota & Panitia Kelas
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Baris Navigasi Atas --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        Kembali ke Detail
                    </a>
                </div>

                {{-- Aksi Sisi Kanan --}}
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Salin Link Pendaftaran Mandiri --}}
                    @if($bimtek->invite_code)
                        <button type="button"
                                onclick="navigator.clipboard.writeText('{{ url('/register?code=' . $bimtek->invite_code) }}'); alert('Tautan Registrasi Mandiri Utusan Instansi berhasil disalin! Silakan sebarkan ke grup koordinator wilayah.');"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100"
                                title="Salin tautan pintu registrasi mandiri untuk lampiran surat dinas">
                            Salin Link Undangan
                        </button>
                    @endif

                    @if($canManage)
                        {{-- Ekspor CSV --}}
                        <a href="{{ route('bimtek.peserta.export', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                            Ekspor Rekap CSV
                        </a>

                        {{-- Dropdown Tambah Manual --}}
                        <div x-data="{ openDropdown: false }" class="relative">
                            <button @click="openDropdown = !openDropdown" @click.away="openDropdown = false" type="button" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">
                                Tambah Manual
                            </button>

                            {{-- Isi Dropdown --}}
                            <div x-show="openDropdown" x-transition.opacity x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 z-50 overflow-hidden">
                                <button type="button" @click="$dispatch('open-modal', 'modal-tambah-eksisting'); openDropdown = false" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                                    Pilih dari Database
                                </button>
                                <button type="button" @click="$dispatch('open-modal', 'modal-tambah-baru'); openDropdown = false" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                                    Daftarkan Peserta Baru
                                </button>
                                <div class="border-t border-gray-100"></div>
                                <button type="button" @click="$dispatch('open-modal', 'modal-import-csv'); openDropdown = false" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                                    Import File CSV
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ringkasan Metrik Anggota Kelas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Peserta</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $bimtek->peserta->count() }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Penanggung Jawab (PIC)</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $bimtek->pic ? 1 : 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jajaran Panitia</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $bimtek->panitia->count() }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Belum Aktivasi Akun</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $bimtek->peserta->where('is_active', false)->count() }}</p>
                </div>
            </div>

            {{-- DATATABLE CARD INDEKS PESERTA --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100" x-data="pesertaTable()">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Anggota Kelas Terdaftar</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                        <span class="mx-1 text-gray-400">&middot;</span>
                        {{ $bimtek->peserta->count() }} anggota terdaftar
                    </p>
                </div>

                {{-- Toolbar Tabel --}}
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    {{-- Input Bar Pencarian Real-Time --}}
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               placeholder="Cari nama / email / NIP..."
                               class="block w-full sm:w-72 pl-9 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    @if($canManage)
                        {{-- Kontrol Aksi Hapus Massal --}}
                        <button type="button"
                                @click="bulkDelete()"
                                :disabled="selectedIds.length === 0"
                                :class="selectedIds.length > 0 ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <span x-text="selectedIds.length > 0 ? 'Hapus Terpilih (' + selectedIds.length + ')' : 'Hapus Terpilih'"></span>
                        </button>
                    @endif
                </div>

                {{-- Tabel Render Indeks Data --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                @if($canManage)
                                    <th class="px-4 py-3 text-left w-10">
                                        <input type="checkbox"
                                            @change="toggleAll($event.target.checked)"
                                            :checked="allSelected"
                                            x-ref="selectAllCheckbox"
                                            {{ $bimtek->peserta->isEmpty() ? 'disabled' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 cursor-pointer">
                                    </th>
                                @endif
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 w-12">No</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">NIP</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Asal Instansi / Sekolah</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Akses Akun</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status Verifikasi</th>
                                @if($canManage)
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($bimtek->peserta->sortBy('name') as $index => $peserta)
                                @php
                                    $assignment = DB::table('bimtek_pesertas')
                                        ->where('bimtek_id', $bimtek->id)
                                        ->where('user_id', $peserta->id)
                                        ->first();

                                    $statusVerifikasi = $assignment->status_verifikasi ?? 'pending';
                                @endphp

                                <tr class="hover:bg-gray-100 transition-colors"
                                    x-show="!search || '{{ strtolower($peserta->name . ' ' . $peserta->email . ' ' . ($peserta->nip ?? '')) }}'.includes(search.toLowerCase())" x-cloak>

                                    @if($canManage)
                                        <td class="px-4 py-4 align-middle">
                                            <input type="checkbox"
                                                value="{{ $peserta->id }}"
                                                @change="toggleSelection(@js($peserta->id))"
                                                :checked="selectedIds.includes(@js($peserta->id))"
                                                class="peserta-checkbox w-4 h-4 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 cursor-pointer">
                                        </td>
                                    @endif

                                    <td class="px-4 py-4 text-sm text-gray-500 align-middle">{{ $index + 1 }}</td>

                                    <td class="px-6 py-4 align-middle max-w-[260px]">
                                        <p class="text-sm font-medium text-gray-900 break-words">{{ $peserta->name ?? '-' }}</p>
                                        <p class="mt-0.5 text-sm text-gray-500 break-all">{{ $peserta->email }}</p>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap align-middle">{{ $peserta->nip ?? '-' }}</td>

                                    <td class="px-6 py-4 text-sm text-gray-700 align-middle max-w-[200px] break-words">{{ $peserta->asal_instansi ?? '-' }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        @if($peserta->is_active)
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Belum Aktivasi</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        @if($bimtek->butuh_verifikasi_dokumen)
                                            @if($statusVerifikasi === 'verified')
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Terverifikasi</span>
                                            @elseif($statusVerifikasi === 'rejected')
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Perlu Koreksi</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu Review</span>
                                            @endif
                                        @else
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Tanpa Verifikasi</span>
                                        @endif
                                    </td>

                                    @if($canManage)
                                        <td class="px-6 py-4 text-right align-middle whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('bimtek.verifikasi-dokumen.index', $bimtek->id) }}" class="px-2 text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                                                    Periksa Berkas
                                                </a>

                                                <form action="{{ route('bimtek.peserta.destroy', [$bimtek->id, $peserta->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                            title="Keluarkan peserta"
                                                            onclick="return confirm('Apakah Anda yakin ingin mengeluarkan {{ $peserta->name }}?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <td colspan="{{ $canManage ? 8 : 6 }}" class="px-6 py-8">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            <p class="mt-2 text-gray-500">Belum ada peserta yang mendaftar.</p>
                                        </div>
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

            {{-- Jajaran Struktur PIC & Panitia --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                {{-- Panel PIC --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Penanggung Jawab Utama Kelas (PIC)</h3>
                        @if($bimtek->pic)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-800 flex items-center justify-center font-semibold text-sm shrink-0">
                                    {{ strtoupper(substr($bimtek->pic->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $bimtek->pic->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $bimtek->pic->email }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 shrink-0">PIC Utama</span>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <p class="mt-2 text-gray-500">Belum ada delegasi nama koordinator utama untuk kelas ini.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Panel Panitia --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Anggota Tim Pelaksana (Panitia Pokja)</h3>
                        <div class="space-y-3">
                            @forelse($bimtek->panitia as $panitia)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-secondary-100 text-secondary-800 flex items-center justify-center font-semibold text-sm shrink-0">
                                        {{ strtoupper(substr($panitia->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $panitia->name }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $panitia->email }}</p>
                                    </div>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-secondary-100 text-secondary-800 shrink-0">Panitia</span>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="mt-2 text-gray-500">Belum ada delegasi daftar staf operasional kedinasan untuk kelas ini.</p>
                                </div>
                            @endforelse
                        </div>
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

                        <div x-show="open" x-transition class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                            <form action="{{ route('bimtek.peserta.store', $bimtek->id) }}" method="POST">
                                @csrf
                                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                                    <h3 class="text-lg font-bold text-primary-800" id="modal-title">Tambah Peserta dari Database</h3>
                                </div>
                                <div class="p-6 space-y-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">Pilih User</label>
                                        <select name="user_ids[]" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm h-48" multiple required>
                                            @foreach($availableUsers as $user)
                                                <option value="{{ $user->id }}" class="py-1">{{ $user->name }} - {{ $user->email }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-sm text-gray-500">Tips: Tahan tombol <strong class="font-semibold">Ctrl</strong> (Windows) atau <strong class="font-semibold">Cmd</strong> (Mac) di keyboard untuk memilih lebih dari satu nama sekaligus.</p>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                                    <button type="button" @click="open = false" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">Tambahkan ke Kelas</button>
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

                        <div x-show="open" x-transition class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                            <form action="{{ route('bimtek.peserta.store-new', $bimtek->id) }}" method="POST">
                                @csrf
                                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                                    <h3 class="text-lg font-bold text-primary-800">Daftarkan Akun Peserta Baru</h3>
                                </div>

                                <div class="p-6 space-y-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-lg {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" required placeholder="Cth: Ahmad Hidayat, M.Pd">
                                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">Email <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-lg {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" required placeholder="Cth: ahmad@gmail.com">
                                        @error('email') <p class="mt-1 text-sm text-red-600">Peringatan: {{ $message }} Silakan gunakan menu "Pilih dari Database" untuk akun ini.</p> @enderror
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700">NIP <span class="text-sm font-normal text-gray-500">(Opsional)</span></label>
                                            <input type="text" name="nip" value="{{ old('nip') }}" class="mt-1 block w-full rounded-lg {{ $errors->has('nip') ? 'border-red-500' : 'border-gray-300' }} shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                            @error('nip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700">Instansi <span class="text-sm font-normal text-gray-500">(Opsional)</span></label>
                                            <input type="text" name="asal_instansi" value="{{ old('asal_instansi') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        </div>
                                    </div>

                                    <div class="flex p-3 rounded-lg border text-sm bg-primary-50 border-primary-100 text-primary-800">
                                        <svg class="w-5 h-5 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p>Sistem akan otomatis membuatkan password secara acak dan langsung mengirimkannya ke email peserta.</p>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                                    <button type="button" @click="open = false" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">Buat Akun & Daftarkan</button>
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

                        <div x-show="open" x-transition class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                            <form action="{{ route('bimtek.peserta.import', $bimtek->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                                    <h3 class="text-lg font-bold text-primary-800">Import Daftar Peserta (CSV)</h3>
                                </div>
                                <div class="p-6 space-y-5">
                                    <p class="text-sm text-gray-500">Pastikan file CSV Anda memiliki format kolom (*Header*): <strong class="font-semibold">Nama, Email, NIP, Instansi</strong>.</p>

                                    <div class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:border-primary-500 transition-colors p-6 text-center cursor-pointer relative">
                                        <input type="file" name="file" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500"><span class="font-medium text-primary-600">Klik untuk memilih file CSV</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Maksimal ukuran: 2MB</p>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                                    <button type="button" @click="open = false" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 bg-primary-600 hover:bg-primary-800 text-white">Mulai Import</button>
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