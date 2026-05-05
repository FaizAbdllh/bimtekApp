<x-app-layout>
    <x-slot name="header">
        QR Code Absensi
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.absensi.show', [$bimtek, $sesi]) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- QR Code Display Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $sesi->nama_sesi }}</h2>
                        <p class="text-sm text-gray-600 mb-6">{{ $bimtek->judul_final }}</p>

                        @if($sesi->isOpen() && $sesi->qr_code && !$sesi->isQrExpired())
                            @php
                                // Generate QR Code using Endroid v6
                                $builder = new \Endroid\QrCode\Builder\Builder();
                                $qrResult = $builder->build(
                                    data: $sesi->qr_code,
                                    size: 300,
                                    margin: 10
                                );
                            @endphp
                            
                            {{-- QR Code Display --}}
                            <div class="bg-gray-50 border-2 border-gray-200 rounded-lg p-8 mb-4">
                                <div class="flex justify-center mb-4">
                                    <img src="{{ $qrResult->getDataUri() }}" alt="QR Code Absensi" class="max-w-full">
                                </div>
                                
                                <div class="flex items-center justify-center gap-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        <span class="font-medium text-green-700">QR Aktif</span>
                                    </div>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600">Berlaku selama sesi terbuka</span>
                                </div>
                            </div>

                            {{-- Instructions --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-left">
                                <h3 class="font-semibold text-blue-900 mb-2">Instruksi untuk Panitia:</h3>
                                <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                                    <li>Tampilkan QR Code ini di proyektor/layar di ruangan</li>
                                    <li>Peserta klik tombol "Scan QR" di halaman sesi absensi</li>
                                    <li>Peserta arahkan kamera HP ke QR Code di layar</li>
                                    <li>Sistem otomatis mencatat kehadiran setelah scan berhasil</li>
                                    <li>QR Code berlaku selama sesi terbuka, tutup sesi untuk invalidkan QR</li>
                                </ol>
                                <p class="text-xs text-blue-600 mt-3 font-medium">💡 Tips: Perbesar tampilan browser (Ctrl/Cmd +) agar QR Code lebih mudah di-scan</p>
                            </div>

                            {{-- Refresh Button --}}
                            {{-- <div class="mt-6">
                                <form action="{{ route('bimtek.absensi.toggle-status', [$bimtek, $sesi]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                                        Generate QR Baru
                                    </button>
                                </form>
                            </div> --}}

                        @elseif($sesi->isQrExpired())
                            {{-- QR Expired (Session Closed) --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <p class="text-gray-600 font-semibold mb-2">Sesi Ditutup</p>
                                <p class="text-sm text-gray-500">QR Code tidak lagi berlaku karena sesi sudah ditutup</p>
                            </div>

                        @else
                            {{-- Session Closed --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <p class="text-gray-600 font-semibold mb-2">Sesi Ditutup</p>
                                <p class="text-sm text-gray-500">Buka sesi terlebih dahulu untuk generate QR Code</p>
                            </div>
                        @endif

                        {{-- Attendance Count --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-2xl font-bold text-primary-600">{{ $sesi->absensiPesertas->count() }}</p>
                                    <p class="text-sm text-gray-600">Peserta Hadir</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-400">{{ $bimtek->peserta->count() - $sesi->absensiPesertas->count() }}</p>
                                    <p class="text-sm text-gray-600">Belum Hadir</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
