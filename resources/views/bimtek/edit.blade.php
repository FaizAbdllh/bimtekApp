<x-app-layout>
    <x-slot name="header">
        Edit Perubahan Data Aktual Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Kelas
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Edit Data Pelaksanaan Aktual</h2>
                    
                    @php
                        $isEditable = $bimtek->isEditable();
                        $isVirtualMeetingEditable = $bimtek->status === 'persiapan';
                    @endphp

                    @if(!$isEditable)
                        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 font-medium leading-relaxed">
                            Status pelaksanaan kelas saat ini: <span class="font-bold uppercase">{{ str_replace('_', ' ', $bimtek->status) }}</span>. Data aktual logistik dan jadwal utama hanya dapat diubah secara bebas ketika status kelas masih berada dalam fase <span class="font-bold">Persiapan, Registrasi, atau Persiapan Selesai</span>.
                        </div>
                    @endif

                    <form action="{{ route('bimtek.update', $bimtek->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Judul Final Pelaksanaan --}}
                            <div class="md:col-span-2">
                                <label for="judul_final" class="block text-sm font-semibold text-gray-700 mb-1">Judul Resmi Kegiatan (Aktual)</label>
                                <input type="text" name="judul_final" id="judul_final" 
                                    value="{{ old('judul_final', $bimtek->judul_final ?? $bimtek->judul_rencana) }}"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('judul_final') border-red-500 @enderror text-sm"
                                    required {{ !$isEditable ? 'readonly bg-gray-50 text-gray-500' : '' }}>
                                @error('judul_final')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tanggal Mulai Aktual --}}
                            <div>
                                <label for="tanggal_mulai_aktual" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai Pelaksanaan</label>
                                <input type="date" name="tanggal_mulai_aktual" id="tanggal_mulai_aktual" 
                                    value="{{ old('tanggal_mulai_aktual', $bimtek->tanggal_mulai_aktual ? $bimtek->tanggal_mulai_aktual->format('Y-m-d') : ($bimtek->tanggal_mulai_rencana ? $bimtek->tanggal_mulai_rencana->format('Y-m-d') : '')) }}"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_mulai_aktual') border-red-500 @enderror text-sm"
                                    {{ !$isEditable ? 'readonly bg-gray-50 text-gray-500' : '' }}>
                                @error('tanggal_mulai_aktual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tanggal Selesai Aktual --}}
                            <div>
                                <label for="tanggal_selesai_aktual" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai Pelaksanaan</label>
                                <input type="date" name="tanggal_selesai_aktual" id="tanggal_selesai_aktual" 
                                    value="{{ old('tanggal_selesai_aktual', $bimtek->tanggal_selesai_aktual ? $bimtek->tanggal_selesai_aktual->format('Y-m-d') : ($bimtek->tanggal_selesai_rencana ? $bimtek->tanggal_selesai_rencana->format('Y-m-d') : '')) }}"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_selesai_aktual') border-red-500 @enderror text-sm"
                                    {{ !$isEditable ? 'readonly bg-gray-50 text-gray-500' : '' }}>
                                @error('tanggal_selesai_aktual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Lokasi Aktual Gedung / Aula --}}
                            <div class="md:col-span-2">
                                <label for="lokasi_aktual" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Tempat Pelaksanaan Aktual</label>
                                <input type="text" name="lokasi_aktual" id="lokasi_aktual" 
                                    value="{{ old('lokasi_aktual', $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana) }}"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('lokasi_aktual') border-red-500 @enderror text-sm"
                                    placeholder="Contoh: Gedung Aula Utama Kompleks BBPMP Sumatera Barat..."
                                    {{ !$isEditable ? 'readonly bg-gray-50 text-gray-500' : '' }}>
                                @error('lokasi_aktual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>


                            {{-- Anggaran Dana Terkunci --}}
                            <div>
                                <label for="anggaran_disetujui" class="block text-sm font-semibold text-gray-400 mb-1">Total Pagu Dana DIPA Disetujui (Read-Only)</label>
                                <div class="relative mt-1 rounded-xl shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-400 text-sm font-medium">Rp</span>
                                    </div>
                                    <input type="text" id="anggaran_disetujui" 
                                        value="{{ number_format($bimtek->kebutuhanAnggarans->sum('total_biaya') ?? 0, 0, ',', '.') }}"
                                        class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-400 font-bold text-sm pl-9 py-2" readonly>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Pagu dana dikunci otomatis sesuai keputusan final meja review pejabat PPK.</p>
                            </div>

                            
                        </div>

                        {{-- Ringkasan Riwayat Perencanaan Pembuat Awal ( Read-Only Baseline ) --}}
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Informasi Peta Perencanaan Awal</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-500 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <div>
                                    <span class="font-bold text-gray-600">PIC Pengaju:</span>
                                    <span class="ml-1 font-medium">{{ $bimtek->pic->name ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-600">Judul Usulan Awal:</span>
                                    <span class="ml-1 font-medium">{{ $bimtek->judul_rencana }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-600">Rencana Waktu:</span>
                                    <span class="ml-1 font-medium">{{ $bimtek->tanggal_mulai_rencana?->format('d M Y') }} s/d {{ $bimtek->tanggal_selesai_rencana?->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-600">Rencana Tempat:</span>
                                    <span class="ml-1 font-medium">{{ $bimtek->tempat_kegiatan_rencana ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-600">Total Pagu Dana DIPA Disetujui:</span>
                                    <span class="ml-1 font-medium">{{ number_format($bimtek->kebutuhanAnggarans->sum('total_biaya') ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Panel Aksi Form --}}
                        <div class="mt-6 pt-6 border-t border-gray-100 flex justify-end gap-3">
                            <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-bold text-xs rounded-xl hover:bg-gray-100 transition shadow-sm">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-5 py-2 bg-primary-600 text-white font-bold text-xs rounded-xl hover:bg-primary-800 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                                Simpan Perubahan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>