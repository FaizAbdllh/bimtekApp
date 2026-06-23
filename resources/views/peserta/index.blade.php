<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.show', $bimtek) }}" class="hover:text-primary-600">{{ Str::limit($bimtek->judul_final, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-900 font-medium">Kelola Peserta</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Kelola Peserta
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header with Actions --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('bimtek.show', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <div class="mt-2">
                        <p class="text-gray-600">{{ $bimtek->judul_final }}</p>
                        <p class="text-sm text-gray-500">{{ $bimtek->peserta->count() }} peserta terdaftar</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if($canManage)
                        <a href="{{ route('bimtek.peserta.export', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Export CSV
                        </a>
                        <button type="button" 
                            x-data 
                            @click="$dispatch('open-modal', 'import-peserta')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Import CSV
                        </button>
                        <button type="button" 
                            x-data 
                            @click="$dispatch('open-modal', 'tambah-peserta')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Manual
                        </button>
                    @endif
                </div>
            </div>

            {{-- Show new users created during import (Bulk) --}}
            @if(session('new_users') && count(session('new_users')) > 0)
                <div class="mb-6 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg">
                    <p class="font-medium mb-2">Akun peserta berhasil dibuat. Password awal:</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-blue-200">
                                    <th class="text-left py-1 pr-4">Nama</th>
                                    <th class="text-left py-1 pr-4">Email</th>
                                    <th class="text-left py-1">Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('new_users') as $newUser)
                                    <tr>
                                        <td class="py-1 pr-4">{{ $newUser['name'] }}</td>
                                        <td class="py-1 pr-4">{{ $newUser['email'] }}</td>
                                        <td class="py-1 font-mono bg-blue-100 px-2 rounded">{{ $newUser['password'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs mt-2 text-blue-600">* Simpan dan berikan ke peserta yang bersangkutan.</p>
                </div>
            @endif

            {{-- Show single new user password --}}
            @if(session('new_user_password'))
                <div class="mb-6 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg">
                    <p class="font-medium">
                        Akun peserta berhasil dibuat. Password awal:
                        <span class="font-mono bg-blue-100 px-2 py-0.5 rounded">{{ session('new_user_password') }}</span>
                    </p>
                    <p class="text-xs mt-1 text-blue-600">
                        Simpan dan berikan ke peserta yang bersangkutan.
                    </p>
                </div>
            @endif

            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="mb-6 px-4 py-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg">
                    <p class="font-medium mb-2">Beberapa data tidak dapat diimport:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Peserta</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bimtek->peserta->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">PIC</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bimtek->pic ? 1 : 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Panitia</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bimtek->panitia->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Menunggu Aktivasi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $pesertaPendingCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Peserta Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" x-data="pesertaTable()">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Peserta</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Peserta yang terdaftar melalui link undangan, import, atau input manual.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Search --}}
                            <div class="relative">
                                <input type="text" 
                                       x-model="search" 
                                       placeholder="Cari peserta..."
                                       class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="navigator.clipboard.writeText('{{ url('/login') }}'); alert('Link halaman login & aktivasi berhasil disalin!');"
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 font-medium text-sm transition"
                                    title="Salin Link Login/Aktivasi untuk Peserta">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 4h5m-5 4h5m-3 4h3"/>
                                </svg>
                                Salin Link Login
                            </button>
                            @if($canManage)
                                {{-- Bulk Actions --}}
                                <div x-show="selectedIds.length > 0" class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500" x-text="selectedIds.length + ' dipilih'"></span>
                                    <button type="button" 
                                            @click="bulkDelete()"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 font-medium text-sm transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                                <div class="ml-2">
                                    @php
                                        $allPesertaIds = $bimtek->peserta->pluck('id')->toArray();
                                        
                                        // Hitung riil user_id mana saja yang statusnya SUDAH AKTIF
                                        $aktifUserIds = \App\Models\ActivationToken::whereIn('user_id', $allPesertaIds)
                                            ->whereNotNull('used_at')
                                            ->pluck('user_id')
                                            ->toArray();

                                        // Hasil pending riil adalah sisa ID peserta yang belum aktif sama sekali
                                        $pendingPesertaIds = array_values(array_diff($allPesertaIds, $aktifUserIds));
                                        $realPendingCount = count($pendingPesertaIds);
                                    @endphp

                                    {{-- Tombol Utama: Terkunci otomatis (disabled) jika riil tidak ada akun pending ATAU panitia belum mencentang --}}
                                    <button type="button" 
                                        :disabled="selectedIds.length === 0"
                                       @click="
                                            const checkedBoxes = Array.from(document.querySelectorAll('.peserta-checkbox:checked'));
                                            const activeCount = checkedBoxes.filter(cb => cb.getAttribute('data-aktif') === 'true').length;

                                            if (checkedBoxes.length > 0 && activeCount === checkedBoxes.length) {
                                                alert('Gagal Membuka Jendela!\n\nSemua peserta yang Anda centang sudah berstatus Aktif.');
                                                return;
                                            }

                                            if (activeCount > 0) {
                                                if (!confirm('Beberapa peserta yang dipilih sudah berstatus Aktif.\n\nSistem hanya akan memproses dan mengirimkan email token ke peserta yang Belum Aktivasi saja. Lanjutkan?')) {
                                                    return;
                                                }
                                            }

                                            $dispatch('open-modal', 'generate-tokens');
                                        "
                                        class="inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm transition"
                                        :class="selectedIds.length === 0 
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' 
                                            : 'bg-blue-600 text-white hover:bg-blue-700 shadow'">
                                        Generate Token Aktivasi
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                @if($canManage)
                                    <th class="px-6 py-3 text-left w-12">
                                        <input type="checkbox" 
                                               @change="toggleAll($event.target.checked)"
                                               :checked="allSelected"
                                               x-ref="selectAllCheckbox"
                                              {{ $bimtek->peserta->isEmpty() ? 'disabled' : '' }}
                                               class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                    </th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instansi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Akun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Berkas</th>
                                @if($canManage)
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($bimtek->peserta->sortBy('name') as $index => $peserta)
                                @php
                                    $latestToken = \App\Models\ActivationToken::where('user_id', $peserta->id)
                                        ->latest()
                                        ->first();
                                    $hasActiveStatus = $latestToken && $latestToken->used_at;

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
                                
                                <tr class="hover:bg-gray-50" 
                                    x-show="!search || '{{ strtolower($peserta->name . ' ' . $peserta->email . ' ' . ($peserta->nip ?? '')) }}'.includes(search.toLowerCase())">
                                    
                                    @if($canManage)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{-- FIX: Mengunci mati checkbox milik user yang sudah AKTIF --}}
                                            <input type="checkbox"
                                                value="{{ $peserta->id }}"
                                                data-boleh-token="{{ $bolehGenerateToken ? 'true' : 'false' }}"
                                                data-aktif="{{ $hasActiveStatus ? 'true' : 'false' }}"
                                                data-nama="{{ $peserta->name }}"
                                                @change="toggleSelection(@js($peserta->id))"
                                                :checked="selectedIds.includes(@js($peserta->id))"
                                                class="peserta-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                        </td>
                                    @endif
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-primary-600">{{ strtoupper(substr($peserta->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $peserta->name }}</p>
                                                @if($peserta->no_hp)
                                                    <p class="text-xs text-gray-500">{{ $peserta->no_hp }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $peserta->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $peserta->nip ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $peserta->asal_instansi ?? '-' }}</td>
                                    
                                    {{-- Kolom Status Akun --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $hasActiveStatus ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                                            {{ $hasActiveStatus ? 'Aktif' : 'Belum Aktivasi' }}
                                        </span>
                                    </td>

                                    {{-- Kolom Status Berkas --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($bimtek->butuh_verifikasi_dokumen)
                                            @if($bolehGenerateToken)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                                    Menunggu Verifikasi
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-400 border border-gray-100">
                                                Tidak Diperlukan
                                            </span>
                                        @endif
                                    </td>

                                    @if($canManage)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1">
                                                <form action="{{ route('bimtek.peserta.destroy', [$bimtek, $peserta]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded-lg transition" 
                                                            title="Hapus Peserta"
                                                            onclick="return confirm('Yakin ingin menghapus {{ $peserta->name }} dari peserta?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                
                                                @if($latestToken && ! $latestToken->used_at && ! $latestToken->revoked_at)
                                                    <form action="{{ route('bimtek.activation-tokens.revoke', [$bimtek, $latestToken]) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded-lg transition" title="Revoke Token" onclick="return confirm('Revoke token untuk {{ $peserta->name }}?')">
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
                                    <td colspan="{{ $canManage ? 9 : 7 }}" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <p class="text-gray-500">Belum ada peserta terdaftar.</p>
                                        @if($canManage)
                                            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                                                <button type="button"
                                                    x-data
                                                    @click="$dispatch('open-modal', 'tambah-peserta')"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm transition">
                                                    Tambah Manual
                                                </button>
                                                <form action="{{ route('bimtek.generate-invite', $bimtek) }}" method="post">
                                                    @csrf
                                                    <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                                                        Buat Link Pendaftaran
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

                {{-- Bulk Delete Form (Hidden) --}}
                @if($canManage)
                    <form id="bulk-delete-form" action="{{ route('bimtek.peserta.bulk-destroy', $bimtek) }}" method="POST" style="display: none;">
                        @csrf
                        <div id="bulk-delete-inputs"></div>
                    </form>
                @endif
            </div>

            {{-- PIC & Panitia Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- PIC --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">PIC (Person In Charge)</h3>
                    </div>
                    <div class="p-6">
                        @if($bimtek->pic)
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">{{ strtoupper(substr($bimtek->pic->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $bimtek->pic->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $bimtek->pic->email }}</p>
                                </div>
                                <span class="ml-auto inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    PIC
                                </span>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Belum ada PIC.</p>
                        @endif
                    </div>
                </div>

                {{-- Panitia --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Panitia</h3>
                    </div>
                    <div class="p-6">
                        @forelse($bimtek->panitia as $panitia)
                            <div class="flex items-center gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b' : '' }}">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-green-600">{{ strtoupper(substr($panitia->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $panitia->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $panitia->email }}</p>
                                </div>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Panitia
                                </span>
                                @if($canManage)
                                    <form action="{{ route('bimtek.peserta.change-role', [$bimtek, $panitia]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="peran" value="peserta">
                                        <button type="submit" 
                                                class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-gray-100 rounded-lg transition" 
                                                title="Jadikan Peserta"
                                                onclick="return confirm('Ubah {{ $panitia->name }} menjadi Peserta?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Belum ada panitia.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Batch Generate Tokens --}}
    <x-modal name="generate-tokens" :show="false">
        <form id="generate-tokens-form" action="{{ Route::has('bimtek.activation-tokens.generate-batch') ? route('bimtek.activation-tokens.generate-batch', $bimtek) : url('') }}" method="POST" class="p-6">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kelola Aktivasi Peserta</h3>
            <p class="text-sm text-gray-500 mb-4">
                Pilih peserta yang akan dibuatkan token aktivasi. Token ditampilkan sekali saat proses generate.
            </p>
            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Jumlah hari berlaku (opsional)</label>
                <input type="number" name="days" min="1" max="365" class="w-32 px-3 py-2 border rounded" placeholder="7">
            </div>
            <div class="mb-4 text-sm text-gray-700">
                <p>Pilih peserta menggunakan kotak centang di daftar peserta, lalu klik "Generate".</p>
            </div>
            <div id="generate-hidden-inputs"></div>
            <div class="flex justify-end gap-2">
                <button type="button" x-data @click="$dispatch('close-modal', 'generate-tokens')" class="px-4 py-2 bg-white border rounded">Batal</button>
                <button type="submit" onclick="return submitGenerateBatch()" class="px-4 py-2 bg-blue-600 text-white rounded">Generate</button>
            </div>
        </form>
    </x-modal>

    @if($canManage)
        <x-modal name="tambah-peserta" :show="false" maxWidth="lg">
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Peserta Manual</h3>
                    <p class="text-sm text-gray-500">Tambahkan peserta secara manual atau sinkronkan data user.</p>
                </div>

                {{-- Form: Tambah Peserta Manual --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-900">Tambah Peserta Manual</h4>
                    <p class="text-sm text-gray-500 mb-3">Buat akun baru sekaligus menambahkan sebagai peserta.</p>
                </div>

                <form action="{{ route('bimtek.peserta.store-new', $bimtek) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" name="name" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                placeholder="Nama lengkap">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                placeholder="nama@email.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                            <input type="text" name="nip"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                placeholder="Opsional">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asal Instansi</label>
                            <input type="text" name="asal_instansi"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                placeholder="Opsional">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                            Buat & Tambah
                        </button>
                    </div>
                </form>

                <div class="border-t border-gray-200"></div>

                {{-- Form: Sinkronkan Peserta yang Sudah Ada --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-900">Sinkronkan Peserta yang Sudah Ada</h4>
                    <p class="text-sm text-gray-500 mb-3">Pilih satu atau beberapa user yang sudah terdaftar di sistem.</p>
                </div>

                <form action="{{ route('bimtek.peserta.store', $bimtek) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        @if($availableUsers->isEmpty())
                            <p class="text-sm text-gray-500 py-4 text-center">Tidak ada user yang tersedia untuk ditambahkan.</p>
                        @else
                            <div class="border border-gray-300 rounded-lg max-h-64 overflow-y-auto">
                                <div class="divide-y divide-gray-200">
                                    @foreach($availableUsers as $user)
                                        <label class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" 
                                                   name="user_ids[]" 
                                                   value="{{ $user->id }}"
                                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->email }} &middot; {{ $user->role?->nama_peran ?? '-' }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Centang satu atau lebih user untuk disinkronkan sebagai peserta.</p>
                        @endif
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition" @disabled($availableUsers->isEmpty())>
                            Sinkronkan Peserta
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="import-peserta" :show="false" maxWidth="md">
            <form action="{{ route('bimtek.peserta.import', $bimtek) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Import Peserta (CSV)</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File CSV</label>
                    <input type="file" name="file" accept=".csv,text/csv" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <p class="text-xs text-gray-500 mt-2">Gunakan template agar format sesuai.</p>
                    <a href="{{ route('bimtek.peserta.download-template') }}" class="text-xs text-primary-600 hover:text-primary-700">
                        Download template CSV
                    </a>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                        Import
                    </button>
                </div>
            </form>
        </x-modal>
    @endif

    <script>
        function pesertaTable() {
            return {
                search: '',
                selectedIds: [],
                // PERBAIKAN SCRIPT: Injeksi daftar ID yang HANYA berstatus BELUM AKTIVASI ke pool Alpine
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
                        alert('Pilih minimal 1 peserta dari daftar untuk dihapus.');
                        return;
                    }
                    
                    if (confirm('Yakin ingin menghapus ' + this.selectedIds.length + ' peserta yang dipilih?')) {
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
                        alert('Token telah dikirimkan via email.');
                    } else {
                        alert('Token dibuat.');
                    }
                } else {
                    alert('Gagal membuat token: ' + (data.message || res.statusText));
                }
            } catch (err) {
                alert('Terjadi kesalahan saat membuat token.');
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