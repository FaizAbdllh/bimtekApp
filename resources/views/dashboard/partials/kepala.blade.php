{{-- Dashboard Kepala --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Pengajuan Menunggu --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $pengajuanMenunggu ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Disetujui --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $pengajuanDisetujui ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Ditolak --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Ditolak</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $pengajuanDitolak ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pengajuan Terbaru --}}
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Pengajuan Menunggu Persetujuan</h3>
    </div>
    @if(isset($recentPengajuan) && $recentPengajuan->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul Bimtek</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengaju</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentPengajuan as $pengajuan)
                        <tr class="hover:bg-gray-100 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pengajuan->judul_rencana }}</td>
                            {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $pengajuan->pic->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $pengajuan->jenis_kegiatan == 'internal' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                                    {{ ucfirst($pengajuan->jenis_kegiatan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{-- REFAKTORISASI: Mengarahkan tautan ke route riil milik Kepala --}}
                                <a href="{{ route('approval.kepala.show', $pengajuan->id) }}" class="text-primary-600 hover:text-primary-800 font-medium">Review</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center px-6 py-8">
            <p class="text-gray-500">Tidak ada pengajuan yang menunggu persetujuan saat ini.</p>
        </div>
    @endif
</div>