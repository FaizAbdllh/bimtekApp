<x-app-layout>
    <x-slot name="header">
        Materi Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Back & Header --}}
            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('bimtek.show', $bimtek) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Bimtek
                </a>

                @if($canManage)
                    <a href="{{ route('bimtek.materi.create', $bimtek) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload Materi
                    </a>
                @endif
            </div>

            {{-- Bimtek Info Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $bimtek->judul_final }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ $bimtek->tanggal_mulai_aktual?->format('d M Y') }}
                        @if($bimtek->tanggal_selesai_aktual && $bimtek->tanggal_mulai_aktual != $bimtek->tanggal_selesai_aktual)
                            - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                        @endif
                        • {{ $bimtek->lokasi_aktual ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- Materi List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(!$isVerified)
                        {{-- Locked State for Unverified Peserta --}}
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                            <p class="text-sm text-gray-600 max-w-md mx-auto">
                                Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses materi pembelajaran.
                            </p>
                        </div>
                    @else
                    {{-- Tabs --}}
                    <div x-data="{ tab: 'semua' }" class="mb-6">
                        <div class="border-b mb-4">
                            <nav class="flex -mb-px space-x-4">
                                <button @click="tab = 'semua'" :class="tab === 'semua' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm">
                                    Semua ({{ $bimtek->materis->count() }})
                                </button>
                                <button @click="tab = 'materi'" :class="tab === 'materi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm">
                                    Materi ({{ $bimtek->materis->where('tipe', 'materi')->count() }})
                                </button>
                                <button @click="tab = 'panduan'" :class="tab === 'panduan' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm">
                                    Panduan ({{ $bimtek->materis->where('tipe', 'panduan')->count() }})
                                </button>
                            </nav>
                        </div>

                        @if($bimtek->materis->count() > 0)
                            <div class="space-y-3">
                                @foreach($bimtek->materis as $materi)
                                    <div x-show="tab === 'semua' || tab === '{{ $materi->tipe }}'" 
                                         class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                        <div class="flex items-center flex-1">
                                            {{-- File Icon --}}
                                            @php
                                                $ext = pathinfo($materi->file_path, PATHINFO_EXTENSION);
                                                $iconColor = match($ext) {
                                                    'pdf' => 'text-red-500',
                                                    'doc', 'docx' => 'text-blue-500',
                                                    'ppt', 'pptx' => 'text-orange-500',
                                                    'xls', 'xlsx' => 'text-green-500',
                                                    default => 'text-gray-500',
                                                };
                                            @endphp
                                            <div class="p-2 rounded-lg bg-gray-100 mr-4">
                                                <svg class="w-8 h-8 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>

                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900">{{ $materi->judul }}</h4>
                                                <div class="flex items-center space-x-3 text-sm text-gray-500 mt-1">
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $materi->tipe === 'materi' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                        {{ $materi->tipe === 'materi' ? 'Materi' : 'Panduan' }}
                                                    </span>
                                                    <span>{{ strtoupper($ext) }}</span>
                                                    <span>{{ $materi->created_at->format('d M Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-1">
                                            @php
                                                $canPreview = in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
                                            @endphp

                                            {{-- Preview (untuk file yang bisa di-preview) --}}
                                            @if($canPreview)
                                                <a href="{{ route('bimtek.materi.preview', [$bimtek, $materi]) }}" 
                                                   target="_blank"
                                                   class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                   title="Lihat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            {{-- Download --}}
                                            <a href="{{ route('bimtek.materi.download', [$bimtek, $materi]) }}" 
                                               class="p-2 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                               title="Download">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>

                                            @if($canManage)
                                                {{-- Edit --}}
                                                <a href="{{ route('bimtek.materi.edit', [$bimtek, $materi]) }}" 
                                                   class="p-2 text-gray-500 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                                   title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>

                                                {{-- Delete --}}
                                                <form action="{{ route('bimtek.materi.destroy', [$bimtek, $materi]) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                            title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-gray-500">Belum ada materi.</p>
                                @if($canManage)
                                    <a href="{{ route('bimtek.materi.create', $bimtek) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Upload Materi Pertama
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
