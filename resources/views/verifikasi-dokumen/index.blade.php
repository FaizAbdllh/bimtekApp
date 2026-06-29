<x-app-layout>
    <x-slot name="header">
        Verifikasi Dokumen Peserta - {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header & Navigasi --}}
            <div class="mb-6">
                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Kelas
                </a>
                <div class="mt-4">
                    <h2 class="text-2xl font-bold text-gray-900">Meja Pemeriksaan Berkas Syarat Peserta</h2>
                    <p class="text-sm text-gray-500 mt-1 font-medium">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
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
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPeserta }}</p>
                </div>
                <div class="bg-yellow-50/60 p-4 rounded-xl shadow-sm border border-yellow-100">
                    <p class="text-xs font-bold text-yellow-600 uppercase tracking-wider">Menunggu Peninjauan</p>
                    <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $pending }}</p>
                </div>
                <div class="bg-green-50/60 p-4 rounded-xl shadow-sm border border-green-100">
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Berkas Sah (Verified)</p>
                    <p class="text-2xl font-bold text-green-700 mt-1">{{ $verified }}</p>
                </div>
                <div class="bg-red-50/60 p-4 rounded-xl shadow-sm border border-red-100">
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Ditolak / Gugur</p>
                    <p class="text-2xl font-bold text-red-700 mt-1">{{ $rejected }}</p>
                </div>
            </div>

            {{-- Peserta Datatable Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 text-center w-12">No</th>
                                    <th class="px-4 py-3 text-left pl-6">Nama Lengkap Anggota</th>
                                    <th class="px-4 py-3 text-center w-36">Status Kelulusan</th>
                                    @foreach($jenisDokumenWajib as $jenis)
                                        <th class="px-4 py-3 text-center">
                                            {{ \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title() }}
                                        </th>
                                    @endforeach
                                    <th class="px-4 py-3 text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                @forelse($pesertaList as $index => $item)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-3 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 pl-6 font-bold text-gray-900">
                                            {{ $item['user']->name ?? '-' }}
                                            <span class="block text-[10px] text-gray-400 font-medium mt-0.5">{{ $item['user']->email ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @if($item['status_verifikasi'] === 'invited')
                                                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">Belum Unggah</span>
                                            @elseif($item['status_verifikasi'] === 'pending')
                                                <span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold animate-pulse">Menunggu</span>
                                            @elseif($item['status_verifikasi'] === 'verified')
                                                <span class="px-2.5 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-bold">✓ Sah (Verified)</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-red-100 text-red-800 rounded-full text-xs font-bold">✗ Ditolak</span>
                                            @endif
                                        </td>
                                        @foreach($jenisDokumenWajib as $jenis)
                                            @php
                                                $dokumen = $item['dokumen'][$jenis] ?? null;
                                                $label = \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title();
                                            @endphp
                                            <td class="px-4 py-3 text-center">
                                                @if($dokumen)
                                                    <div class="flex flex-col items-center gap-1.5">
                                                        @if($dokumen->status === 'pending')
                                                            <span class="px-2 py-0.5 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-md text-[10px] font-bold uppercase">Tinjau</span>
                                                        @elseif($dokumen->status === 'approved')
                                                            <span class="px-2 py-0.5 bg-green-50 border border-green-100 text-green-700 rounded-md text-[10px] font-bold uppercase">Diterima</span>
                                                        @else
                                                            <span class="px-2 py-0.5 bg-red-50 border border-red-100 text-red-700 rounded-md text-[10px] font-bold uppercase">Ditolak</span>
                                                        @endif
                                                        <div class="flex items-center gap-2 mt-0.5 font-bold text-xs">
                                                            @if(str_ends_with($dokumen->file_path, '.pdf'))
                                                                <a href="{{ route('bimtek.verifikasi-dokumen.preview', $dokumen->id) }}" 
                                                                    target="_blank" rel="noopener"
                                                                    class="text-purple-600 hover:text-purple-800 hover:underline">Lihat</a>
                                                            @endif
                                                            <a href="{{ route('bimtek.verifikasi-dokumen.download', $dokumen->id) }}" 
                                                               class="text-primary-600 hover:text-primary-800 hover:underline">Unduh</a>
                                                            
                                                            @if($dokumen->status === 'pending')
                                                                <button type="button" x-data @click="$dispatch('open-modal', 'approve-{{ $dokumen->id }}')"
                                                                        class="text-green-600 hover:text-green-800 hover:underline">Setujui</button>
                                                                <button type="button" x-data @click="$dispatch('open-modal', 'reject-{{ $dokumen->id }}')"
                                                                        class="text-red-600 hover:text-red-800 hover:underline">Tolak</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400 font-medium">Kosong</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-center text-gray-400 font-medium align-middle">-</td>
                                    </tr>

                                    {{-- COMPONENT MODAL PERSURATAN DI DALAM TABEL ITERASI --}}
                                    @foreach($jenisDokumenWajib as $jenis)
                                        @php
                                            $dokumen = $item['dokumen'][$jenis] ?? null;
                                            $label = \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title();
                                        @endphp
                                        @if($dokumen && $dokumen->status === 'pending')
                                            {{-- Modal Konfirmasi Persetujuan Berkas Berjalan --}}
                                            <x-modal name="approve-{{ $dokumen->id }}" :show="false" maxWidth="md">
                                                <form action="{{ route('bimtek.verifikasi-dokumen.approve', $dokumen->id) }}" method="POST" class="p-6 bg-white">
                                                    @csrf
                                                    <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                                                        <div class="w-9 h-9 bg-green-100 text-green-700 rounded-full flex items-center justify-center flex-shrink-0">
                                                            ✓
                                                        </div>
                                                        <h3 class="text-base font-bold text-gray-900">Sahkan {{ $label }}</h3>
                                                    </div>
                                                    <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1 mb-4">
                                                        <p><span class="font-bold text-gray-500">Nama Dokumen:</span> {{ $dokumen->file_name }}</p>
                                                        <p><span class="font-bold text-gray-500">Nama Pengirim:</span> {{ $item['user']->name ?? '-' }}</p>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Catatan Disposisi (Opsional)</label>
                                                        <textarea name="catatan" rows="2" class="w-full rounded-xl border-gray-300 text-sm" placeholder="Tambahkan catatan kelayakan berkas jika diperlukan..."></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-2 border-t border-gray-50 pt-4">
                                                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs rounded-xl hover:bg-gray-200 transition">Batal</button>
                                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white font-semibold text-xs rounded-xl hover:bg-green-700 transition shadow-sm">Sahkan Berkas</button>
                                                    </div>
                                                </form>
                                            </x-modal>

                                            {{-- Modal Penolakan / Pengembalian Berkas Peserta --}}
                                            <x-modal name="reject-{{ $dokumen->id }}" :show="false" maxWidth="md">
                                                <form action="{{ route('bimtek.verifikasi-dokumen.reject', $dokumen->id) }}" method="POST" class="p-6 bg-white">
                                                    @csrf
                                                    <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                                                        <div class="w-9 h-9 bg-red-100 text-red-700 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                                                            ✗
                                                        </div>
                                                        <h3 class="text-base font-bold text-gray-900">Tolak Berkas {{ $label }}</h3>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Alasan Penolakan Berkas <span class="text-red-500">*</span></label>
                                                        <textarea name="catatan_verifikasi" rows="3" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Jelaskan alasan penolakan secara rinci (misal: File buram / TTD stempel tidak lengkap)..."></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-2 border-t border-gray-50 pt-4">
                                                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs rounded-xl hover:bg-gray-200 transition">Batal</button>
                                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold text-xs rounded-xl hover:bg-red-700 transition shadow-sm">Tolak Berkas</button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @endif
                                    @endforeach
                                @empty
                                    <tr>
                                        @php
                                            $colspan = 4 + count($jenisDokumenWajib);
                                        @endphp
                                        <td colspan="{{ $colspan }}" class="px-4 py-12 text-center text-gray-400 font-medium">
                                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Belum ada berkas peserta luar instansi yang masuk untuk diverifikasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>