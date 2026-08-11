<x-app-layout>
    <x-slot name="header">
        Scan QR Code & Presensi Absensi
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.absensi.show', [$bimtek->id, $sesi->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- Main Scanner Card Container --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $sesi->nama_sesi }}</h2>
                        <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                    </div>

                    @if($hasAttended)
                        {{-- Kondisi Layar Jika Peserta Sudah Tercatat Hadir --}}
                        <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-8 text-center shadow-sm">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-200 font-bold text-lg">
                                ✓
                            </div>
                            <p class="text-emerald-900 font-bold text-base mb-1">Kehadiran Anda Sudah Tercatat</p>
                            <p class="text-xs text-emerald-600/80 font-medium">Sistem mengonfirmasi data presensi Anda untuk sesi ini telah terekam aman di pangkalan data.</p>
                        </div>
                    @else

                        {{-- JIKA KELAS HYBRID: TAMPILKAN SWITCHER PILIHAN METODE --}}
                        @if($bimtek->mode_pelaksanaan === 'hybrid')
                            <div x-data="{ method: 'qr' }" class="space-y-6">
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 text-center">Pilih Metode Kehadiran Anda</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <button @click="method = 'qr'" :class="method === 'qr' ? 'border-primary-600 bg-white text-primary-900 shadow-sm' : 'border-transparent bg-transparent text-gray-500 hover:text-gray-800'" class="p-3 rounded-xl border-2 text-center transition font-bold text-xs flex flex-col items-center gap-1">
                                            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            <span>Hadir di Ruangan</span>
                                            <span class="text-[10px] font-normal text-gray-400">Scan QR Code</span>
                                        </button>
                                        <button @click="method = 'online'" :class="method === 'online' ? 'border-primary-600 bg-white text-primary-900 shadow-sm' : 'border-transparent bg-transparent text-gray-500 hover:text-gray-800'" class="p-3 rounded-xl border-2 text-center transition font-bold text-xs flex flex-col items-center gap-1">
                                            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Hadir Online</span>
                                            <span class="text-[10px] font-normal text-gray-400">Upload Screenshot</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- PANEL 1: SCANNER KAMERA (QR) --}}
                                <div x-show="method === 'qr'" class="space-y-6">
                                    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5 shadow-inner shadow-blue-50">
                                        <h3 class="font-bold text-blue-900 mb-2 text-xs uppercase tracking-wider">Langkah Pemindaian:</h3>
                                        <ol class="text-xs text-blue-800 space-y-1.5 list-decimal list-inside font-semibold">
                                            <li>Berikan izin pembukaan <span class="text-blue-950 font-bold">Akses Kamera</span> pada browser Anda.</li>
                                            <li>Arahkan lensa kamera menghadap ke gambar <span class="text-blue-950 font-bold">QR Code</span> di proyektor.</li>
                                            <li>Sistem otomatis memproses verifikasi dan mengunci kehadiran Anda.</li>
                                        </ol>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="relative bg-neutral-900 rounded-2xl overflow-hidden shadow-md border border-neutral-800" style="aspect-ratio: 1/1;">
                                            <div id="qr-reader" class="w-full h-full"></div>
                                            <div id="scan-status" class="absolute top-4 left-4 right-4 hidden z-20">
                                                <div class="bg-white/95 backdrop-blur rounded-xl shadow-xl p-3 text-center border border-gray-100">
                                                    <p class="text-xs font-bold text-gray-900"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <form id="qr-form" action="{{ route('bimtek.absensi.scan-qr', [$bimtek->id, $sesi->id]) }}" method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="qr_code" id="qr_code_input">
                                        </form>

                                        <div class="flex gap-3">
                                            <button id="start-scan" type="button" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-xs uppercase tracking-wide transition shadow-sm">
                                                Mulai Pemindaian
                                            </button>
                                            <button id="stop-scan" type="button" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold text-xs uppercase tracking-wide transition shadow-sm hidden">
                                                Hentikan Kamera
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- PANEL 2: UPLOAD SCREENSHOT ONLINE --}}
                                <div x-show="method === 'online'" class="space-y-6" style="display: none;">
                                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-5">
                                        <h3 class="font-bold text-indigo-900 mb-2 text-xs uppercase tracking-wider">Ketentuan Kehadiran Online:</h3>
                                        <p class="text-xs text-indigo-800 font-medium leading-relaxed">
                                            Unggah tangkapan layar (*screenshot*) saat Anda aktif mengikuti ruang virtual (Zoom/Meet). Pastikan nama akun atau wajah Anda terlihat jelas pada bukti gambar.
                                        </p>
                                    </div>

                                    <form action="{{ route('bimtek.absensi.hadir-online', [$bimtek->id, $sesi->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">File Screenshot (JPG, PNG, maks 4MB)</label>
                                            <input type="file" name="bukti_hadir_online" accept="image/png,image/jpeg,image/jpg" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-xl">
                                            @error('bukti_hadir_online')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <button type="submit" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow">
                                            Kirim Bukti Kehadiran Online
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            {{-- JIKA KELAS OFFLINE MURNI (HANYA SCANNER QR) --}}
                            <div class="space-y-6">
                                <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5 shadow-inner shadow-blue-50">
                                    <h3 class="font-bold text-blue-900 mb-2 text-xs uppercase tracking-wider">Langkah Pemindaian:</h3>
                                    <ol class="text-xs text-blue-800 space-y-1.5 list-decimal list-inside font-semibold">
                                        <li>Berikan izin pembukaan <span class="text-blue-950 font-bold">Akses Kamera</span> pada browser Anda.</li>
                                        <li>Arahkan lensa kamera menghadap ke gambar <span class="text-blue-950 font-bold">QR Code</span> di proyektor.</li>
                                        <li>Sistem otomatis memproses verifikasi dan mengunci kehadiran Anda.</li>
                                    </ol>
                                </div>

                                <div class="space-y-4">
                                    <div class="relative bg-neutral-900 rounded-2xl overflow-hidden shadow-md border border-neutral-800" style="aspect-ratio: 1/1;">
                                        <div id="qr-reader" class="w-full h-full"></div>
                                        <div id="scan-status" class="absolute top-4 left-4 right-4 hidden z-20">
                                            <div class="bg-white/95 backdrop-blur rounded-xl shadow-xl p-3 text-center border border-gray-100">
                                                <p class="text-xs font-bold text-gray-900"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="qr-form" action="{{ route('bimtek.absensi.scan-qr', [$bimtek->id, $sesi->id]) }}" method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="qr_code" id="qr_code_input">
                                    </form>

                                    <div class="flex gap-3">
                                        <button id="start-scan" type="button" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-xs uppercase tracking-wide transition shadow-sm">
                                            Mulai Pemindaian
                                        </button>
                                        <button id="stop-scan" type="button" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold text-xs uppercase tracking-wide transition shadow-sm hidden">
                                            Hentikan Kamera
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @endif

                    {{-- Informasi Maklumat Keamanan Lokasi Kedinasan --}}
                    <div class="mt-6 bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-xs font-bold text-gray-900 uppercase tracking-wide">Maklumat Validasi Keamanan</p>
                                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
                                    Token sandi lembar QR Code presensi diperbarui secara dinamis oleh sistem dan hanya dapat divalidasi jika perangkat Anda terverifikasi dalam kegiatan resmi BBPMP Sumatera Barat.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Memuat Library html5-qrcode dari CDN Terpilih --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrCode = null;
        const startBtn = document.getElementById('start-scan');
        const stopBtn = document.getElementById('stop-scan');
        const qrReader = document.getElementById('qr-reader');
        const qrForm = document.getElementById('qr-form');
        const qrCodeInput = document.getElementById('qr_code_input');
        const scanStatus = document.getElementById('scan-status');

        function showStatus(message, type = 'info') {
            if (!scanStatus) return;
            const statusEl = scanStatus.querySelector('p');
            statusEl.textContent = message;
            scanStatus.classList.remove('hidden');
            
            if (type === 'success') {
                statusEl.parentElement.classList.remove('bg-white', 'bg-red-50');
                statusEl.parentElement.classList.add('bg-green-50', 'border-green-200');
                statusEl.classList.remove('text-gray-900', 'text-red-800');
                statusEl.classList.add('text-green-800');
            } else if (type === 'error') {
                statusEl.parentElement.classList.remove('bg-white', 'bg-green-50');
                statusEl.parentElement.classList.add('bg-red-50', 'border-red-200');
                statusEl.classList.remove('text-gray-900', 'text-green-800');
                statusEl.classList.add('text-red-800');
            }
            
            setTimeout(() => {
                scanStatus.classList.add('hidden');
            }, 3000);
        }

        function startScanning() {
            if (!document.getElementById('qr-reader')) return;
            html5QrCode = new Html5Qrcode("qr-reader");
            
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras.length > 1 ? cameras[1].id : cameras[0].id;
                    
                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        },
                        (decodedText, decodedResult) => {
                            showStatus('QR Code terverifikasi! Mengunci kehadiran...', 'success');
                            
                            html5QrCode.stop().then(() => {
                                qrCodeInput.value = decodedText;
                                qrForm.submit();
                            }).catch(err => {
                                console.error('Gagal menghentikan aliran kamera stream:', err);
                                qrCodeInput.value = decodedText;
                                qrForm.submit();
                            });
                        },
                        (errorMessage) => {
                            // Silent frame skip
                        }
                    ).then(() => {
                        if (startBtn) startBtn.classList.add('hidden');
                        if (stopBtn) stopBtn.classList.remove('hidden');
                        showStatus('Kamera Aktif. Silakan bidik pola kotak QR Code Panitia', 'info');
                    }).catch(err => {
                        showStatus('Gagal membuka aliran hardware kamera: ' + err, 'error');
                    });
                } else {
                    showStatus('Sistem tidak menemukan perangkat modul hardware kamera', 'error');
                }
            }).catch(err => {
                showStatus('Penolakan otorisasi pembukaan privasi kamera browser: ' + err, 'error');
            });
        }

        function stopScanning() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    if (startBtn) startBtn.classList.remove('hidden');
                    if (stopBtn) stopBtn.classList.add('hidden');
                    showStatus('Proses pemindaian kamera dinonaktifkan', 'info');
                }).catch(err => {
                    console.error('Scanner release object frame error:', err);
                });
            }
        }

        if (startBtn) startBtn.addEventListener('click', startScanning);
        if (stopBtn) stopBtn.addEventListener('click', stopScanning);

        @if(!$hasAttended)
            window.addEventListener('load', () => {
                setTimeout(() => {
                    startScanning();
                }, 500);
            });
        @endif
    </script>
</x-app-layout>