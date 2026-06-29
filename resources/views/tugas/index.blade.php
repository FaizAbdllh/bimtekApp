<x-app-layout>
    <x-slot name="header">
        Tugas Pembelajaran Kelas
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Navigasi Atas & Tombol Rilis --}}
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                {{-- REFAKTORISASI: Kestabilan UUID rute kembali ke detail kelas --}}
                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Kelas
                </a>

                @if($canManage)
                    {{-- REFAKTORISASI: Kestabilan UUID rute tambah tugas baru --}}
                    <a href="{{ route('bimtek.tugas.create', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Tugas Baru
                    </a>
                @endif
            </div>

            {{-- Ringkasan Informasi Kelas Terkait --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-5 bg-gray-50/50 border-b border-gray-100">
                    {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                    <h2 class="text-base font-bold text-gray-900 mb-0.5">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</h2>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">{{ $bimtek->tugas->count() }} Modul Tugas Terjadwal</p>
                </div>
            </div>

            {{-- Blok Kondisi Pemeriksaan Hak Akses Verifikasi Dokumen --}}
            @if(!$isVerified)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-16 bg-gray-50 rounded-full mb-4 border border-gray-100 text-gray-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Akses Penugasan Terkunci</h3>
                    <p class="text-xs text-gray-400 max-w-sm mx-auto font-medium leading-relaxed">
                        Mohon maaf, Anda wajib menyelesaikan proses unggah berkas persyaratan administrasi (Surat Tugas) serta menunggu konfirmasi panitia untuk dapat membuka lembar lembar kerja.
                    </p>
                </div>
            @else
            
                {{-- Iterasi Daftar Tugas Aktif --}}
                @if($bimtek->tugas->count() > 0)
                    <div class="space-y-4">
                        @foreach($bimtek->tugas as $tugas)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-gray-200/80 transition-all duration-200">
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                        
                                        {{-- Sisi Kiri: Detail Informasi Tugas --}}
                                        <div class="flex-1 flex items-start gap-4 min-w-0">
                                            <div class="p-3 bg-primary-50 text-primary-600 rounded-xl shrink-0 border border-primary-100/40">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                {{-- REFAKTORISASI: Kestabilan ID parameter rute detail --}}
                                                <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="text-base font-bold text-gray-900 hover:text-primary-600 transition-colors block truncate">
                                                    {{ $tugas->judul }}
                                                </a>
                                                @if($tugas->deskripsi)
                                                    <p class="mt-1 text-xs text-gray-500 font-medium line-clamp-2 leading-relaxed">{{ Str::limit($tugas->deskripsi, 150) }}</p>
                                                @endif
                                                
                                                <div class="mt-3.5 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-gray-400 font-semibold">
                                                    {{-- Batas Waktu --}}
                                                    <div class="flex items-center gap-1.5 {{ $tugas->isDeadlinePassed() ? 'text-red-500' : 'text-gray-400' }}">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span>Batas: {{ $tugas->deadline->format('d M Y, H:i') }} WIB 
                                                            @if($tugas->isDeadlinePassed())
                                                                <span class="text-[10px] uppercase font-bold bg-red-50 px-1.5 py-0.5 rounded border border-red-100/60 ml-0.5">(Selesai)</span>
                                                            @endif
                                                        </span>
                                                    </div>

                                                    {{-- Jumlah Pengumpulan Akumulatif --}}
                                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                        <span>{{ $tugas->pengumpulanTugas->count() }} Berkas Masuk</span>
                                                    </div>

                                                    {{-- File Penunjang --}}
                                                    @if($tugas->file_instruksi_path)
                                                        <div class="flex items-center gap-1 text-purple-600 font-bold bg-purple-50 border border-purple-100 rounded px-1.5 py-0.5 text-[10px] uppercase tracking-wide">
                                                            Template Lampiran Aktif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Sisi Kanan: Status Evaluasi & Navigasi Aksi --}}
                                        <div class="flex flex-row sm:flex-row lg:flex-col items-end justify-between lg:justify-start gap-3 shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-gray-50">
                                            {{-- Penunjuk Rekam Evaluasi Khusus Aktor Peserta --}}
                                            @if($isPeserta)
                                                @php
                                                    $submission = $userSubmissions->get($tugas->id);
                                                @endphp
                                                @if($submission)
                                                    @if($submission->nilai !== null)
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-green-50 border border-green-200 text-green-700">
                                                            ✓ Skor: {{ $submission->nilai }} / 100
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 border border-blue-200 text-blue-700">
                                                            Terkumpul (Review)
                                                        </span>
                                                    @endif
                                                @else
                                                    @if($tugas->isDeadlinePassed())
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 border border-red-200 text-red-700">
                                                            Kosong (Gugur)
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-50 border border-yellow-200 text-yellow-700">
                                                            Belum Mengisi
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif

                                            {{-- Tombol Tindakan Operasional --}}
                                            <div class="flex items-center gap-1.5 font-bold text-xs uppercase tracking-wide">
                                                {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute show --}}
                                                <a href="{{ route('bimtek.tugas.show', [$bimtek->id, $tugas->id]) }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                                                    Buka
                                                </a>
                                                @if($canManage)
                                                    {{-- REFAKTORISASI: Kestabilan array binding ID parameter rute edit --}}
                                                    <a href="{{ route('bimtek.tugas.edit', [$bimtek->id, $tugas->id]) }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-amber-600 rounded-xl hover:bg-amber-50 transition shadow-sm">
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
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-400 font-medium">
                        <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-0.5">Belum Ada Lembar Kerja</h3>
                        <p class="text-xs text-gray-400 mb-4 max-w-xs mx-auto">Modul penilaian atau tugas pengayaan belum diterbitkan oleh instruktur pengajar kelas.</p>
                        @if($canManage)
                            {{-- REFAKTORISASI: Penyelarasan ID parameter rute perilisan tugas pertama --}}
                            <a href="{{ route('bimtek.tugas.create', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-bold text-xs uppercase tracking-wide rounded-xl hover:bg-primary-700 transition shadow-sm">
                                Rilis Tugas Pertama
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>