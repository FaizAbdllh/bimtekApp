<x-app-layout>
    <x-slot name="header">
        Detail Kebutuhan Fasilitas
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('rt.index') }}" class="text-gray-500 hover:text-primary-600">
                            Kebutuhan Fasilitas
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-700 font-medium">Detail</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Status Banner --}}
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
            <div class="mb-6 p-4 rounded-lg border-l-4 {{ $statusRtColors[$pengajuan->status_rt] ?? 'bg-gray-100 border-gray-400 text-gray-800' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Status: {{ $statusRtLabels[$pengajuan->status_rt] ?? $pengajuan->status_rt }}</span>
                </div>
            </div>

            {{-- Info Kegiatan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $pengajuan->judul_rencana }}</h2>
                            <p class="mt-1 text-sm text-gray-500">Diajukan oleh {{ $pengajuan->user->name }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $pengajuan->jenis_kegiatan === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($pengajuan->jenis_kegiatan) }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tempat Kegiatan</h4>
                            <p class="mt-1 text-gray-900">{{ $pengajuan->tempat_kegiatan ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tanggal Kegiatan</h4>
                            <p class="mt-1 text-gray-900">
                                {{ $pengajuan->tanggal_mulai_rencana?->format('d F Y') ?? '-' }}
                                @if($pengajuan->tanggal_selesai_rencana && $pengajuan->tanggal_mulai_rencana != $pengajuan->tanggal_selesai_rencana)
                                    - {{ $pengajuan->tanggal_selesai_rencana->format('d F Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Fasilitas & Logistik --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Daftar Kebutuhan Fasilitas & Logistik
                    </h3>

                    @if($pengajuan->fasilitasLogistiks->count() > 0)
                        <form action="{{ route('rt.update', $pengajuan) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                                                <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Fasilitas</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Jumlah</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->fasilitasLogistiks as $index => $fasilitas)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" 
                                                        name="fasilitas_dipenuhi[]" 
                                                        value="{{ $fasilitas->id }}"
                                                        {{ $fasilitas->is_dipenuhi ? 'checked' : '' }}
                                                        class="fasilitas-check rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $fasilitas->nama_fasilitas }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $fasilitas->jumlah }} {{ $fasilitas->satuan ?? '' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ $fasilitas->keterangan ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($fasilitas->is_dipenuhi)
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Terpenuhi</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Belum</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Catatan RT --}}
                            <div class="mt-6">
                                <label for="catatan_rt" class="block text-sm font-medium text-gray-700 mb-2">Catatan dari RT (Opsional)</label>
                                <textarea name="catatan_rt" id="catatan_rt" rows="3" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="Tambahkan catatan jika ada kendala atau informasi tambahan...">{{ $pengajuan->catatan_rt }}</textarea>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-6 flex justify-between items-center">
                                <a href="{{ route('rt.index') }}" class="text-gray-600 hover:text-gray-900">
                                    ← Kembali ke Daftar
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg font-semibold text-sm hover:bg-primary-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="mt-2 text-gray-500">Tidak ada kebutuhan fasilitas untuk kegiatan ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
