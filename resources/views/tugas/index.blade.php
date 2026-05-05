<x-app-layout>
    <x-slot name="header">
        Kelola Tugas
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
                    <a href="{{ route('bimtek.tugas.create', $bimtek) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Tugas Baru
                    </a>
                @endif
            </div>

            {{-- Bimtek Info Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $bimtek->judul_final }}</h2>
                    <p class="text-sm text-gray-500">{{ $bimtek->tugas->count() }} tugas tersedia</p>
                </div>
            </div>

            {{-- Tugas List --}}
            @if(!$isVerified)
                {{-- Locked State for Unverified Peserta --}}
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                    <p class="text-sm text-gray-600 max-w-md mx-auto">
                        Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses tugas.
                    </p>
                </div>
            @else
            @if($bimtek->tugas->count() > 0)
                <div class="space-y-4">
                    @foreach($bimtek->tugas as $tugas)
                        <div class="bg-white rounded-lg shadow-sm border overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    {{-- Info Tugas --}}
                                    <div class="flex-1">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <a href="{{ route('bimtek.tugas.show', [$bimtek, $tugas]) }}" class="text-lg font-semibold text-gray-900 hover:text-primary-600 transition-colors">
                                                    {{ $tugas->judul }}
                                                </a>
                                                @if($tugas->deskripsi)
                                                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ Str::limit($tugas->deskripsi, 150) }}</p>
                                                @endif
                                                
                                                <div class="mt-3 flex flex-wrap items-center gap-4 text-sm">
                                                    {{-- Deadline --}}
                                                    <div class="flex items-center gap-1.5 {{ $tugas->isDeadlinePassed() ? 'text-red-600' : 'text-gray-500' }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span>
                                                            Deadline: {{ $tugas->deadline->format('d M Y, H:i') }}
                                                            @if($tugas->isDeadlinePassed())
                                                                <span class="text-red-600 font-medium">(Terlewat)</span>
                                                            @endif
                                                        </span>
                                                    </div>

                                                    {{-- Pengumpulan Count --}}
                                                    <div class="flex items-center gap-1.5 text-gray-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                        <span>{{ $tugas->pengumpulanTugas->count() }} pengumpulan</span>
                                                    </div>

                                                    {{-- File Instruksi --}}
                                                    @if($tugas->file_instruksi_path)
                                                        <div class="flex items-center gap-1.5 text-gray-500">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                            </svg>
                                                            <span>Ada file instruksi</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status & Actions --}}
                                    <div class="flex flex-col items-end gap-3">
                                        {{-- Status Peserta --}}
                                        @if($isPeserta)
                                            @php
                                                $submission = $userSubmissions->get($tugas->id);
                                            @endphp
                                            @if($submission)
                                                @if($submission->nilai !== null)
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Dinilai: {{ $submission->nilai }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Sudah Dikumpulkan
                                                    </span>
                                                @endif
                                            @else
                                                @if($tugas->isDeadlinePassed())
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Tidak Mengumpulkan
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Belum Dikumpulkan
                                                    </span>
                                                @endif
                                            @endif
                                        @endif

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('bimtek.tugas.show', [$bimtek, $tugas]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Detail
                                            </a>
                                            @if($canManage)
                                                <a href="{{ route('bimtek.tugas.edit', [$bimtek, $tugas]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Tugas</h3>
                    <p class="text-gray-500 mb-6">Belum ada tugas yang dibuat untuk bimtek ini.</p>
                    @if($canManage)
                        <a href="{{ route('bimtek.tugas.create', $bimtek) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat Tugas Pertama
                        </a>
                    @endif
                </div>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>
