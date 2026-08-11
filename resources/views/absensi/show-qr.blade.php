<x-app-layout>
    <x-slot name="header">
        QR Code Absensi Sesi
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-4">
                {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute kembali --}}
                <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Sesi
                </a>
            </div>

            {{-- QR Code Display Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 sm:p-8">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $sesi->nama_sesi }}</h2>
                        {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                        <p class="text-sm font-semibold text-primary-600 uppercase tracking-wider mb-6">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>

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
                            <div class="bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 sm:p-8 mb-6 shadow-inner">
                                <div class="flex justify-center mb-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100 max-w-[320px] mx-auto">
                                    <img src="{{ $qrResult->getDataUri() }}" alt="QR Code Absensi Sesi Active" class="max-w-full h-auto">
                                </div>
                                
                                <div class="flex items-center justify-center gap-3 text-xs">
                                    <div class="flex items-center gap-1.5 bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                        Sesi QR Aktif
                                    </div>
                                    <span class="text-gray-300 font-bold">•</span>
                                    <span class="text-gray-500 font-medium">Valid selama gerbang absensi dibuka</span>
                                </div>
                            </div>

                            {{-- Panel Instruksi Tata Cara Penggunaan --}}
                            <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5 text-left shadow-inner shadow-blue-50">
                                <h3 class="font-bold text-blue-900 mb-2 text-xs uppercase tracking-wider">Instruksi untuk Panitia Lapangan:</h3>
                                <ol class="text-xs text-blue-800 space-y-2 list-decimal list-inside font-semibold leading-relaxed">
                                    <li>Proyeksikan/tampilkan halaman penuh browser ini pada <span class="text-blue-950 font-bold">Layar Utama / TV Proyektor</span> ruangan.</li>
                                    <li>Arahkan peserta untuk masuk ke menu "Kelola Absensi" lalu menekan tombol <span class="text-blue-950 font-bold">Scan QR</span>.</li>
                                    <li>Minta peserta membidik kamera HP mereka tepat menghadap ke gambar kode QR di atas.</li>
                                    <li>Sistem otomatis mencatat log waktu kehadiran begitu pola kotak terkonfirmasi valid.</li>
                                    <li>Untuk keamanan DIPA, silakan <span class="text-blue-950 font-bold">Kunci Gerbang</span> pada panel utama jika waktu toleransi keterlambatan habis.</li>
                                </ol>
                                <div class="mt-4 pt-3 border-t border-blue-200/60 text-[11px] text-blue-600 font-medium flex items-center gap-1">
                                    💡 <strong>Tips Proyektor:</strong> Tekan kombinasi tombol <kbd class="bg-white px-1 py-0.5 rounded border border-blue-200 shadow-sm font-sans font-bold text-gray-700 text-[10px]">Ctrl</kbd> + <kbd class="bg-white px-1 py-0.5 rounded border border-blue-200 shadow-sm font-sans font-bold text-gray-700 text-[10px]">+</kbd> beberapa kali untuk memperbesar gambar QR agar peserta di barisan belakang lebih mudah melakukan scan.
                                </div>
                            </div>

                        @elseif($sesi->isQrExpired() || !$sesi->isOpen())
                            {{-- Kondisi Layar Jika Sesi Sedang Terkunci/Ditutup --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-8 text-center shadow-inner">
                                <div class="w-12 h-12 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-bold">
                                    !
                                </div>
                                <p class="text-gray-700 font-bold text-base mb-1">Gerbang Presensi Ditutup</p>
                                <p class="text-xs text-gray-400 max-w-sm mx-auto font-medium leading-relaxed">Kode QR dinamis tidak lagi diterbitkan sistem karena status sesi absensi ini sedang dalam kondisi terkunci atau telah berakhir.</p>
                            </div>
                        @endif

                        {{-- Panel Akumulasi Real-Time Kehadiran Kelas --}}
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                    <p class="text-2xl font-bold text-primary-600">{{ $sesi->absensiPesertas->count() }}</p>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Peserta Hadir</p>
                                </div>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                    {{-- REFAKTORISASI PROTEKSI: Mengamankan kueri kalkulasi counter sisa absensi --}}
                                    <p class="text-2xl font-bold text-gray-400">{{ max(0, ($bimtek->peserta->count() ?? 0) - $sesi->absensiPesertas->count()) }}</p>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Belum Check-In</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>