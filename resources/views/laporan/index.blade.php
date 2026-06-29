<x-app-layout>
    @section('title', 'Laporan Hub')

    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Pusat Pelaporan & Ekspor Data
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-gray-500 mb-6 font-medium">Silakan pilih jenis dokumen laporan di bawah ini untuk mengunduh rekapitulasi dalam format cetak PDF resmi atau spreadsheet Excel.</p>

            @if($bimteks->isEmpty())
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 text-yellow-800 shadow-sm">
                    <p class="text-sm font-semibold">Tidak ada log data bimbingan teknis yang tersedia atau dapat diakses oleh akun Anda saat ini.</p>
                </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Rekap Peserta --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Rekapitulasi Anggota</h3>
                                <p class="text-xs text-gray-400 font-medium">Biodata profil & asal instansi peserta</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('laporan.rekap-peserta') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" id="bimtek_peserta" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">-- Pilih Kelas Bimtek --</option>
                            @foreach($bimteks as $bimtek)
                                {{-- REFAKTORISASI: Menambahkan fallback judul jika judul_final belum diisi --}}
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 40) }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2 pt-1">
                            <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-red-700 transition shadow-sm">
                                Ekspor PDF
                            </button>
                            <button type="button" onclick="exportPesertaExcel()" class="flex-1 px-4 py-2 bg-green-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-green-700 transition shadow-sm">
                                spreadsheet
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Rekap Absensi --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Rekapitulasi Presensi</h3>
                                <p class="text-xs text-gray-400 font-medium">Matriks kehadiran sesi kelas</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('laporan.rekap-absensi') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" id="bimtek_absensi" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">-- Pilih Kelas Bimtek --</option>
                            @foreach($bimteks as $bimtek)
                                {{-- REFAKTORISASI: Menambahkan fallback judul jika judul_final belum diisi --}}
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 40) }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2 pt-1">
                            <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-red-700 transition shadow-sm">
                                Ekspor PDF
                            </button>
                            <button type="button" onclick="exportAbsensiExcel()" class="flex-1 px-4 py-2 bg-green-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-green-700 transition shadow-sm">
                                spreadsheet
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Rekap Nilai Tugas --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Rekapitulasi Nilai Tugas</h3>
                                <p class="text-xs text-gray-400 font-medium">Batas kelulusan syarat sertifikat</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('laporan.rekap-nilai') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">-- Pilih Kelas Bimtek --</option>
                            @foreach($bimteks as $bimtek)
                                {{-- REFAKTORISASI: Menambahkan fallback judul jika judul_final belum diisi --}}
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 40) }}</option>
                            @endforeach
                        </select>
                        <div class="pt-1">
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-red-700 transition shadow-sm">
                                Ekspor Cetak PDF resmi
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Laporan Kegiatan Komplit --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Buku Laporan Kegiatan</h3>
                                <p class="text-xs text-gray-400 font-medium">Berkas komplit pertanggungjawaban</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('laporan.laporan-kegiatan') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">-- Pilih Kelas Bimtek --</option>
                            @foreach($bimteks as $bimtek)
                                {{-- REFAKTORISASI: Menambahkan fallback judul jika judul_final belum diisi --}}
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 40) }}</option>
                            @endforeach
                        </select>
                        <div class="pt-1">
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-red-700 transition shadow-sm">
                                Unduh Dokumen PDF
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Daftar Bimtek Kolektif Tahunan (Pimpinan & Admin) --}}
                @if(Auth::user()->hasRole(['Admin IT', 'Kepala', 'PPK']))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-5">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Daftar Agenda Kolektif</h3>
                                <p class="text-xs text-gray-400 font-medium">Matriks rekapitulasi realisasi DIPA</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('laporan.daftar-bimtek') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <select name="tahun" class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                            <select name="status" class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="semua">Semua Status</option>
                                <option value="persiapan">Persiapan</option>
                                <option value="berlangsung">Berlangsung</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="pt-1">
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-red-700 transition shadow-sm">
                                Cetak Rekap Tahunan
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Script Handler spreadsheet Ekspor Excel --}}
    @push('scripts')
    <script>
        function exportPesertaExcel() {
            const bimtekId = document.getElementById('bimtek_peserta').value;
            if (!bimtekId) {
                alert('Silakan pilih salah satu kelas bimtek terlebih dahulu.');
                return;
            }
            window.location.href = '{{ route("laporan.export-peserta-excel") }}?bimtek_id=' + bimtekId;
        }
        
        function exportAbsensiExcel() {
            const bimtekId = document.getElementById('bimtek_absensi').value;
            if (!bimtekId) {
                alert('Silakan pilih salah satu kelas bimtek terlebih dahulu.');
                return;
            }
            window.location.href = '{{ route("laporan.export-absensi-excel") }}?bimtek_id=' + bimtekId;
        }
    </script>
    @endpush
</x-app-layout>