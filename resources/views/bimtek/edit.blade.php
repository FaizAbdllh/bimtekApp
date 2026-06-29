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
                        // REFAKTORISASI: Mengubah status_pelaksanaan menjadi status
                        $isPersiapan = $bimtek->status === 'persiapan';
                    @endphp

                    @if(!$isPersiapan)
                        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 font-medium leading-relaxed">
                            Status pelaksanaan kelas saat ini: <span class="font-bold uppercase">{{ $bimtek->status }}</span>. Data aktual logistik dan jadwal utama hanya dapat diubah secara bebas ketika status kelas berada dalam fase <span class="font-bold">Persiapan</span>.
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
                                    required {{ !$isPersiapan ? 'readonly bg-gray-50 text-gray-500' : '' }}>
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
                                    {{ !$isPersiapan ? 'readonly bg-gray-50 text-gray-500' : '' }}>
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
                                    {{ !$isPersiapan ? 'readonly bg-gray-50 text-gray-500' : '' }}>
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
                                    {{ !$isPersiapan ? 'readonly bg-gray-50 text-gray-500' : '' }}>
                                @error('lokasi_aktual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tautan URL Virtual Meeting --}}
                            <div class="md:col-span-2">
                                <label for="virtual_meeting_url" class="block text-sm font-semibold text-gray-700 mb-1">Tautan Media Virtual Rapat (Zoom / Google Meet)</label>
                                <input type="url" name="virtual_meeting_url" id="virtual_meeting_url"
                                    value="{{ old('virtual_meeting_url', $bimtek->virtual_meeting_url) }}"
                                    placeholder="https://us02web.zoom.us/j/xxxxxxxx"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('virtual_meeting_url') border-red-500 @enderror text-sm">
                                @error('virtual_meeting_url')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @php
                                    // REFAKTORISASI: Mengubah properti mode_pelaksanaan_code menjadi mode_pelaksanaan
                                    $modeCode = $bimtek->mode_pelaksanaan;
                                @endphp
                                <p class="text-[11px] text-gray-400 mt-1">Saran: Diisi jika kelas menggunakan mode online/hybrid agar tautan otomatis muncul di beranda akun peserta luar.</p>
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

                            {{-- Status Pelaksanaan Dropdown --}}
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-400 mb-1">Status Alur Pelaksanaan (Kunci Sistem)</label>
                                <select id="status" disabled class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-400 font-medium text-sm py-2">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $bimtek->status) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1">Perubahan status wajib dilakukan via tombol interaktif di halaman detail utama kelas.</p>
                            </div>

                            {{-- Deskripsi Rundown Acara Aktual --}}
                            <div class="md:col-span-2">
                                <label for="deskripsi_jadwal" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Ringkas Rundown Jadwal Aktual</label>
                                <textarea name="deskripsi_jadwal" id="deskripsi_jadwal" rows="4" placeholder="Tuliskan detail jam pelaksanaan materi atau tautan menuju draf berkas eksternal lainnya..."
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('deskripsi_jadwal') border-red-500 @enderror text-sm">{{ old('deskripsi_jadwal', $bimtek->deskripsi_jadwal) }}</textarea>
                                @error('deskripsi_jadwal')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Sub-Modul Input Array Nama Pemateri (Alpine.js) --}}
                            <div class="md:col-span-2 pt-4 border-t border-gray-100">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">Daftar Pengisi Acara / Pemateri</h3>
                                <p class="text-xs text-gray-400">Masukkan nama instruktur narasumber beserta instansi asal untuk rekap berkas pertanggungjawaban.</p>
                            </div>

                            <div class="md:col-span-2" x-data="{ items: @json(old('daftar_pemateri', $bimtek->daftar_pemateri ?? [])) }" x-init="if (items.length === 0) items.push({ nama: '', asal_instansi: '' })">
                                <div class="space-y-3">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-end bg-gray-50/50 border border-gray-100 p-3 rounded-xl shadow-sm">
                                            <div class="sm:col-span-3">
                                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Narasumber</label>
                                                <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1.5"
                                                    :name="'daftar_pemateri[' + index + '][nama]'" x-model="item.nama" placeholder="Nama lengkap & gelar..." required">
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1">Asal Instansi / Unit Kerja</label>
                                                <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1.5"
                                                    :name="'daftar_pemateri[' + index + '][asal_instansi]'" x-model="item.asal_instansi" placeholder="Misal: Widyaprada BBPMP Sumbar...">
                                            </div>
                                            <div class="sm:col-span-1 flex items-end">
                                                <button type="button" class="w-full py-1.5 text-xs font-bold text-red-700 bg-red-50 border border-red-100 hover:bg-red-100 rounded-lg transition"
                                                    @click="items.splice(index, 1)" x-show="items.length > 1">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <button type="button" class="mt-3 inline-flex items-center px-3 py-1.5 border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 rounded-lg text-xs font-bold transition shadow-sm"
                                    @click="items.push({ nama: '', asal_instansi: '' })">
                                    + Tambah Baris Narasumber
                                </button>
                            </div>
                            
                            {{-- Aturan Batas Kelulusan & Konfigurasi Fitur Sertifikasi --}}
                            <div class="md:col-span-2 pt-4 border-t border-gray-100">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">Konfigurasi Hak Fitur Kelas</h3>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_tugas" value="1"
                                        {{ old('has_tugas', $bimtek->has_tugas ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                                        {{ !$isPersiapan ? 'disabled' : '' }}>
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Aktifkan Modul Unggah Lembar Tugas Peserta</span>
                                </label>
                                <p class="text-xs text-gray-400 ml-6">Jika dinonaktifkan, tab Tugas pada dasbor utama peserta akan disembunyikan penuh.</p>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_sertifikat" value="1"
                                        {{ old('has_sertifikat', $bimtek->has_sertifikat ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                                        {{ !$isPersiapan ? 'disabled' : '' }}>
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Aktifkan Kelulusan Penerbitan E-Sertifikat</span>
                                </label>
                                <p class="text-xs text-gray-400 ml-6">Jika dicentang, peserta yang memenuhi kriteria kehadiran dapat langsung mengunduh file sertifikat.</p>
                            </div>
                        </div>

                        {{-- Ringkasan Riwayat Perencanaan Pembuat Awal ( Read-Only Baseline ) --}}
                        {{-- REFAKTORISASI: Mengambil data rencana awal langsung dari baris data objek itu sendiri --}}
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
                            </div>
                        </div>

                        {{-- Panel Aksi Form --}}
                        <div class="mt-6 pt-6 border-t border-gray-100 flex justify-end gap-3">
                            <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-bold text-xs rounded-xl hover:bg-gray-50 transition shadow-sm">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-5 py-2 bg-primary-600 text-white font-bold text-xs rounded-xl hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm" {{ !$isPersiapan ? 'disabled' : '' }}>
                                Simpan Perubahan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>