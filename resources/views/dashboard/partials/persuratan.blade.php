{{-- Dashboard Persuratan (Bagian Surat Undangan) --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Bimtek Menunggu Final Surat --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Menunggu Upload Final</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $bimtekMenungguFinal ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bimtek Surat Selesai --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Surat Sudah Final</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $bimtekSuratSelesai ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Bimtek Menunggu Final Surat (Daftar) --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Daftar Menunggu Upload Final</h3>
            @if(isset($recentBimtekMenunggu) && $recentBimtekMenunggu->count() > 0)
                <div class="space-y-3">
                    @foreach($recentBimtekMenunggu as $bimtek)
                        <div class="p-3 bg-orange-50 rounded-lg border-l-4 border-orange-400">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0 mr-2">
                                    <a href="{{ route('bimtek.show', $bimtek) }}" class="font-medium text-gray-900 hover:text-orange-600 truncate block">{{ $bimtek->judul_final }}</a>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Draft oleh: <span class="font-medium">{{ $bimtek->draftUploader?->name ?? '-' }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $bimtek->file_surat_draft_uploaded_at ? \Illuminate\Support\Carbon::parse($bimtek->file_surat_draft_uploaded_at)->diffForHumans() : '-' }}
                                    </p>
                                </div>
                                <a href="{{ route('bimtek.show', $bimtek) }}" class="flex-shrink-0 px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700 hover:bg-orange-200">
                                    Upload Final
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500">Tidak ada bimtek yang menunggu final surat.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Bimtek Surat Sudah Final --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Surat Sudah Final</h3>
            @if(isset($recentBimtekSelesai) && $recentBimtekSelesai->count() > 0)
                <div class="space-y-3">
                    @foreach($recentBimtekSelesai as $bimtek)
                        <div class="p-3 bg-green-50 rounded-lg border-l-4 border-green-400">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0 mr-2">
                                    <a href="{{ route('bimtek.show', $bimtek) }}" class="font-medium text-gray-900 hover:text-green-600 truncate block">{{ $bimtek->judul_final }}</a>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Final oleh: <span class="font-medium">{{ $bimtek->finalUploader?->name ?? '-' }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $bimtek->file_surat_final_uploaded_at ? \Illuminate\Support\Carbon::parse($bimtek->file_surat_final_uploaded_at)->diffForHumans() : '-' }}
                                    </p>
                                </div>
                                <a href="{{ route('bimtek.download-final', $bimtek) }}" class="flex-shrink-0 px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 hover:bg-green-200">
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500">Belum ada surat final yang diunggah.</p>
                </div>
            @endif
        </div>
    </div>
</div>
