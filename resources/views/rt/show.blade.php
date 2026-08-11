<x-app-layout>
    <x-slot name="header">
        Detail Kebutuhan Fasilitas
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('rt.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium">
                            Kebutuhan Fasilitas
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-medium">Detail Checklist</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner Indikator Logistik Rumah Tangga --}}
            @php
                $statusRtColors = [
                    'belum_dipenuhi' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
                    'sebagian_dipenuhi' => 'bg-blue-100 border-blue-400 text-blue-800',
                    'telah_dipenuhi' => 'bg-green-100 border-green-400 text-green-800',
                ];
                $statusRtLabels = [
                    'belum_dipenuhi' => 'Belum Dipenuhi',
                    'sebagian_dipenuhi' => 'Sebagian Dipenuhi',
                    'telah_dipenuhi' => 'Telah Dipenuhi Semua',
                ];
            @endphp
            <div class="mb-6 p-4 rounded-xl border-l-4 bg-white shadow-sm {{ $statusRtColors[$pengajuan->status_rt] ?? 'bg-gray-100 border-gray-400 text-gray-800' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-bold text-sm">Status Pemenuhan Logistik: {{ $statusRtLabels[$pengajuan->status_rt] ?? $pengajuan->status_rt }}</span>
                </div>
            </div>

            {{-- Info Kegiatan Utama --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $pengajuan->judul_final ?? $pengajuan->judul_rencana }}</h2>
                            {{-- REFAKTORISASI: Mengubah relasi user menjadi pic --}}
                            <p class="mt-1 text-sm text-gray-500 font-medium">Diajukan oleh Pokja / PIC: <span class="text-gray-700 font-semibold">{{ $pengajuan->pic->name ?? '-' }}</span></p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold w-fit {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $pengajuan->jenis_kegiatan === 'internal' ? 'Internal BBPMP' : 'Eksternal / Luar' }}
                        </span>
                    </div>
                </div>
                <div class="p-6 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tempat / Aula Pelaksanaan</h4>
                            {{-- REFAKTORISASI LOKASI: Menggunakan lokasi_aktual dengan fallback tempat_kegiatan_rencana --}}
                            <p class="mt-1 text-base font-semibold text-gray-800">{{ $pengajuan->lokasi_aktual ?? $pengajuan->tempat_kegiatan_rencana ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pelaksanaan Acara</h4>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ $pengajuan->tanggal_mulai_aktual ? $pengajuan->tanggal_mulai_aktual->format('d F Y') : ($pengajuan->tanggal_mulai_rencana ? $pengajuan->tanggal_mulai_rencana->format('d F Y') : '-') }}
                                @if($pengajuan->tanggal_selesai_aktual)
                                    - {{ $pengajuan->tanggal_selesai_aktual->format('d F Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($pengajuan->catatan_logistik)
                        <div class="mt-4 pt-4 border-t border-gray-100 bg-amber-50/40 p-4 rounded-xl border border-amber-100 text-amber-900">
                            <span class="text-xs font-bold uppercase tracking-wider block mb-1">Catatan / Instruksi Khusus dari PIC Pokja:</span>
                            <p class="text-sm font-medium leading-relaxed whitespace-pre-line">{{ $pengajuan->catatan_logistik }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Daftar Check-List Prasarana Fasilitas & Logistik --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Daftar Kebutuhan Fasilitas Kerja Lapangan
                    </h3>

                    @if($pengajuan->fasilitasLogistiks->count() > 0)
                        {{-- REFAKTORISASI: Menyematkan ID pada parameter route update untuk kestabilan UUID --}}
                        <form action="{{ route('rt.update', $pengajuan->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider">
                                        <tr>
                                            <th class="px-4 py-3 text-center w-16">
                                                <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                            </th>
                                            <th class="px-4 py-3 text-center w-12">No</th>
                                            <th class="px-4 py-3 text-left">Nama Prasarana Fasilitas</th>
                                            <th class="px-4 py-3 text-center w-28">Volume</th>
                                            <th class="px-4 py-3 text-center w-36">Status Kesiapan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                        @foreach($pengajuan->fasilitasLogistiks as $index => $fasilitas)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-4 py-3 text-center">
                                                    {{-- REFAKTORISASI: Penanda centang diselaraskan jika status barang adalah 'tersedia' --}}
                                                    <input type="checkbox" 
                                                        name="fasilitas_dipenuhi[]" 
                                                        value="{{ $fasilitas->id }}"
                                                        {{ $fasilitas->status === 'tersedia' ? 'checked' : '' }}
                                                        class="fasilitas-check rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $fasilitas->nama_fasilitas }}</td>
                                                <td class="px-4 py-3 text-center font-bold text-gray-800 bg-gray-50/40">{{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? 'Unit' }}</td>
                                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                                    {{-- REFAKTORISASI: Tampilan badge mengacu pada standar enum logistik baru --}}
                                                    @if($fasilitas->status === 'tersedia')
                                                        <span class="px-2.5 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-bold">Siap / Tersedia</span>
                                                    @elseif($fasilitas->status === 'tidak_tersedia')
                                                        <span class="px-2.5 py-0.5 bg-red-100 text-red-800 rounded-full text-xs font-bold">Kosong / Rusak</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">Belum Diperiksa</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Catatan Balik / Feedback dari Unit Kerja RT --}}
                            <div class="mt-5">
                                <label for="catatan_rt" class="block text-sm font-semibold text-gray-700 mb-2">Lembar Catatan / Feedback Kendala Lapangan Unit RT</label>
                                <textarea name="catatan_rt" id="catatan_rt" rows="3" 
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    placeholder="Tuliskan catatan fisik penataan ruangan atau laporan jika ada sarana logistik yang rusak/kosong di sini...">{{ old('catatan_rt', $pengajuan->catatan_rt) }}</textarea>
                            </div>

                            {{-- Tombol Kendali Form --}}
                            <div class="mt-6 border-t border-gray-100 pt-4 flex justify-between items-center">
                                <a href="{{ route('rt.index') }}" class="inline-flex items-center text-xs font-bold text-gray-600 hover:text-gray-900 transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Kembali ke Daftar
                                </a>
                                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white font-bold text-xs uppercase tracking-wide rounded-xl hover:bg-primary-700 transition shadow-sm">
                                    Simpan Perubahan Checklist
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-4 text-sm font-semibold text-gray-500">Tidak melampirkan draf kebutuhan prasarana gedung Rumah Tangga.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Script Pembantu Centang Semua Logistik --}}
    @push('scripts')
    <script>
        document.getElementById('checkAll')?.addEventListener('change', function() {
            document.querySelectorAll('.fasilitas-check').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
    @endpush
</x-app-layout>