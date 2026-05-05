<x-app-layout>
    <x-slot name="header">
        Verifikasi Dokumen Peserta - {{ $bimtek->judul_final }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('bimtek.show', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                <div class="mt-2">
                    <h2 class="text-2xl font-bold text-gray-900">Verifikasi Dokumen Peserta</h2>
                    <p class="text-gray-600 mt-1">{{ $bimtek->judul_final }}</p>
                </div>
            </div>

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $totalPeserta = $pesertaList->count();
                    $invited = $pesertaList->where('status_verifikasi', 'invited')->count();
                    $pending = $pesertaList->where('status_verifikasi', 'pending')->count();
                    $verified = $pesertaList->where('status_verifikasi', 'verified')->count();
                    $rejected = $pesertaList->where('status_verifikasi', 'rejected')->count();
                @endphp
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">Total Peserta</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalPeserta }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg shadow-sm border border-yellow-200">
                    <p class="text-sm text-yellow-700">Pending Verifikasi</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $pending }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
                    <p class="text-sm text-green-700">Terverifikasi</p>
                    <p class="text-2xl font-bold text-green-900">{{ $verified }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
                    <p class="text-sm text-red-700">Ditolak</p>
                    <p class="text-2xl font-bold text-red-900">{{ $rejected }}</p>
                </div>
            </div>

            {{-- Peserta List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Peserta</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    @foreach($jenisDokumenWajib as $jenis)
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            {{ \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title() }}
                                        </th>
                                    @endforeach
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pesertaList as $index => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['user']->name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($item['status_verifikasi'] === 'invited')
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Belum Upload</span>
                                            @elseif($item['status_verifikasi'] === 'pending')
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                                            @elseif($item['status_verifikasi'] === 'verified')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Verified</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                                            @endif
                                        </td>
                                        @foreach($jenisDokumenWajib as $jenis)
                                            @php
                                                $dokumen = $item['dokumen'][$jenis] ?? null;
                                                $label = \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title();
                                            @endphp
                                            <td class="px-4 py-3 text-center">
                                                @if($dokumen)
                                                    <div class="flex flex-col items-center gap-2">
                                                        @if($dokumen->status === 'pending')
                                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                                        @elseif($dokumen->status === 'approved')
                                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">✓ Approved</span>
                                                        @else
                                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">✗ Rejected</span>
                                                        @endif
                                                        <div class="flex gap-1">
                                                            @if(str_ends_with($dokumen->file_path, '.pdf'))
                                                                <a href="{{ route('bimtek.verifikasi-dokumen.preview', $dokumen) }}" 
                                                                   target="_blank"
                                                                   class="text-xs text-purple-600 hover:text-purple-800 underline">Lihat</a>
                                                            @endif
                                                            <a href="{{ route('bimtek.verifikasi-dokumen.download', $dokumen) }}" 
                                                               class="text-xs text-blue-600 hover:text-blue-800 underline">Unduh</a>
                                                            @if($dokumen->status === 'pending')
                                                                <button x-data @click="$dispatch('open-modal', 'approve-{{ $dokumen->id }}')"
                                                                        class="text-xs text-green-600 hover:text-green-800 underline">Setujui</button>
                                                                <button x-data @click="$dispatch('open-modal', 'reject-{{ $dokumen->id }}')"
                                                                        class="text-xs text-red-600 hover:text-red-800 underline">Tolak</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400">Belum upload</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-xs text-gray-500">-</span>
                                        </td>
                                    </tr>

                                    {{-- Modal Approve --}}
                                    @foreach($jenisDokumenWajib as $jenis)
                                        @php
                                            $dokumen = $item['dokumen'][$jenis] ?? null;
                                            $label = \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title();
                                        @endphp
                                        @if($dokumen && $dokumen->status === 'pending')
                                            <x-modal name="approve-{{ $dokumen->id }}" :show="false" maxWidth="md">
                                                <form action="{{ route('bimtek.verifikasi-dokumen.approve', $dokumen) }}" method="POST" class="bg-white">
                                                    <div class="p-6 border-b border-gray-200">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-3">
                                                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </div>
                                                                <h3 class="text-lg font-semibold text-gray-900">Setujui {{ $label }}</h3>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="p-6 space-y-4">
                                                        <p class="text-gray-700">Anda yakin ingin menyetujui dokumen berikut?</p>
                                                        
                                                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-100">
                                                            <div class="space-y-2">
                                                                <div class="flex justify-between items-start">
                                                                    <span class="text-sm font-medium text-gray-600">Jenis Dokumen:</span>
                                                                    <span class="text-sm font-semibold text-gray-900">{{ $label }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-start">
                                                                    <span class="text-sm font-medium text-gray-600">Nama File:</span>
                                                                    <span class="text-sm font-semibold text-gray-900 text-right">{{ $dokumen->file_name }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-start">
                                                                    <span class="text-sm font-medium text-gray-600">Peserta:</span>
                                                                    <span class="text-sm font-semibold text-gray-900">{{ $item['user']->name }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-start pt-2 border-t border-blue-200">
                                                                    <span class="text-xs text-gray-500">Diupload:</span>
                                                                    <span class="text-xs text-gray-500">{{ $dokumen->uploaded_at->format('d M Y H:i') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @csrf
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                                            <textarea name="catatan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                                                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</button>
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Setujui Dokumen
                                                        </button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                            <x-modal name="reject-{{ $dokumen->id }}" :show="false" maxWidth="md">
                                                <form action="{{ route('bimtek.verifikasi-dokumen.reject', $dokumen) }}" method="POST" class="bg-white">
                                                    <div class="p-6 border-b border-gray-200">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-3">
                                                                <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                    </svg>
                                                                </div>
                                                                <h3 class="text-lg font-semibold text-gray-900">Tolak {{ $label }}</h3>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="p-6 space-y-4">
                                                        <p class="text-gray-700">Anda yakin ingin menolak dokumen dari <strong>{{ $item['user']->name }}</strong>?</p>
                                                        
                                                        <div class="bg-gradient-to-r from-red-50 to-orange-50 p-4 rounded-lg border border-red-100">
                                                            <div class="space-y-2">
                                                                <div class="flex justify-between items-start">
                                                                    <span class="text-sm font-medium text-gray-600">Jenis Dokumen:</span>
                                                                    <span class="text-sm font-semibold text-gray-900">{{ $label }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-start">
                                                                    <span class="text-sm font-medium text-gray-600">Nama File:</span>
                                                                    <span class="text-sm font-semibold text-gray-900 text-right">{{ $dokumen->file_name }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @csrf
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Penolakan <span class="text-red-500 font-bold">*</span></label>
                                                            <textarea name="catatan_verifikasi" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Jelaskan alasan penolakan..."></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                                                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</button>
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                            Tolak Dokumen
                                                        </button>
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
                                        <td colspan="{{ $colspan }}" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada peserta yang terdaftar
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
