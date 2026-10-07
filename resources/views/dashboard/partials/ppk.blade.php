{{-- Dashboard PPK --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Pengajuan Menunggu PPK --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $pengajuanMenungguPpk ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Disetujui PPK --}}
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
                    <p class="text-2xl font-semibold text-gray-800">{{ $pengajuanDisetujuiPpk ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Anggaran --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Anggaran Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-800">Rp {{ number_format($totalAnggaran ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pengajuan Menunggu Approval Anggaran --}}
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Pengajuan Menunggu Persetujuan Anggaran</h3>
    </div>
    @if(isset($recentPengajuan) && $recentPengajuan->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul Bimtek</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengaju</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estimasi Anggaran Usulan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentPengajuan as $pengajuan)
                        <tr class="hover:bg-gray-100 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pengajuan->judul_rencana }}</td>
                            {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $pengajuan->pic->name ?? '-' }}</td>
                            {{-- REFAKTORISASI: Menghitung total anggaran rencana secara dinamis dari relasi kebutuhanAnggarans --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                Rp {{ number_format($pengajuan->kebutuhanAnggarans->sum('total_biaya') ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{-- REFAKTORISASI: Mengarahkan tombol review langsung menuju rute detail PPK --}}
                                <a href="{{ route('approval.ppk.show', $pengajuan->id) }}" class="text-primary-600 hover:text-primary-800 font-medium">Review</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center px-6 py-8">
            <p class="text-gray-500">Tidak ada pengajuan usulan biaya yang memerlukan peninjauan anggaran saat ini.</p>
        </div>
    @endif
</div>