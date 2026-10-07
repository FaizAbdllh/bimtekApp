<x-app-layout>
    <x-slot name="header">
        Verifikasi Dokumen Peserta - {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Header & Navigasi --}}
            <div class="mb-6">
                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dasbor Kelas
                </a>
                <div class="mt-4">
                    <h2 class="text-lg font-bold text-gray-800">Meja Pemeriksaan Berkas Syarat Peserta</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                </div>
            </div>

            {{-- Summary Stats Widgets --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @php
                    $totalPeserta = $pesertaList->count();
                    $pending = $pesertaList->where('status_verifikasi', 'pending')->count();
                    $verified = $pesertaList->where('status_verifikasi', 'verified')->count();
                    $rejected = $pesertaList->where('status_verifikasi', 'rejected')->count();
                @endphp
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Anggota</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $totalPeserta }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Menunggu Tinjauan</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $pending }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Berkas Sah</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $verified }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ditolak / Gugur</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $rejected }}</p>
                </div>
            </div>

            {{-- Tabel Indikator Status Utama --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-center w-12 text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Informasi Anggota</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Kelulusan</th>
                                
                                {{-- Kolom Header Dinamis Berdasarkan Dokumen Syarat --}}
                                @foreach($bimtek->syaratDokumens as $syarat)
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        {{ Str::limit($syarat->nama_dokumen, 20) }}
                                    </th>
                                @endforeach
                                
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Panel Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700">
                            @forelse($pesertaList as $index => $item)
                                <tr class="hover:bg-gray-100 transition-colors group">
                                    <td class="px-6 py-4 text-center text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-900">
                                        <span class="block font-medium">{{ $item['user']->name ?? '-' }}</span>
                                        <span class="block text-sm font-normal text-gray-500 mt-0.5">{{ $item['user']->email ?? '-' }}</span>
                                    </td>
                                    
                                    {{-- Status Makro Kelulusan Peserta --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($item['status_verifikasi'] === 'invited')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Belum Unggah</span>
                                        @elseif($item['status_verifikasi'] === 'pending')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Menunggu</span>
                                        @elseif($item['status_verifikasi'] === 'verified')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Sah / Lulus</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Ditolak</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Status Mikro Lencana (Badge) per Dokumen --}}
                                    @foreach($bimtek->syaratDokumens as $syarat)
                                        @php
                                            $dokumen = $item['user']->dokumenPersyaratan
                                                ->where('bimtek_id', $bimtek->id)
                                                ->where('syarat_dokumen_id', $syarat->id)
                                                ->first();
                                        @endphp
                                        <td class="px-6 py-4 text-center align-middle whitespace-nowrap">
                                            @if($dokumen)
                                                @if($dokumen->status === 'pending')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Tinjau</span>
                                                @elseif($dokumen->status === 'approved')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Diterima</span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Ditolak</span>
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    {{-- Tombol Aksi Tunggal Terpusat --}}
                                    <td class="px-6 py-4 text-right align-middle">
                                        <button type="button" x-data @click="$dispatch('open-modal', 'panel-verifikasi-{{ $item['user']->id }}')" 
                                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                            
                                            Periksa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    @php $colspan = 4 + $bimtek->syaratDokumens->count(); @endphp
                                    <td colspan="{{ $colspan }}" class="px-6 py-8 text-center">
                                        <svg class="h-12 w-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="mt-2 text-base font-bold text-gray-800">Belum Ada Transmisi Dokumen</p>
                                        <p class="mt-1 text-sm text-gray-500">Berkas persyaratan peserta akan muncul di sini untuk ditinjau.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>    
                </div>
            </div>
        </div>
    </div>

    {{-- MODUL EKSEKUSI: PERBAIKAN maxWidth="2xl" --}}
    @foreach($pesertaList as $item)
        <x-modal name="panel-verifikasi-{{ $item['user']->id }}" :show="false" maxWidth="2xl">
            <div class="bg-gray-50 overflow-hidden rounded-xl">
                
                {{-- Modal Header --}}
                <div class="bg-white px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 z-10">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Panel Tinjauan Kelayakan Dokumen</h2>
                        <p class="text-sm text-gray-500 mt-1">Pemohon: <span class="font-medium text-gray-900">{{ $item['user']->name }}</span></p>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" title="Tutup" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body: Grid Ruang Kerja Dokumen --}}
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                    @foreach($bimtek->syaratDokumens as $syarat)
                        @php
                            $dokumen = $item['user']->dokumenPersyaratan
                                ->where('bimtek_id', $bimtek->id)
                                ->where('syarat_dokumen_id', $syarat->id)
                                ->first();
                        @endphp
                        
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden flex flex-col sm:flex-row">
                            
                            {{-- Sisi Kiri: Identitas Dokumen & Penampil Berkas --}}
                            <div class="flex-1 p-5 border-b sm:border-b-0 sm:border-r border-gray-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-bold text-gray-800">{{ $syarat->nama_dokumen }}</h4>
                                        @if($dokumen)
                                            <p class="text-sm text-gray-500 mt-1 truncate max-w-[12rem] sm:max-w-xs">{{ basename($dokumen->file_name ?? $dokumen->file_path) }}</p>
                                        @else
                                            <p class="text-sm text-red-600 mt-1">⚠ Dokumen belum diunggah.</p>
                                        @endif
                                    </div>
                                    @if($dokumen)
                                        @if($dokumen->status === 'pending')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Tinjau</span>
                                        @elseif($dokumen->status === 'approved')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Diterima</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Ditolak</span>
                                        @endif
                                    @endif
                                </div>

                                @if($dokumen)
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('bimtek.verifikasi-dokumen.preview', ['bimtek' => $bimtek->id, 'userId' => $item['user']->id, 'syaratId' => $syarat->id]) }}" 
                                           target="_blank" rel="noopener" 
                                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Pratinjau PDF
                                        </a>
                                        <a href="{{ route('bimtek.verifikasi-dokumen.download', ['bimtek' => $bimtek->id, 'userId' => $item['user']->id, 'syaratId' => $syarat->id]) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Unduh
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Sisi Kanan: Ruang Eksekusi Keputusan --}}
                            <div class="sm:w-64 bg-gray-50 p-5 flex flex-col justify-center relative">
                                @if($dokumen && $dokumen->status === 'pending')
                                    <div x-data="{ mode: 'idle' }" class="w-full">
                                        
                                        <div x-show="mode === 'idle'" class="flex flex-col gap-2">
                                            <form action="{{ route('bimtek.verifikasi-dokumen.approve', ['bimtek' => $bimtek->id, 'userId' => $item['user']->id, 'syaratId' => $syarat->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                    Sahkan
                                                </button>
                                            </form>
                                            <button type="button" @click="mode = 'reject'" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                Tolak
                                            </button>
                                        </div>

                                        <div x-show="mode === 'reject'" style="display: none;" class="flex flex-col gap-2">
                                            <form action="{{ route('bimtek.verifikasi-dokumen.reject', ['bimtek' => $bimtek->id, 'userId' => $item['user']->id, 'syaratId' => $syarat->id]) }}" method="POST" class="flex flex-col gap-2">
                                                @csrf
                                                <label class="text-sm font-semibold text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                                <textarea name="catatan_verifikasi" required rows="2" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Detail alasan..."></textarea>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="mode = 'idle'" class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Batal</button>
                                                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">Tolak</button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                @elseif($dokumen && $dokumen->status !== 'pending')
                                    <div class="text-center">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Status Keputusan</p>
                                        <p class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $dokumen->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $dokumen->status === 'approved' ? 'DISAHKAN' : 'DITOLAK' }}
                                        </p>
                                        @if($dokumen->catatan_verifikasi)
                                            <p class="text-sm leading-relaxed text-left text-gray-700 mt-3 bg-white p-4 rounded-lg border border-gray-200">"{{ Str::limit($dokumen->catatan_verifikasi, 50) }}"</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500">Menunggu Peserta</p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </x-modal>
    @endforeach
</x-app-layout>