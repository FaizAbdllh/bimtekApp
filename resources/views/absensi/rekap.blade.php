<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    {{-- REFAKTORISASI: Menjamin kestabilan UUID dan menyematkan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">Absensi</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-800 font-bold">Rekap Kehadiran</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Rekap Kehadiran Peserta
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header dengan Tombol Aksi --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <p class="text-sm font-semibold text-gray-700">Kegiatan: <span class="text-gray-900 font-bold">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span></p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- REFAKTORISASI: Kestabilan UUID parameter rute kembali --}}
                    <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Statistics Cards Widgets --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Sesi</p>
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $bimtek->sesiAbsensis->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Peserta</p>
                            {{-- REFAKTORISASI COUNTER: Menggunakan hitungan memori rekapData agar aman dari crash relasi lama --}}
                            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ count($rekapData) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lulus Syarat</p>
                            <p class="text-xl font-bold text-green-600 mt-0.5">
                                {{ collect($rekapData)->where('persentase', '>=', $syaratKehadiran)->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tidak Lulus</p>
                            <p class="text-xl font-bold text-red-600 mt-0.5">
                                {{ collect($rekapData)->where('persentase', '<', $syaratKehadiran)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Batas Batasan Kelulusan --}}
            <div class="mb-6 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-xs font-semibold shadow-inner shadow-blue-50 w-fit">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Ambang Batas Kehadiran Minimum Kelulusan Sertifikat: <span class="text-blue-950 font-bold text-sm">{{ $syaratKehadiran }}%</span></span>
                </div>
            </div>

            {{-- Rekap Horizontal Table Card Wrapper --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider">Matriks Kehadiran Komplit</h3>
                </div>

                @if(count($rekapData) > 0 && $bimtek->sesiAbsensis->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-center w-14 sticky left-0 bg-gray-50 border-r border-gray-100 z-10">No</th>
                                    <th class="px-6 py-3 text-left sticky left-14 bg-gray-50 border-r border-gray-100 z-10 min-w-[200px]">Nama Lengkap Anggota</th>
                                    @foreach($bimtek->sesiAbsensis as $sesi)
                                        <th class="px-4 py-3 text-center border-r border-gray-100/60 max-w-[120px] truncate">
                                            {{ $sesi->nama_sesi }}
                                        </th>
                                    @endforeach
                                    <th class="px-6 py-3 text-center bg-gray-50/50">Total</th>
                                    <th class="px-6 py-3 text-center bg-gray-50/50">Rasio</th>
                                    <th class="px-6 py-3 text-center bg-gray-50/50 pr-6">Status Kelayakan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                @foreach($rekapData as $index => $data)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-4 text-center text-gray-400 font-medium sticky left-0 bg-white border-r border-gray-100 z-10">{{ $loop->iteration }}</td>
                                        {{-- REFAKTORISASI STICKY ROW: Menjamin warna latar belakang cell tetap putih bersih saat digeser --}}
                                        <td class="px-6 py-4 sticky left-14 bg-white border-r border-gray-100 z-10 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center font-bold text-xs shrink-0 border border-primary-100/40">
                                                    {{ strtoupper(substr($data['peserta']->name ?? 'P', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="text-sm font-bold text-gray-900 block">{{ $data['peserta']->name ?? '-' }}</span>
                                                    <span class="text-[10px] text-gray-400 font-medium block mt-0.5">{{ $data['peserta']->email ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($bimtek->sesiAbsensis as $sesi)
                                            <td class="px-4 py-4 text-center border-r border-gray-50/40">
                                                @if(in_array($sesi->id, $data['attendances']))
                                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-50 text-green-600 rounded-full border border-green-100">
                                                        ✓
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-50 text-red-500 rounded-full border border-red-100">
                                                        ✗
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-6 py-4 text-center font-bold text-gray-900 bg-gray-50/30">
                                            {{ $data['total_hadir'] }}<span class="text-gray-400 font-normal text-xs">/{{ $bimtek->sesiAbsensis->count() }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap bg-gray-50/30">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                    <div class="{{ $data['persentase'] >= $syaratKehadiran ? 'bg-green-600' : 'bg-red-600' }} h-1.5" style="width: {{ $data['persentase'] }}%"></div>
                                                </div>
                                                <span class="text-xs font-bold {{ $data['persentase'] >= $syaratKehadiran ? 'text-green-600' : 'text-red-600' }}">{{ $data['persentase'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap bg-gray-50/30 pr-6">
                                            @if($data['persentase'] >= $syaratKehadiran)
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">
                                                    Memenuhi
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                                    Gugur
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($bimtek->sesiAbsensis->count() == 0)
                    <div class="p-12 text-center text-gray-400 font-medium">
                        <svg class="w-14 h-14 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-base font-bold text-gray-900 mb-0.5">Belum Ada Sesi Pelaksanaan</h3>
                        <p class="text-xs text-gray-400">Silakan rilis lembar sesi absensi terlebih dahulu pada menu utama.</p>
                    </div>
                @else
                    <div class="p-12 text-center text-gray-400 font-medium">
                        <svg class="w-14 h-14 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <h3 class="text-base font-bold text-gray-900 mb-0.5">Belum Ada Anggota Terdaftar</h3>
                        <p class="text-xs text-gray-400">Daftar presensi kumulatif akan muncul setelah kuota peserta luar atau internal terisi.</p>
                    </div>
                @endif
            </div>

            {{-- Legend Indikator Tabel --}}
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-xs font-semibold text-gray-500">
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Indikator Keterangan Matriks</h4>
                <div class="flex flex-wrap gap-x-6 gap-y-3 items-center">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-green-50 text-green-600 rounded-full border border-green-100 font-bold">✓</span>
                        <span>Peserta Konfirmasi Hadir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-red-50 text-red-500 rounded-full border border-red-100 font-bold">✗</span>
                        <span>Peserta Tidak Hadir (Absen)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 border border-green-200">Memenuhi</span>
                        <span>Rasio Persentase Kehadiran Mandatori &ge; {{ $syaratKehadiran }}%</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">Gugur</span>
                        <span>Rasio Persentase Kehadiran Mandatori &lt; {{ $syaratKehadiran }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>