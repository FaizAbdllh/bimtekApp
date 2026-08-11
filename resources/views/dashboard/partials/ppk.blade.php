{{-- Dashboard PPK --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Pengajuan Menunggu PPK --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 00-2 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $pengajuanMenungguPpk ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Disetujui PPK --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $pengajuanDisetujuiPpk ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Anggaran --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Anggaran Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-700">Rp {{ number_format($totalAnggaran ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pengajuan Menunggu Approval Anggaran --}}
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Pengajuan Menunggu Persetujuan Anggaran</h3>
        </div>
        @if(isset($recentPengajuan) && $recentPengajuan->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Bimtek</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengaju</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estimasi Anggaran Usulan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentPengajuan as $pengajuan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pengajuan->judul_rencana }}</td>
                                {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->pic->name ?? '-' }}</td>
                                {{-- REFAKTORISASI: Menghitung total anggaran rencana secara dinamis dari relasi kebutuhanAnggarans --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya') ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{-- REFAKTORISASI: Mengarahkan tombol review langsung menuju rute detail PPK --}}
                                    <a href="{{ route('approval.ppk.show', $pengajuan->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">Review</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-6">
                <p class="text-gray-500">Tidak ada pengajuan usulan biaya yang memerlukan peninjauan anggaran saat ini.</p>
            </div>
        @endif
    </div>
</div>