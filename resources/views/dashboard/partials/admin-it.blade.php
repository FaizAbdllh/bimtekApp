{{-- Dashboard Admin IT --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
    {{-- Total Users --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-primary-100 text-primary-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total User</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Bimtek --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-green-100 text-green-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Bimtek</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalBimtek ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bimtek Aktif --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-yellow-100 text-yellow-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Bimtek Aktif</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $bimtekAktif ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Pengajuan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-purple-100 text-purple-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalPengajuan ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Pengajuan --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Pengajuan Terbaru</h3>
    </div>
    <div class="p-6">
        @if(isset($recentPengajuan) && $recentPengajuan->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengaju</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentPengajuan as $pengajuan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-800 font-medium">{{ $pengajuan->judul_rencana }}</td>
                                {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $pengajuan->pic->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status dan menyelaraskan warna badge --}}
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full 
                                        @if($pengajuan->status == 'disetujui_final') bg-green-100 text-green-700
                                        @elseif($pengajuan->status == 'ditolak') bg-red-100 text-red-700
                                        @elseif($pengajuan->status == 'perlu_revisi') bg-orange-100 text-orange-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $pengajuan->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-2 text-gray-500">Belum ada pengajuan terbaru yang membutuhkan peninjauan.</p>
            </div>
        @endif
    </div>
</div>