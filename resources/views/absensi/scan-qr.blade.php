<x-app-layout>
    <x-slot name="header">
        Scan QR Code Absensi
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $sesi->nama_sesi }}</h2>
                        <p class="text-sm text-gray-600">{{ $bimtek->judul_final }}</p>
                    </div>

                    @if($hasAttended)
                        {{-- Already Attended --}}
                        <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-green-800 font-semibold mb-2">Anda Sudah Tercatat Hadir</p>
                            <p class="text-sm text-green-600">Kehadiran Anda sudah terekam untuk sesi ini</p>
                        </div>
                    @else
                        {{-- Scan Instructions --}}
                        <div class="mb-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h3 class="font-semibold text-blue-900 mb-2">Instruksi:</h3>
                                <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                                    <li>Izinkan akses kamera pada browser Anda</li>
                                    <li>Arahkan kamera ke QR Code yang ditampilkan panitia</li>
                                    <li>Tunggu hingga QR Code berhasil di-scan</li>
                                    <li>Sistem akan otomatis mencatat kehadiran Anda</li>
                                </ol>
                            </div>

                            {{-- QR Scanner --}}
                            <div class="space-y-4">
                                {{-- Camera Preview --}}
                                <div class="relative bg-black rounded-lg overflow-hidden" style="aspect-ratio: 1/1;">
                                    <div id="qr-reader" class="w-full h-full"></div>
                                    <div id="scan-status" class="absolute top-4 left-4 right-4 hidden">
                                        <div class="bg-white rounded-lg shadow-lg p-3 text-center">
                                            <p class="text-sm font-medium text-gray-900"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden Form --}}
                                <form id="qr-form" action="{{ route('bimtek.absensi.scan-qr', [$bimtek, $sesi]) }}" method="POST" class="hidden">
                                    @csrf
                                    <input type="hidden" name="qr_code" id="qr_code_input">
                                </form>

                                {{-- Camera Controls --}}
                                <div class="flex gap-3">
                                    <button id="start-scan" type="button" class="flex-1 px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Mulai Scan
                                    </button>
                                    <button id="stop-scan" type="button" class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium hidden">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Hentikan Scan
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Catatan Keamanan</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    QR Code hanya berlaku selama 10 menit dan hanya dapat digunakan saat berada di lokasi kegiatan. 
                                    Pastikan Anda berada di ruangan saat melakukan scan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include html5-qrcode library --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrCode = null;
        const startBtn = document.getElementById('start-scan');
        const stopBtn = document.getElementById('stop-scan');
        const qrReader = document.getElementById('qr-reader');
        const qrForm = document.getElementById('qr-form');
        const qrCodeInput = document.getElementById('qr_code_input');
        const scanStatus = document.getElementById('scan-status');

        // Function to show status message
        function showStatus(message, type = 'info') {
            const statusEl = scanStatus.querySelector('p');
            statusEl.textContent = message;
            scanStatus.classList.remove('hidden');
            
            if (type === 'success') {
                statusEl.parentElement.classList.remove('bg-white');
                statusEl.parentElement.classList.add('bg-green-50');
                statusEl.classList.remove('text-gray-900');
                statusEl.classList.add('text-green-800');
            } else if (type === 'error') {
                statusEl.parentElement.classList.remove('bg-white');
                statusEl.parentElement.classList.add('bg-red-50');
                statusEl.classList.remove('text-gray-900');
                statusEl.classList.add('text-red-800');
            }
            
            setTimeout(() => {
                scanStatus.classList.add('hidden');
            }, 3000);
        }

        // Function to start scanning
        function startScanning() {
            html5QrCode = new Html5Qrcode("qr-reader");
            
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    // Use back camera if available (better for QR scanning)
                    const cameraId = cameras.length > 1 ? cameras[1].id : cameras[0].id;
                    
                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        },
                        (decodedText, decodedResult) => {
                            // QR Code successfully scanned
                            showStatus('QR Code berhasil di-scan! Mencatat kehadiran...', 'success');
                            
                            // Stop scanning
                            html5QrCode.stop().then(() => {
                                // Set the scanned value and submit form
                                qrCodeInput.value = decodedText;
                                qrForm.submit();
                            }).catch(err => {
                                console.error('Error stopping scanner:', err);
                                qrCodeInput.value = decodedText;
                                qrForm.submit();
                            });
                        },
                        (errorMessage) => {
                            // Scanning failed (no QR detected) - this is normal, keep scanning
                        }
                    ).then(() => {
                        startBtn.classList.add('hidden');
                        stopBtn.classList.remove('hidden');
                        showStatus('Scanning... Arahkan kamera ke QR Code', 'info');
                    }).catch(err => {
                        showStatus('Gagal memulai kamera: ' + err, 'error');
                        console.error('Camera start error:', err);
                    });
                } else {
                    showStatus('Tidak ada kamera ditemukan', 'error');
                }
            }).catch(err => {
                showStatus('Gagal mengakses kamera: ' + err, 'error');
                console.error('Camera access error:', err);
            });
        }

        // Function to stop scanning
        function stopScanning() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    startBtn.classList.remove('hidden');
                    stopBtn.classList.add('hidden');
                    showStatus('Scanning dihentikan', 'info');
                }).catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        }

        // Event listeners
        startBtn.addEventListener('click', startScanning);
        stopBtn.addEventListener('click', stopScanning);

        // Auto-start scanning when page loads (if not already attended)
        @if(!$hasAttended)
            window.addEventListener('load', () => {
                setTimeout(() => {
                    startScanning();
                }, 500);
            });
        @endif
    </script>
</x-app-layout>
