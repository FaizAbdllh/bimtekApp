<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium">
                    <li>
                        <a href="{{ route('bimtek.index') }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">Bimtek</a>
                    </li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    <li>
                        <a href="{{ route('bimtek.show', $bimtek->id) }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">
                            {{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}
                        </a>
                    </li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    <li>
                        <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}"
                            class="text-gray-500 hover:text-primary-600 transition-colors">Absensi</a>
                    </li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    <li class="text-gray-700 font-semibold">
                        {{ Str::limit($sesi->nama_sesi, 20) }}
                    </li>
                </ol>
            </nav>

            <h2 class="text-xl font-bold text-gray-800">
                {{ $sesi->nama_sesi }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">

        {{-- Page Header dengan Tombol Kendali Aksi --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-700">
                    Kegiatan:
                    <span class="font-medium text-gray-900">
                        {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                    </span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>

                @if($canManage)
                    {{-- TOMBOL BARU: Munculkan Layar QR Code jika mode bukan online murni --}}
                    @if($bimtek->mode_pelaksanaan !== 'online')
                        <a href="{{ route('bimtek.absensi.qr', [$bimtek->id, $sesi->id]) }}"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            Layar QR Code
                        </a>
                    @endif

                    <a href="{{ route('bimtek.absensi.edit', [$bimtek->id, $sesi->id]) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Koreksi Sesi
                    </a>

                    <form action="{{ route('bimtek.absensi.toggle-status', [$bimtek->id, $sesi->id]) }}"
                        method="POST"
                        class="inline">
                        @csrf
                        @method('PATCH')

                        @if($sesi->isOpen())
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Kunci Gerbang
                            </button>
                        @else
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
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

            {{-- Kiri-Tengah: Tabel Log Daftar Peserta Hadir --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">
                            Log Antrean Peserta Hadir
                        </h3>

                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ $sesi->absensiPesertas->count() }} Terisi
                        </span>
                    </div>

                    @if($sesi->absensiPesertas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr class="border-b border-gray-200">
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 w-14">
                                            No
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Nama Lengkap Anggota
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Waktu Presensi
                                        </th>

                                        @if($canManage)
                                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 pr-6 w-24">
                                                Aksi
                                            </th>
                                        @endif
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200">
                                    @foreach($sesi->absensiPesertas as $index => $absensi)
                                        <tr class="hover:bg-gray-100 transition-colors">
                                            <td class="px-6 py-4 text-center text-sm text-gray-500">
                                                {{ $index + 1 }}
                                            </td>

                                            <td class="px-6 py-4">
                                                <span class="text-sm font-medium text-gray-900 block">
                                                    {{ $absensi->user->name ?? '-' }}
                                                </span>
                                                <span class="text-xs text-gray-500 block mt-1">
                                                    {{ $absensi->user->email ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-700">
                                                {{ $absensi->created_at->format('H:i') }}
                                                <span class="text-xs text-gray-500">WIB</span>
                                            </td>

                                            @if($canManage)
                                                <td class="px-6 py-4 text-right pr-6 align-middle whitespace-nowrap">
                                                    <form action="{{ route('bimtek.absensi.delete-record', [$bimtek->id, $sesi->id, $absensi->user_id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Batalkan pencatatan kehadiran untuk peserta ini?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:text-red-800 transition-colors">
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
                        <div class="text-center py-8 px-6">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <p class="mt-2 text-sm text-gray-500">
                                Belum ada record peserta yang melakukan check-in pada sesi ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kanan: Panel Informasi & Statistik Sesi --}}
            <div class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Informasi Sesi
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Dibuat Oleh
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-900">
                                {{ $sesi->openedBy->name ?? 'Sistem' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Waktu Rilis Pertama
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-900">
                                {{ $sesi->created_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Terakhir Diperbarui
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-900">
                                {{ $sesi->updated_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Widget Progress Bar Grafik Kehadiran Sesi --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Statistik Kelas Sesi Ini
                    </h3>

                    @php
                        $hadir = $sesi->absensiPesertas->count();
                        $tidakHadir = max(0, $totalPeserta - $hadir);
                        $persentase = $totalPeserta > 0 ? round(($hadir / $totalPeserta) * 100, 1) : 0;
                    @endphp

                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Rasio Partisipasi
                                </span>
                                <span class="text-sm font-semibold text-primary-600">
                                    {{ $persentase }}%
                                </span>
                            </div>

                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-primary-600 h-2.5 rounded-full"
                                    style="width: {{ $persentase }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2 text-center">
                            <div class="bg-green-50 rounded-lg border border-green-100 p-3">
                                <p class="text-2xl font-semibold text-green-700">
                                    {{ $hadir }}
                                </p>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-green-600">
                                    Hadir
                                </p>
                            </div>

                            <div class="bg-red-50 rounded-lg border border-red-100 p-3">
                                <p class="text-2xl font-semibold text-red-600">
                                    {{ $tidakHadir }}
                                </p>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-red-500">
                                    Absen
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($canManage)
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-red-100 p-6">
                        <h3 class="text-base font-bold text-red-600 mb-2">
                            Zona Berbahaya
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed mb-4">
                            Penghapusan lembar sesi ini bersifat permanen. Seluruh matriks kehadiran peserta yang tertangkap di dalam sesi ini akan ikut terhapus dari sistem DIPA.
                        </p>

                        <form action="{{ route('bimtek.absensi.destroy', [$bimtek->id, $sesi->id]) }}"
                            method="POST"
                            onsubmit="return confirm('PERINGATAN: Anda yakin ingin menghapus total sesi absensi ini? Seluruh record kehadiran peserta pada sesi ini akan hangus permanen!')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Hapus Sesi Secara Permanen
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>