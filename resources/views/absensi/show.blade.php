<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    {{-- REFAKTORISASI: Menggunakan parameter ID eksplisit dan menambahkan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">Absensi</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-800 font-bold">{{ Str::limit($sesi->nama_sesi, 20) }}</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                {{ $sesi->nama_sesi }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header dengan Tombol Kendali Aksi --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="text-sm font-semibold text-gray-700">Kegiatan: <span class="text-gray-900 font-bold">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>
                </div>
                <div class="flex flex-wrap items-center gap-2 font-bold text-xs uppercase tracking-wide">
                    {{-- REFAKTORISASI: Kestabilan ID parameter rute kembali --}}
                    <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    @if($canManage)
                        {{-- REFAKTORISASI: Kestabilan ID parameter rute edit --}}
                        <a href="{{ route('bimtek.absensi.edit', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Koreksi Sesi
                        </a>
                        {{-- REFAKTORISASI: Kestabilan ID parameter rute toggle status gerbang --}}
                        <form action="{{ route('bimtek.absensi.toggle-status', [$bimtek->id, $sesi->id]) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            @if($sesi->isOpen())
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Kunci Gerbang
                                </button>
                            @else
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                    Buka Gerbang
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            {{-- Tata Letak Split Grid Konten --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                {{-- Kiri-Tengah: Tabel Log Daftar Peserta Hadir (2/3 Width) --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Log Antrean Peserta Hadir</h3>
                            <span class="px-2.5 py-0.5 bg-green-100 text-green-800 rounded-md text-xs font-bold border border-green-200">
                                {{ $sesi->absensiPesertas->count() }} Terisi
                            </span>
                        </div>

                        @if($sesi->absensiPesertas->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-center w-14">No</th>
                                            <th class="px-6 py-3 text-left">Nama Lengkap Anggota</th>
                                            <th class="px-6 py-3 text-center">Waktu Presensi</th>
                                            @if($canManage)
                                                <th class="px-6 py-3 text-right pr-6 w-24">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                        {{-- Catatan: Relasi absensiPesertas mengarah ke koleksi entitas absensi_pesertas --}}
                                        @foreach($sesi->absensiPesertas as $index => $absensi)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-6 py-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm font-bold text-gray-900 block">{{ $absensi->user->name ?? '-' }}</span>
                                                    <span class="text-[10px] text-gray-400 font-medium block mt-0.5">{{ $absensi->user->email ?? '-' }}</span>
                                                </td>
                                                <td class="px-6 py-4 text-center font-semibold text-gray-700">
                                                    {{ $absensi->created_at->format('H:i') }} <span class="text-gray-400 font-normal text-xs">WIB</span>
                                                </td>
                                                @if($canManage)
                                                    <td class="px-6 py-4 text-right pr-6 align-middle whitespace-nowrap">
                                                        {{-- REFAKTORISASI FORM DELETE: Kestabilan parameter rute ID multi-level --}}
                                                        <form action="{{ route('bimtek.absensi.delete-record', [$bimtek->id, $sesi->id, $absensi->id]) }}" method="POST" onsubmit="return confirm('Batalkan pencatatan kehadiran untuk peserta ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 transition">
                                                                Batalkan
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-12 text-center text-gray-400 font-medium">
                                <p class="text-xs text-gray-400">Belum ada record peserta yang melakukan check-in pada sesi ini.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kanan: Panel Panel Informasi & Statistik Sesi (1/3 Width) --}}
                <div class="space-y-6">
                    {{-- Widget Informasi Metadata Sesi --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">Informasi Sesi</h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Dibuat Oleh</p>
                                <p class="text-gray-900 font-bold mt-0.5">{{ $sesi->openedBy->name ?? 'Sistem' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Waktu Rilis Pertama</p>
                                <p class="text-gray-700 font-semibold mt-0.5">{{ $sesi->created_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Terakhir Diperbarui</p>
                                <p class="text-gray-700 font-semibold mt-0.5">{{ $sesi->updated_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Widget Progress Bar Grafik Kehadiran Sesi --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">Statistik Kelas Sesi Ini</h3>
                        @php
                            $hadir = $sesi->absensiPesertas->count();
                            $tidakHadir = max(0, $totalPeserta - $hadir);
                            $persentase = $totalPeserta > 0 ? round(($hadir / $totalPeserta) * 100, 1) : 0;
                        @endphp
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-1.5 text-xs font-bold">
                                    <span class="text-gray-400 uppercase tracking-wide">Rasio Partisipasi</span>
                                    <span class="text-primary-600 text-sm font-black">{{ $persentase }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $persentase }}%"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-2 text-center">
                                <div class="bg-green-50/60 rounded-xl border border-green-100 p-3">
                                    <p class="text-xl font-black text-green-700">{{ $hadir }}</p>
                                    <p class="text-[10px] font-bold text-green-600 uppercase tracking-wider mt-0.5">Hadir</p>
                                </div>
                                <div class="bg-red-50/60 rounded-xl border border-red-100 p-3">
                                    <p class="text-xl font-black text-red-600">{{ $tidakHadir }}</p>
                                    <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mt-0.5">Absen</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modul Penghapusan Permanen (Zona Berbahaya) --}}
                    @if($canManage)
                        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
                            <h3 class="text-xs font-bold text-red-600 uppercase tracking-wider mb-2">Zona Berbahaya</h3>
                            <p class="text-xs text-gray-400 font-medium leading-relaxed mb-4">Penghapusan lembar sesi ini bersifat permanen. Seluruh matriks kehadiran peserta yang tertangkap di dalam sesi ini akan ikut terhapus dari sistem DIPA.</p>
                            {{-- REFAKTORISASI: Kestabilan ID parameter rute destroy --}}
                            <form action="{{ route('bimtek.absensi.destroy', [$bimtek->id, $sesi->id]) }}" method="POST" onsubmit="return confirm('PERINGATAN: Anda yakin ingin menghapus total sesi absensi ini? Seluruh record kehadiran peserta pada sesi ini akan hangus permanen!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm">
                                    Hapus Sesi Secara Permanen
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>