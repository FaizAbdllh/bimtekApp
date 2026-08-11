<x-app-layout>
    <x-slot name="header">
        Materi Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Navigasi Atas & Tombol Tambah --}}
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Kelas
                </a>

                @if($canManage)
                    <a href="{{ route('bimtek.materi.create', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload Materi Baru
                    </a>
                @endif
            </div>

            {{-- Ringkasan Informasi Ringkas Kegiatan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-5 bg-gray-50/50 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900 mb-1">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</h2>
                    <p class="text-xs text-gray-400 font-medium">
                        {{ $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('d M Y') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('d M Y') : '-') }}
                        @if($bimtek->tanggal_selesai_aktual && $bimtek->tanggal_mulai_aktual != $bimtek->tanggal_selesai_aktual)
                            - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                        @endif
                        <span class="mx-1.5 text-gray-300">•</span>
                        Aula: {{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- Main List Card Wrapper --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    @if(!$isVerified)
                        {{-- Proteksi Khusus Jika Peserta Belum Lulus Verifikasi Administrasi --}}
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-14 h-16 bg-gray-50 rounded-full mb-4 border border-gray-100 text-gray-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-1">Akses Materi Terkunci</h3>
                            <p class="text-xs text-gray-400 max-w-sm mx-auto font-medium leading-relaxed">
                                Mohon maaf, Anda wajib menyelesaikan proses unggah dokumen persyaratan (Surat Tugas) serta menunggu verifikasi sah panitia untuk dapat melihat bahan ajar kelas.
                            </p>
                        </div>
                    @else
                    
                    {{-- Navigasi Sistem Filter Tab (Alpine.js Terpadu) --}}
                    <div x-data="{ tab: 'semua' }">
                        <div class="border-b border-gray-200 mb-6">
                            {{-- 💡 PERBAIKAN 1: Navigasi diubah ke text-sm normal-case agar tidak berhimpitan ekstrem --}}
                            <nav class="flex space-x-6 -mb-px text-sm font-medium">
                                <button @click="tab = 'semua'" :class="tab === 'semua' ? 'border-primary-500 text-primary-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3.5 px-1 border-b-2 transition-all flex items-center gap-2">
                                    Semua 
                                    {{-- Badge Counter Bulat Proporsional Dinamis --}}
                                    <span :class="tab === 'semua' ? 'bg-primary-50 text-primary-600' : 'bg-gray-100 text-gray-500'" class="px-2 py-0.5 rounded-full text-[11px] font-bold transition-colors">
                                        {{ $bimtek->materis->count() }}
                                    </span>
                                </button>
                                
                                <button @click="tab = 'materi'" :class="tab === 'materi' ? 'border-primary-500 text-primary-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3.5 px-1 border-b-2 transition-all flex items-center gap-2">
                                    Materi 
                                    <span :class="tab === 'materi' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'" class="px-2 py-0.5 rounded-full text-[11px] font-bold transition-colors">
                                        {{ $bimtek->materis->where('tipe', 'materi')->count() }}
                                    </span>
                                </button>
                                
                                <button @click="tab = 'panduan'" :class="tab === 'panduan' ? 'border-primary-500 text-primary-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-3.5 px-1 border-b-2 transition-all flex items-center gap-2">
                                    Panduan 
                                    <span :class="tab === 'panduan' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500'" class="px-2 py-0.5 rounded-full text-[11px] font-bold transition-colors">
                                        {{ $bimtek->materis->where('tipe', 'panduan')->count() }}
                                    </span>
                                </button>
                            </nav>
                        </div>

                        @if($bimtek->materis->count() > 0)
                            <div class="space-y-3">
                                @foreach($bimtek->materis as $materi)
                                    @php
                                        $ext = strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION));
                                        $iconColor = match($ext) {
                                            'pdf' => 'text-red-500 bg-red-50 border-red-100/70',
                                            'doc', 'docx' => 'text-blue-500 bg-blue-50 border-blue-100/70',
                                            'ppt', 'pptx' => 'text-orange-500 bg-orange-50 border-orange-100/70',
                                            'xls', 'xlsx' => 'text-green-500 bg-green-50 border-green-100/70',
                                            default => 'text-gray-500 bg-gray-50 border-gray-200/70',
                                        };
                                        $canPreview = in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
                                    @endphp
                                    
                                    {{-- Baris Elemen List Berkas --}}
                                    <div x-show="tab === 'semua' || tab === '{{ $materi->tipe }}'" x-cloak
                                         class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl hover:bg-gray-50/40 hover:border-gray-200/80 hover:shadow-sm transition-all duration-200">
                                        <div class="flex items-center flex-1 min-w-0 mr-4 gap-4">
                                            {{-- 💡 PERBAIKAN 2: Representasi Ikon Dokumen Berbentuk Kotak Rounded Presisi --}}
                                            <div class="w-10 h-10 rounded-xl border flex items-center justify-center shrink-0 {{ $iconColor }} shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm font-bold text-gray-900 truncate tracking-tight leading-tight">{{ $materi->judul }}</h4>
                                                
                                                {{-- 💡 PERBAIKAN 3: Baris Sub-info Longgar (gap-x-2.5), normal-case, tidak bersaing dengan judul --}}
                                                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-gray-400 mt-1.5 font-medium">
                                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider {{ $materi->tipe === 'materi' ? 'bg-blue-50 border border-blue-100 text-blue-700' : 'bg-purple-50 border border-purple-100 text-purple-700' }}">
                                                        {{ $materi->tipe === 'materi' ? 'Materi' : 'Panduan' }}
                                                    </span>
                                                    <span class="text-gray-300 font-light">•</span>
                                                    <span class="text-gray-500 font-medium">{{ strtoupper($ext) }} Berkas</span>
                                                    <span class="text-gray-300 font-light">•</span>
                                                    <span class="font-mono text-[11px] text-gray-400/90">{{ $materi->created_at->format('d M Y, H:i') }} WIB</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Panel Baris Tombol Aksi Kendali Berkas --}}
                                        {{-- 💡 PERBAIKAN 4: Ikon aksi dibungkus background hover pad rounded yang empuk --}}
                                        <div class="flex items-center gap-1 shrink-0 text-gray-400">
                                            {{-- Tombol Introspeksi Preview --}}
                                            @if($canPreview)
                                                <a href="{{ route('bimtek.materi.preview', [$bimtek->id, $materi->id]) }}" 
                                                   target="_blank" rel="noopener"
                                                   class="p-2 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-colors duration-150"
                                                   title="Lihat Pratinjau">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            {{-- Tombol Unduh Berkas --}}
                                            <a href="{{ route('bimtek.materi.download', [$bimtek->id, $materi->id]) }}" 
                                               class="p-2 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors duration-150"
                                               title="Unduh Berkas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>

                                            {{-- Tombol Operasional Khusus (PIC / Tim Pokja Only) --}}
                                            @if($canManage)
                                                <a href="{{ route('bimtek.materi.edit', [$bimtek->id, $materi->id]) }}" 
                                                   class="p-2 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors duration-150"
                                                   title="Ubah Berkas">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>

                                                <form action="{{ route('bimtek.materi.destroy', [$bimtek->id, $materi->id]) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen dokumen materi pembelajaran ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors duration-150"
                                                            title="Hapus Permanen">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            {{-- Tampilan Keadaan Kosong (Empty State) --}}
                            <div class="text-center py-12 text-gray-400 font-medium">
                                <svg class="w-14 h-14 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-base font-bold text-gray-900 mb-0.5">Belum Ada Bahan Ajar</h3>
                                <p class="text-xs text-gray-400 mb-4">Modul atau lembar panduan penugasan belum dirilis oleh instruktur kelas.</p>
                                @if($canManage)
                                    <a href="{{ route('bimtek.materi.create', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-bold text-xs uppercase tracking-wide rounded-xl hover:bg-primary-700 transition shadow-sm">
                                        Rilis Berkas Pertama
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