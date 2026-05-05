<x-app-layout>
    @section('title', 'Laporan')

    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Laporan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-gray-600 mb-6">Pilih jenis laporan untuk pratinjau ekspor PDF atau Excel.</p>

            @if($bimteks->isEmpty())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
                    <p>Tidak ada data bimtek yang tersedia untuk hak akses Anda saat ini.</p>
                </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Rekap Peserta --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Rekap Peserta</h3>
                            <p class="text-sm text-gray-500">Daftar peserta bimtek</p>
                        </div>
                    </div>
                    <form action="{{ route('laporan.rekap-peserta') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" id="bimtek_peserta" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Bimtek</option>
                            @foreach($bimteks as $bimtek)
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final, 40) }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                PDF
                            </button>
                            <button type="button" onclick="exportPesertaExcel()" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                Excel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Rekap Absensi --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Rekap Absensi</h3>
                            <p class="text-sm text-gray-500">Kehadiran peserta</p>
                        </div>
                    </div>
                    <form action="{{ route('laporan.rekap-absensi') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" id="bimtek_absensi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Bimtek</option>
                            @foreach($bimteks as $bimtek)
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final, 40) }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                PDF
                            </button>
                            <button type="button" onclick="exportAbsensiExcel()" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                Excel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Rekap Nilai --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Rekap Nilai Tugas</h3>
                            <p class="text-sm text-gray-500">Nilai tugas peserta</p>
                        </div>
                    </div>
                    <form action="{{ route('laporan.rekap-nilai') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Bimtek</option>
                            @foreach($bimteks as $bimtek)
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final, 40) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                            Export PDF
                        </button>
                    </form>
                </div>

                {{-- Laporan Kegiatan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Laporan Kegiatan</h3>
                            <p class="text-sm text-gray-500">Laporan lengkap bimtek</p>
                        </div>
                    </div>
                    <form action="{{ route('laporan.laporan-kegiatan') }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="bimtek_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Bimtek</option>
                            @foreach($bimteks as $bimtek)
                                <option value="{{ $bimtek->id }}">{{ Str::limit($bimtek->judul_final, 40) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                            Export PDF
                        </button>
                    </form>
                </div>

                {{-- Daftar Bimtek (Admin, Kepala, PPK only) --}}
                @if(Auth::user()->hasRole(['Admin IT', 'Kepala', 'PPK']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Daftar Bimtek</h3>
                            <p class="text-sm text-gray-500">Daftar bimtek per tahun</p>
                        </div>
                    </div>
                    <form action="{{ route('laporan.daftar-bimtek') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <select name="tahun" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="semua">Semua Status</option>
                                <option value="persiapan">Persiapan</option>
                                <option value="berlangsung">Berlangsung</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                            Export PDF
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function exportPesertaExcel() {
            const bimtekId = document.getElementById('bimtek_peserta').value;
            if (!bimtekId) {
                alert('Pilih bimtek terlebih dahulu');
                return;
            }
            window.location.href = '{{ route("laporan.export-peserta-excel") }}?bimtek_id=' + bimtekId;
        }
        
        function exportAbsensiExcel() {
            const bimtekId = document.getElementById('bimtek_absensi').value;
            if (!bimtekId) {
                alert('Pilih bimtek terlebih dahulu');
                return;
            }
            window.location.href = '{{ route("laporan.export-absensi-excel") }}?bimtek_id=' + bimtekId;
        }
    </script>
    @endpush
</x-app-layout>
