<x-app-layout>
    @section('title', 'Manajemen Pengguna')

    <x-slot name="header">
        Manajemen Akun Pengguna
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Bagian Halaman --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Daftar Akun Pengguna</h2>
                    <p class="mt-1 text-sm text-gray-500">Kelola data profil, tingkat kewenangan, dan kredensial akses user sistem</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Registrasi User Baru
                    </a>
                </div>
            </div>
            
            {{-- Panel Penyaringan & Pencarian Komplit (Filter & Search Section) --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
                        {{-- Field Input Kata Kunci Pencarian --}}
                        <div class="flex-1">
                            <label for="search" class="sr-only">Cari Pengguna</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama lengkap, alamat email, atau identitas NIP..." class="block w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            </div>
                        </div>

                        {{-- Dropdown Filter Klasifikasi Peran --}}
                        <div class="sm:w-52">
                            <label for="role" class="sr-only">Filter Kewenangan Role</label>
                            <select name="role" id="role" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">Semua Hak Akses Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nama_peran }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tombol Kendali Submit Form Filter --}}
                        <div class="flex gap-2 shrink-0">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter Data
                            </button>
                            @if (request('search') || request('role'))
                                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- KARTU GRGRID DATA MASTER TABEL PENGGUNA --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">Informasi Akun Utama</th>
                                <th scope="col" class="px-6 py-3 text-center w-40">Identitas NIP</th>
                                <th scope="col" class="px-6 py-3 text-left">Asal Instansi Kedinasan</th>
                                <th scope="col" class="px-6 py-3 text-center w-44">Tingkat Hak Akses</th>
                                <th scope="col" class="px-6 py-3 text-right w-32">Tindakan Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-100 transition-colors">
                                    {{-- Kolom Detail User Avatar & Kredensial --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-primary-100 text-primary-800 flex items-center justify-center font-semibold text-xs shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-sm font-medium text-gray-900 block truncate max-w-[200px]">{{ $user->name }}</span>
                                                <span class="text-sm text-gray-500 block mt-0.5">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- Kolom NIP Pegawai --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-gray-700">
                                        {{ $user->nip ?? '-' }}
                                    </td>
                                    
                                    {{-- Kolom Nama Lembaga Instansi --}}
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap max-w-xs truncate">
                                        {{ $user->asal_instansi ?? '-' }}
                                    </td>
                                    
                                    {{-- Kolom Lencana Level Kewenangan (Refaktorisasi Style Lencana) --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap align-middle">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                            @stylePeran($user->role?->nama_peran)">
                                            {{ $user->role?->nama_peran ?? 'Anggota Luar' }}
                                        </span>
                                    </td>
                                    
                                    {{-- Kelompok Tombol Aksi Operasional Operator --}}
                                    <td class="px-6 py-4 text-right whitespace-nowrap align-middle">
                                        <div class="flex items-center justify-end gap-1">
                                            {{-- Tombol Modifikasi --}}
                                            {{-- REFAKTORISASI: Kestabilan ID parameter rute edit --}}
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Ubah Data Profil">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            
                                            {{-- Form Reset Sandi Akun --}}
                                            {{-- REFAKTORISASI: Kestabilan ID parameter rute reset-password --}}
                                            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengatur ulang (reset) password milik akun {{ $user->name }} menjadi setelan kata sandi bawaan pabrik?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Reset Kata Sandi Akun">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            
                                            {{-- Form Penghapusan Akun Pengguna (Kecuali Akun Sendiri) --}}
                                            @if ($user->id !== auth()->id())
                                                {{-- REFAKTORISASI: Kestabilan ID parameter rute destroy --}}
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN KRITIS: Menghapus total data user {{ $user->name }} dapat memutuskan relasi log operasional kelas terkait. Anda yakin ingin melanjutkan?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun Permanen">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- Keadaan Tampilan Jika Kueri Filter / Database Kosong --}}
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <h3 class="mt-2 text-base font-bold text-gray-800">Tidak Ditemukan Record User</h3>
                                        <p class="mt-1 text-sm text-gray-500">Belum ada akun pengguna yang terdaftar atau kriteria filter penyaringan salah.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Komponen Paginasi Daftar --}}
                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

{{-- REFAKTORISASI BLADE DIRECTIVES STYLING MACRO: Memangkas wall of text markup switch --}}
{{-- Catatan: pastikan directive @stylePeran (jika didefinisikan di AppServiceProvider) memakai palet yang sama dengan fungsi di bawah --}}
@php
function getStylePeran($namaPeran) {
    switch($namaPeran) {
        case 'Admin IT':
            return 'bg-primary-100 text-primary-800';
        case 'Kepala':
            return 'bg-primary-100 text-primary-800';
        case 'PPK':
            return 'bg-primary-100 text-primary-800';
        case 'Koordinator RT':
            return 'bg-secondary-100 text-secondary-800';
        case 'Pegawai Internal':
            return 'bg-secondary-100 text-secondary-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
@endphp