<x-app-layout>
    <x-slot name="header">
        Tugas Pembelajaran Kelas
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Navigasi Atas & Tombol Rilis --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ route('bimtek.show', $bimtek->id) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                   <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg> 
                   Kembali ke Kelas
                </a>

                @if($canManage)
                    <a href="{{ route('bimtek.tugas.create', $bimtek->id) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 hover:bg-primary-800 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Buat Tugas Baru
                    </a>
                @endif
            </div>

            {{-- Ringkasan Informasi Kelas Terkait --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-1">
                        {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
                    </h2>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                        {{ $bimtek->tugas->count() }} Modul Tugas Terjadwal
                    </p>
                </div>
            </div>

            {{-- Blok Kondisi Pemeriksaan Hak Akses Verifikasi Dokumen --}}
            @if(!$isVerified)
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 px-6 py-8 text-center">
                    <svg class="h-12 w-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h3 class="mt-2 text-base font-bold text-gray-800">
                        Akses Penugasan Terkunci
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                        Mohon maaf, Anda wajib menyelesaikan proses unggah berkas persyaratan administrasi (Surat Tugas) serta menunggu konfirmasi panitia untuk dapat membuka lembar kerja.
                    </p>
                </div>
            @else

                {{-- Iterasi Daftar Tugas Aktif --}}
                @if($bimtek->tugas->count() > 0)
                    <div class="space-y-6">
                        @foreach($bimtek->tugas as $tugas)
                            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">

                                        {{-- Sisi Kiri: Detail Informasi Tugas --}}
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}"
                                               class="text-lg font-bold text-gray-800 hover:text-primary-800 transition-colors block truncate">
                                                {{ $tugas->judul }}
                                            </a>

                                            @if($tugas->deskripsi)
                                                <p class="mt-1 text-sm text-gray-500 leading-relaxed">
                                                    {{ Str::limit($tugas->deskripsi, 150) }}
                                                </p>
                                            @endif

                                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                                                {{-- Batas Waktu --}}
                                                <div class="{{ $tugas->isDeadlinePassed() ? 'text-red-600' : 'text-gray-500' }}">
                                                    <span class="font-medium">
                                                        Batas: {{ $tugas->deadline->format('d M Y, H:i') }} WIB
                                                    </span>

                                                    @if($tugas->isDeadlinePassed())
                                                        <span class="ml-1 text-xs font-semibold text-red-600">
                                                            (Selesai)
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Jumlah Pengumpulan Akumulatif --}}
                                                <div class="whitespace-nowrap">
                                                    {{ $tugas->pengumpulanTugas->count() }} Berkas Masuk
                                                </div>

                                                {{-- File Penunjang --}}
                                                @if($tugas->file_instruksi_path)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800">
                                                        Template Lampiran Aktif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Sisi Kanan: Status Evaluasi & Navigasi Aksi --}}
                                        <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end gap-3 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 border-gray-200">

                                            {{-- Penunjuk Rekam Evaluasi Khusus Aktor Peserta --}}
                                            @if($isPeserta)
                                                @php
                                                    $submission = $userSubmissions->get($tugas->id);
                                                @endphp

                                                @if($submission)
                                                    @if($submission->nilai !== null)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                            Skor: {{ $submission->nilai }} / 100
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                            Terkumpul (Review)
                                                        </span>
                                                    @endif
                                                @else
                                                    @if($tugas->isDeadlinePassed())
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                            Kosong (Gugur)
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                            Belum Mengisi
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif

                                            {{-- Tombol Tindakan Operasional --}}
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}"
                                                   class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                    Buka
                                                </a>

                                                @if($canManage)
                                                    <a href="{{ route('bimtek.tugas.edit', [$bimtek->id, $tugas->id]) }}"
                                                       class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 hover:bg-primary-800 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                        Koreksi
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
                    {{-- Komponen Layar Kosong (Empty State) --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 px-6 py-8 text-center">
                        <svg class="h-12 w-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-base font-bold text-gray-800">
                            Belum Ada Lembar Kerja
                        </h3>

                        <p class="mt-1 mb-4 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                            Modul penilaian atau tugas pengayaan belum diterbitkan oleh instruktur pengajar kelas.
                        </p>

                        @if($canManage)
                            <a href="{{ route('bimtek.tugas.create', $bimtek->id) }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 hover:bg-primary-800 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Rilis Tugas Pertama
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>