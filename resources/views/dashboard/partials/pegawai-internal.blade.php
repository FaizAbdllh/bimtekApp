{{-- Dashboard Pegawai Internal --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- Pengajuan Saya --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pengajuan Saya</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $pengajuanSaya ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Disetujui --}}
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
                    <p class="text-2xl font-semibold text-gray-700">{{ $pengajuanDisetujui ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bimtek Saya --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Bimtek Saya</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $bimtekSaya ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sebagai PIC --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Sebagai PIC</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $bimtekSebagaiPic ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Pengajuan Terbaru --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Pengajuan Saya</h3>
            </div>
            @if(isset($recentPengajuan) && $recentPengajuan->count() > 0)
                <div class="space-y-3">
                    @foreach($recentPengajuan as $pengajuan)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1 min-w-0 mr-3">
                                <p class="font-medium text-gray-900 truncate">{{ $pengajuan->judul_rencana }}</p>
                                <p class="text-sm text-gray-500">{{ $pengajuan->created_at->format('d/m/Y') }}</p>
                            </div>
                            {{-- REFAKTORISASI: Mengubah status_pengajuan menjadi status tunggal baru --}}
                            <span class="flex-shrink-0 px-2 py-1 text-xs font-semibold rounded-full 
                                @if($pengajuan->status == 'disetujui_final') bg-green-100 text-green-800
                                @elseif($pengajuan->status == 'ditolak') bg-red-100 text-red-800
                                @elseif($pengajuan->status == 'perlu_revisi') bg-orange-100 text-orange-800
                                @elseif($pengajuan->status == 'draft_pic') bg-gray-100 text-gray-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $pengajuan->status == 'draft_pic' ? 'Draft' : ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 py-4 text-center">Belum ada riwayat pengajuan kegiatan.</p>
            @endif
        </div>
    </div>

    {{-- Bimtek Aktif --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Bimtek Aktif</h3>
            @if(isset($bimtekAktif) && $bimtekAktif->count() > 0)
                <div class="space-y-3">
                    @foreach($bimtekAktif as $bimtek)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $bimtek->tanggal_mulai_aktual ? \Carbon\Carbon::parse($bimtek->tanggal_mulai_aktual)->format('d/m/Y') : '-' }} - 
                                        {{ $bimtek->tanggal_selesai_aktual ? \Carbon\Carbon::parse($bimtek->tanggal_selesai_aktual)->format('d/m/Y') : '-' }}
                                    </p>
                                </div>
                                {{-- REFAKTORISASI: Menentukan peran kontekstual secara dinamis dari skema tabel terpisah baru --}}
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($bimtek->pic_user_id === auth()->id()) bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ $bimtek->pic_user_id === auth()->id() ? 'PIC' : ($bimtek->pivot->fungsi_panitia ?? 'Panitia') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 py-4 text-center">Tidak ada bimtek aktif yang sedang Anda ikuti.</p>
            @endif
        </div>
    </div>
</div>