<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.show', $bimtek) }}" class="hover:text-primary-600">{{ Str::limit($bimtek->judul_final, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.absensi.index', $bimtek) }}" class="hover:text-primary-600">Absensi</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-900 font-medium">Rekap Kehadiran</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Rekap Kehadiran Peserta
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header with Actions --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-gray-600">{{ $bimtek->judul_final }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('bimtek.absensi.index', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Sesi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bimtek->sesiAbsensis->count() }}</p>
                        </div>
                    </div>
                </div>

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
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Memenuhi Syarat</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ collect($rekapData)->where('persentase', '>=', $syaratKehadiran)->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tidak Memenuhi</p>
                            <p class="text-2xl font-bold text-red-600">
                                {{ collect($rekapData)->where('persentase', '<', $syaratKehadiran)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Syarat Kehadiran --}}
            <div class="mb-6 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Syarat Kehadiran Minimum: {{ $syaratKehadiran }}%</span>
                </div>
            </div>

            {{-- Rekap Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Rekap Kehadiran Per Peserta</h3>
                </div>

                @if(count($rekapData) > 0 && $bimtek->sesiAbsensis->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-12 bg-gray-50">Nama Peserta</th>
                                    @foreach($bimtek->sesiAbsensis as $sesi)
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            {{ Str::limit($sesi->nama_sesi, 15) }}
                                        </th>
                                    @endforeach
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rekapData as $index => $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 sticky left-0 bg-white">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap sticky left-12 bg-white">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-primary-600">{{ strtoupper(substr($data['peserta']->name, 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900">{{ $data['peserta']->name }}</span>
                                                    <p class="text-xs text-gray-500">{{ $data['peserta']->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($bimtek->sesiAbsensis as $sesi)
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                @if(in_array($sesi->id, $data['attendances']))
                                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 rounded-full">
                                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 rounded-full">
                                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $data['total_hadir'] }}</span>
                                            <span class="text-sm text-gray-500">/ {{ $bimtek->sesiAbsensis->count() }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="{{ $data['persentase'] >= $syaratKehadiran ? 'bg-green-600' : 'bg-red-600' }} h-2 rounded-full" style="width: {{ $data['persentase'] }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium {{ $data['persentase'] >= $syaratKehadiran ? 'text-green-600' : 'text-red-600' }}">{{ $data['persentase'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($data['persentase'] >= $syaratKehadiran)
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Memenuhi
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Tidak Memenuhi
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($bimtek->sesiAbsensis->count() == 0)
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada sesi absensi</h3>
                        <p class="text-gray-500">Buat sesi absensi terlebih dahulu untuk melihat rekap kehadiran.</p>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada peserta</h3>
                        <p class="text-gray-500">Tambahkan peserta ke bimtek ini untuk melihat rekap kehadiran.</p>
                    </div>
                @endif
            </div>

            {{-- Legend --}}
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Keterangan:</h4>
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 rounded-full">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span class="text-sm text-gray-600">Hadir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 rounded-full">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span class="text-sm text-gray-600">Tidak Hadir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Memenuhi
                        </span>
                        <span class="text-sm text-gray-600">Kehadiran ≥ {{ $syaratKehadiran }}%</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Tidak Memenuhi
                        </span>
                        <span class="text-sm text-gray-600">Kehadiran &lt; {{ $syaratKehadiran }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
