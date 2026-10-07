{{-- Dashboard Koordinator RT --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Kebutuhan Belum Dipenuhi --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Belum Dipenuhi</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $kebutuhanBelumDipenuhi ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kebutuhan Terpenuhi --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Telah Terpenuhi</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $kebutuhanTerpenuhi ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Kebutuhan RT Menunggu --}}
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Kebutuhan RT Menunggu Pemenuhan</h3>
    </div>
    @if(isset($recentPengajuan) && $recentPengajuan->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul Bimtek</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Pelaksanaan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status RT</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentPengajuan as $pengajuan)
                        <tr class="hover:bg-gray-100 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $pengajuan->judul_final ?? $pengajuan->judul_rencana }}
                            </td>
                            {{-- REFAKTORISASI: Menampilkan lokasi aktual, jika kosong gunakan rencana lokasi awal --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $pengajuan->lokasi_aktual ?? $pengajuan->tempat_kegiatan_rencana ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $pengajuan->tanggal_mulai_aktual ? $pengajuan->tanggal_mulai_aktual->format('d/m/Y') : ($pengajuan->tanggal_mulai_rencana ? $pengajuan->tanggal_mulai_rencana->format('d/m/Y') : '-') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusRtColors = [
                                        'belum_dipenuhi' => 'bg-yellow-100 text-yellow-800',
                                        'sebagian_dipenuhi' => 'bg-primary-100 text-primary-800',
                                        'telah_dipenuhi' => 'bg-green-100 text-green-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusRtColors[$pengajuan->status_rt] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $pengajuan->status_rt)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('rt.show', $pengajuan->id) }}" class="text-primary-600 hover:text-primary-800 font-medium">Kelola</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center px-6 py-8">
            <p class="text-gray-500">Tidak ada kebutuhan logistik sarana Rumah Tangga yang menunggu pemenuhan.</p>
        </div>
    @endif
</div>