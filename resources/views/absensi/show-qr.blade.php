<x-app-layout>
    <x-slot name="header">
        QR Code Absensi Sesi
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-6">
                {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute kembali --}}
                <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Sesi
                </a>
            </div>

            {{-- QR Code Display Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="text-center">
                        <h2 class="text-lg font-bold text-gray-800 mb-1">{{ $sesi->nama_sesi }}</h2>
                        {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                        <p class="text-sm text-gray-500 mb-6">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>

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
                            
                            {{-- Modul Frame Tampilan QR Code (Dibuat kontras tinggi agar mudah di-scan proyektor) --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                                <div class="flex justify-center mb-4 bg-white p-4 rounded-lg border border-gray-200 max-w-[320px] mx-auto">
                                    <img src="{{ $qrResult->getDataUri() }}" alt="QR Code Absensi Sesi Active" class="max-w-full h-auto">
                                </div>
                                
                                <div class="flex items-center justify-center gap-3 text-sm">
                                    <div class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        Sesi QR Aktif
                                    </div>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-500">Valid selama gerbang absensi dibuka</span>
                                </div>
                            </div>

                            {{-- Panel Instruksi Tata Cara Penggunaan --}}
                            <div class="bg-primary-50 border border-primary-100 rounded-lg p-5 text-left">
                                <h3 class="text-base font-bold text-primary-800 mb-2">Instruksi untuk Panitia Lapangan:</h3>
                                <ol class="text-sm text-primary-800 space-y-2 list-decimal list-inside leading-relaxed">
                                    <li>Proyeksikan/tampilkan halaman penuh browser ini pada <span class="font-semibold">Layar Utama / TV Proyektor</span> ruangan.</li>
                                    <li>Arahkan peserta untuk masuk ke menu "Kelola Absensi" lalu menekan tombol <span class="font-semibold">Scan QR</span>.</li>
                                    <li>Minta peserta membidik kamera HP mereka tepat menghadap ke gambar kode QR di atas.</li>
                                    <li>Sistem otomatis mencatat log waktu kehadiran begitu pola kotak terkonfirmasi valid.</li>
                                    <li>Untuk keamanan DIPA, silakan <span class="font-semibold">Kunci Gerbang</span> pada panel utama jika waktu toleransi keterlambatan habis.</li>
                                </ol>
                                
                            </div>

                        @elseif($sesi->isQrExpired() || !$sesi->isOpen())
                            {{-- Kondisi Layar Jika Sesi Sedang Terkunci/Ditutup --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-6 py-8 text-center">
                                <svg class="h-12 w-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <p class="mt-2 text-base font-bold text-gray-800">Gerbang Presensi Ditutup</p>
                                <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">Kode QR dinamis tidak lagi diterbitkan sistem karena status sesi absensi ini sedang dalam kondisi terkunci atau telah berakhir.</p>
                            </div>
                        @endif

                        {{-- Panel Akumulasi Real-Time Kehadiran Kelas --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <p class="text-2xl font-semibold text-gray-800">{{ $sesi->absensiPesertas->count() }}</p>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">Peserta Hadir</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    {{-- REFAKTORISASI PROTEKSI: Mengamankan kueri kalkulasi counter sisa absensi --}}
                                    <p class="text-2xl font-semibold text-gray-800">{{ max(0, ($bimtek->peserta->count() ?? 0) - $sesi->absensiPesertas->count()) }}</p>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">Belum Check-In</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>