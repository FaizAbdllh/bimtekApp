<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="text-gray-500 hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    {{-- REFAKTORISASI: Menjamin kestabilan UUID dan menyematkan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="text-gray-500 hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="text-gray-500 hover:text-primary-600 transition-colors">Absensi</a></li>
                    <li><span class="mx-1 text-gray-400">/</span></li>
                    <li class="text-gray-700">Rekap Kehadiran</li>
                </ol>
            </nav>
            <h2 class="text-xl font-bold text-gray-800">
                Rekap Kehadiran Peserta
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        {{-- Page Header dengan Tombol Aksi --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                <p class="text-sm font-semibold text-gray-700">
                    Kegiatan:
                    <span class="text-gray-900 font-medium">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</span>
                </p>
            </div>
            <div>
                {{-- REFAKTORISASI: Kestabilan UUID parameter rute kembali --}}
                <a href="{{ route('bimtek.absensi.index', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Kembali
                </a>
            </div>
        </div>

        {{-- Statistics Cards Widgets --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Sesi</p>
                <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $bimtek->sesiAbsensis->count() }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Peserta</p>
                {{-- REFAKTORISASI COUNTER: Menggunakan hitungan memori rekapData agar aman dari crash relasi lama --}}
                <p class="text-2xl font-semibold text-gray-800 mt-1">{{ count($rekapData) }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Lulus Syarat</p>
                <p class="text-2xl font-semibold text-gray-800 mt-1">
                    {{ collect($rekapData)->where('persentase', '>=', $syaratKehadiran)->count() }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tidak Lulus</p>
                <p class="text-2xl font-semibold text-gray-800 mt-1">
                    {{ collect($rekapData)->where('persentase', '<', $syaratKehadiran)->count() }}
                </p>
            </div>
        </div>

        {{-- Informasi Batas Batasan Kelulusan --}}
        <div class="mb-6 p-4 bg-primary-50 border border-primary-200 text-primary-800 rounded-lg text-sm">
            Ambang Batas Kehadiran Minimum Kelulusan Sertifikat:
            <span class="font-semibold">{{ $syaratKehadiran }}%</span>
        </div>

        {{-- Rekap Horizontal Table Card Wrapper --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Matriks Kehadiran</h3>
            </div>

            @if(count($rekapData) > 0 && $bimtek->sesiAbsensis->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center w-14 sticky left-0 bg-gray-50 border-r border-gray-200 z-10 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    No
                                </th>
                                <th class="px-6 py-3 text-left sticky left-14 bg-gray-50 border-r border-gray-200 z-10 min-w-[220px] text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Nama Peserta
                                </th>

                                @foreach($bimtek->sesiAbsensis as $sesi)
                                    <th class="px-4 py-3 text-center border-r border-gray-200 min-w-[120px] text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        {{ $sesi->nama_sesi }}
                                    </th>
                                @endforeach

                                <th class="px-6 py-3 text-center min-w-[90px] text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-center min-w-[90px] text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Rasio
                                </th>
                                <th class="px-6 py-3 text-center min-w-[130px] text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status Kelayakan
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @foreach($rekapData as $index => $data)
                                <tr>
                                    <td class="px-4 py-4 text-center text-sm text-gray-500 sticky left-0 bg-white border-r border-gray-200 z-10">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4 sticky left-14 bg-white border-r border-gray-200 z-10">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900 block">
                                                {{ $data['peserta']->name ?? '-' }}
                                            </span>
                                            <span class="text-xs text-gray-500 block mt-1">
                                                {{ $data['peserta']->email ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    @foreach($bimtek->sesiAbsensis as $sesi)
                                        <td class="px-4 py-4 text-center border-r border-gray-200 bg-white">
                                            @if(in_array($sesi->id, $data['attendances']))
                                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    Hadir
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    Tidak Hadir
                                                </span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="px-6 py-4 text-center bg-white">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $data['total_hadir'] }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            / {{ $bimtek->sesiAbsensis->count() }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center bg-white">
                                        <span class="text-sm font-semibold {{ $data['persentase'] >= $syaratKehadiran ? 'text-green-700' : 'text-red-700' }}">
                                            {{ $data['persentase'] }}%
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center bg-white">
                                        @if($data['persentase'] >= $syaratKehadiran)
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                Memenuhi
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
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
                <div class="text-center py-8">
                    <h3 class="text-base font-bold text-gray-800">
                        Belum Ada Sesi Pelaksanaan
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Silakan rilis lembar sesi absensi terlebih dahulu pada menu utama.
                    </p>
                </div>
            @else
                <div class="text-center py-8">
                    <h3 class="text-base font-bold text-gray-800">
                        Belum Ada Anggota Terdaftar
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Daftar presensi kumulatif akan muncul setelah kuota peserta luar atau internal terisi.
                    </p>
                </div>
            @endif
        </div>

        {{-- Legend Indikator Tabel --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-base font-bold text-gray-800 mb-3">
                Keterangan Matriks
            </h4>

            <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm text-gray-500">
                <div class="flex items-center gap-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Hadir
                    </span>
                    <span>Peserta tercatat hadir pada sesi.</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        Tidak Hadir
                    </span>
                    <span>Peserta tidak tercatat hadir pada sesi.</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Memenuhi
                    </span>
                    <span>Rasio kehadiran &ge; {{ $syaratKehadiran }}%.</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        Gugur
                    </span>
                    <span>Rasio kehadiran &lt; {{ $syaratKehadiran }}%.</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>