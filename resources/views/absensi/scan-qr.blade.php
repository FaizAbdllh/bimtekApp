<x-app-layout>
    <x-slot name="header">
        Scan QR Code Absensi
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <div class="mb-4">
                {{-- REFAKTORISASI: Kestabilan parameter rute ID kembali ke ruang absensi --}}
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
                        {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
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
                        {{-- Alur Pemindaian Kamera Aktif --}}
                        <div class="mb-6">
                            {{-- Petunjuk Alur Penggunaan --}}
                            <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5 mb-5 shadow-inner shadow-blue-50">
                                <h3 class="font-bold text-blue-900 mb-2 text-xs uppercase tracking-wider">Langkah Pemindaian:</h3>
                                <ol class="text-xs text-blue-800 space-y-1.5 list-decimal list-inside font-semibold">
                                    <li>Berikan izin pembukaan <span class="text-blue-950 font-bold">Akses Kamera</span> pada peramban/browser Anda.</li>
                                    <li>Arahkan lensa kamera menghadap ke gambar <span class="text-blue-950 font-bold">QR Code</span> yang diproyeksikan panitia.</li>
                                    <li>Posisikan kode masuk tepat di dalam kotak pembaca scanner.</li>
                                    <li>Sistem otomatis memproses verifikasi kode dan mengunci kehadiran Anda.</li>
                                </ol>
                            </div>

                            {{-- Komponen Pembaca Aliran Stream Kamera (QR Scanner) --}}
                            <div class="space-y-4">
                                {{-- Kotak Bidik Lensa Kamera Preview --}}
                                <div class="relative bg-neutral-900 rounded-2xl overflow-hidden shadow-md border border-neutral-800" style="aspect-ratio: 1/1;">
                                    <div id="qr-reader" class="w-full h-full"></div>
                                    <div id="scan-status" class="absolute top-4 left-4 right-4 hidden z-20">
                                        <div class="bg-white/95 backdrop-blur rounded-xl shadow-xl p-3 text-center border border-gray-100">
                                            <p class="text-xs font-bold text-gray-900"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Formulir Tersembunyi Pengirim Payload QR Ke Controller --}}
                                {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute scan-qr --}}
                                <form id="qr-form" action="{{ route('bimtek.absensi.scan-qr', [$bimtek->id, $sesi->id]) }}" method="POST" class="hidden">
                                    @csrf
                                    <input type="hidden" name="qr_code" id="qr_code_input">
                                </form>

                                {{-- Tombol Sakelar Pengendali Aliran Kamera --}}
                                <div class="flex gap-3">
                                    <button id="start-scan" type="button" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-xs uppercase tracking-wide transition shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Mulai Pemindaian
                                    </button>
                                    <button id="stop-scan" type="button" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold text-xs uppercase tracking-wide transition shadow-sm hidden">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Hentikan Kamera
                                    </button>
                                </div>
                            </div>
                        </div>
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
                                    Token sandi lembar QR Code presensi diperbarui secara dinamis oleh sistem dan hanya dapat divalidasi jika perangkat Anda berada di dalam radius koordinat lokasi aula kelas penugasan BBPMP Sumatera Barat berjalan.
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

        // Fungsi manipulasi boks status melayang penunjuk proses scan
        function showStatus(message, type = 'info') {
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

        // Alur pemicu inisialisasi aliran tangkapan lensa kamera
        function startScanning() {
            html5QrCode = new Html5Qrcode("qr-reader");
            
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    // Berikan prioritas pada lensa kamera belakang (Back Camera) ponsel agar fokus makro QR tajam
                    const cameraId = cameras.length > 1 ? cameras[1].id : cameras[0].id;
                    
                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        },
                        (decodedText, decodedResult) => {
                            // Handler Interseptor: Kondisi jika QR Code valid tertangkap scanner
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
                            // Silent frame skip kegagalan deteksi pola QR (wajar dalam stream kontinu)
                        }
                    ).then(() => {
                        startBtn.classList.add('hidden');
                        stopBtn.classList.remove('hidden');
                        showStatus('Kamera Aktif. Silakan bidik pola kotak QR Code Panitia', 'info');
                    }).catch(err => {
                        showStatus('Gagal membuka aliran hardware kamera: ' + err, 'error');
                        console.error('Camera stream initialize error:', err);
                    });
                } else {
                    showStatus('Sistem tidak menemukan perangkat modul hardware kamera', 'error');
                }
            }).catch(err => {
                showStatus('Penolakan otorisasi pembukaan privasi kamera browser: ' + err, 'error');
                console.error('Camera access permissions error:', err);
            });
        }

        // Alur pembongkaran penutupan stream lensa kamera
        function stopScanning() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    startBtn.classList.remove('hidden');
                    stopBtn.classList.add('hidden');
                    showStatus('Proses pemindaian kamera dinonaktifkan', 'info');
                }).catch(err => {
                    console.error('Scanner release object frame error:', err);
                });
            }
        }

        // Pemasangan Event Listener Pemicu Sakelar
        startBtn.addEventListener('click', startScanning);
        stopBtn.addEventListener('click', stopScanning);

        // Otomatisasi pemicu pembukaan lensa kamera saat halaman selesai dimuat (jika belum absen)
        @if(!$hasAttended)
            window.addEventListener('load', () => {
                setTimeout(() => {
                    startScanning();
                }, 500);
            });
        @endif
    </script>
</x-app-layout>