<x-app-layout>
    <x-slot name="header">
        Edit Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.show', $bimtek) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Edit Data Bimtek</h2>
                    @php
                        $isPersiapan = $bimtek->status_pelaksanaan === 'persiapan';
                    @endphp

                    @if(!$isPersiapan)
                        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            Status bimtek saat ini <span class="font-semibold">{{ ucfirst($bimtek->status_pelaksanaan) }}</span>. Data hanya dapat diubah saat status <span class="font-semibold">Persiapan</span>. Gunakan Kelola Status Bimtek pada halaman detail untuk perubahan status.
                        </div>
                    @endif

                    <form action="{{ route('bimtek.update', $bimtek) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Judul --}}
                            <div class="md:col-span-2">
                                <label for="judul_final" class="block text-sm font-medium text-gray-700 mb-1">Judul Bimtek</label>
                                <input type="text" name="judul_final" id="judul_final" 
                                    value="{{ old('judul_final', $bimtek->judul_final) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('judul_final') border-red-500 @enderror"
                                    required>
                                @error('judul_final')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tanggal Mulai --}}
                            <div>
                                <label for="tanggal_mulai_aktual" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai_aktual" id="tanggal_mulai_aktual" 
                                    value="{{ old('tanggal_mulai_aktual', $bimtek->tanggal_mulai_aktual?->format('Y-m-d')) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_mulai_aktual') border-red-500 @enderror">
                                @error('tanggal_mulai_aktual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tanggal Selesai --}}
                            <div>
                                <label for="tanggal_selesai_aktual" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai_aktual" id="tanggal_selesai_aktual" 
                                    value="{{ old('tanggal_selesai_aktual', $bimtek->tanggal_selesai_aktual?->format('Y-m-d')) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_selesai_aktual') border-red-500 @enderror">
                                @error('tanggal_selesai_aktual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Lokasi --}}
                            <div class="md:col-span-2">
                                <label for="lokasi_aktual" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                                <input type="text" name="lokasi_aktual" id="lokasi_aktual" 
                                    value="{{ old('lokasi_aktual', $bimtek->lokasi_aktual) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('lokasi_aktual') border-red-500 @enderror">
                                @error('lokasi_aktual')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Anggaran Disetujui --}}
                            <div>
                                <label for="anggaran_disetujui" class="block text-sm font-medium text-gray-700 mb-1">Anggaran Disetujui (Rp)</label>
                                <input type="number" name="anggaran_disetujui" id="anggaran_disetujui" 
                                    value="{{ old('anggaran_disetujui', $bimtek->anggaran_disetujui) }}"
                                    class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm @error('anggaran_disetujui') border-red-500 @enderror"
                                    min="0" readonly>
                                <p class="text-xs text-gray-500 mt-1">Field major: perubahan wajib melalui fitur Revisi.</p>
                                @error('anggaran_disetujui')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status Pelaksanaan --}}
                            <div>
                                <label for="status_pelaksanaan" class="block text-sm font-medium text-gray-700 mb-1">Status Pelaksanaan</label>
                                <select name="status_pelaksanaan" id="status_pelaksanaan" disabled
                                    class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm @error('status_pelaksanaan') border-red-500 @enderror">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('status_pelaksanaan', $bimtek->status_pelaksanaan) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="status_pelaksanaan" value="{{ old('status_pelaksanaan', $bimtek->status_pelaksanaan) }}">
                                <p class="text-xs text-gray-500 mt-1">Status hanya dapat diubah melalui tombol Kelola Status Bimtek di halaman detail.</p>
                                @error('status_pelaksanaan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Deskripsi Jadwal --}}
                            <div class="md:col-span-2">
                                <label for="deskripsi_jadwal" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Jadwal</label>
                                <textarea name="deskripsi_jadwal" id="deskripsi_jadwal" rows="4"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('deskripsi_jadwal') border-red-500 @enderror">{{ old('deskripsi_jadwal', $bimtek->deskripsi_jadwal) }}</textarea>
                                @error('deskripsi_jadwal')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Pemateri --}}
                            <div class="md:col-span-2 pt-4 border-t">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Pemateri</h3>
                            </div>

                            <div class="md:col-span-2" x-data="{ items: @json(old('daftar_pemateri', $bimtek->daftar_pemateri ?? [])) }" x-init="if (items.length === 0) items.push({ nama: '', asal_instansi: '' })">
                                <div class="space-y-3">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="grid grid-cols-1 md:grid-cols-7 gap-3 items-start">
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pemateri</label>
                                                <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                    :name="'daftar_pemateri[' + index + '][nama]'" x-model="item.nama" placeholder="Nama pemateri">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Asal Instansi</label>
                                                <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                    :name="'daftar_pemateri[' + index + '][asal_instansi]'" x-model="item.asal_instansi" placeholder="Instansi (opsional)">
                                            </div>
                                            <div class="md:col-span-1 flex items-end">
                                                <button type="button" class="w-full px-3 py-2 text-xs text-red-700 bg-red-50 hover:bg-red-100 rounded-lg"
                                                    @click="items.splice(index, 1)" x-show="items.length > 1">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <button type="button" class="mt-3 inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs text-gray-700"
                                    @click="items.push({ nama: '', asal_instansi: '' })">
                                    + Tambah Pemateri
                                </button>
                            </div>
                            
                            {{-- Syarat Kelulusan --}}
                            <div class="md:col-span-2 pt-4 border-t">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Syarat Kelulusan</h3>
                            </div>

                            {{-- Feature Toggles: Has Tugas / Has Sertifikat --}}
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_tugas" value="1"
                                        {{ old('has_tugas', $bimtek->has_tugas ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 shadow-sm"
                                        {{ !$isPersiapan ? 'disabled' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Aktifkan fitur Tugas</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1 ml-6">Jika dimatikan, tab Tugas dan alur pengumpulan tugas akan disembunyikan.</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_sertifikat" value="1"
                                        {{ old('has_sertifikat', $bimtek->has_sertifikat ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 shadow-sm"
                                        {{ !$isPersiapan ? 'disabled' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Aktifkan fitur Sertifikat</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1 ml-6">Jika dimatikan, tab Sertifikat dan tombol generate akan disembunyikan.</p>
                            </div>
                            
                            <div>
                                <label for="syarat_kehadiran_persen" class="block text-sm font-medium text-gray-700 mb-1">Syarat Kehadiran (%)</label>
                                <input type="number" name="syarat_kehadiran_persen" id="syarat_kehadiran_persen" 
                                    value="{{ old('syarat_kehadiran_persen', $bimtek->syarat_kehadiran_persen) }}"
                                    class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm"
                                    min="0" max="100" readonly>
                                <p class="text-xs text-gray-500 mt-1">Field major: perubahan wajib melalui fitur Revisi.</p>
                            </div>
                            
                            <div>
                                <label for="syarat_tugas_persen" class="block text-sm font-medium text-gray-700 mb-1">Nilai Minimal Tugas untuk Sertifikat (%)</label>
                                <input type="number" name="syarat_tugas_persen" id="syarat_tugas_persen" 
                                    value="{{ old('syarat_tugas_persen', $bimtek->syarat_tugas_persen) }}"
                                    class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm"
                                    min="0" max="100" readonly>
                                <p class="text-xs text-gray-500 mt-1">Field major: perubahan wajib melalui fitur Revisi.</p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="syarat_tugas_wajib" value="1" 
                                        {{ old('syarat_tugas_wajib', $bimtek->syarat_tugas_wajib) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 shadow-sm"
                                        disabled>
                                    @if(old('syarat_tugas_wajib', $bimtek->syarat_tugas_wajib))
                                        <input type="hidden" name="syarat_tugas_wajib" value="1">
                                    @endif
                                    <span class="ml-2 text-sm text-gray-700">Wajib mengumpulkan semua tugas</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1 ml-6">Field major: perubahan wajib melalui fitur Revisi.</p>
                            </div>

                            {{-- Verifikasi Dokumen --}}
                            <div class="md:col-span-2 pt-4 border-t">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Verifikasi Dokumen Peserta</h3>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="butuh_verifikasi_dokumen" value="1" 
                                        {{ old('butuh_verifikasi_dokumen', $bimtek->butuh_verifikasi_dokumen) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        id="toggle_verifikasi"
                                        x-data
                                        @change="document.getElementById('dokumen_options').classList.toggle('hidden', !$el.checked)"
                                        disabled>
                                    <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan verifikasi dokumen persyaratan</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1 ml-6">Pengaturan ini ditentukan saat pengajuan dan tidak dapat diubah di tahap ini.</p>
                            </div>
                            
                            <div id="dokumen_options" class="md:col-span-2 ml-6 {{ old('butuh_verifikasi_dokumen', $bimtek->butuh_verifikasi_dokumen) ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen yang Wajib Diupload:</label>
                                <div class="space-y-2">
                                    @php
                                        $jenisSelected = old('jenis_dokumen_wajib', $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd']);
                                    @endphp
                                    @foreach($jenisSelected as $jenis)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="jenis_dokumen_wajib[]" value="{{ $jenis }}" 
                                                checked
                                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                disabled>
                                            <span class="ml-2 text-sm text-gray-700">{{ \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Pengaturan ini ditentukan saat pengajuan dan tidak dapat diubah di tahap ini.</p>
                            </div>
                        </div>

                        {{-- Informasi Pengajuan Asal (readonly) --}}
                        @if($bimtek->pengajuan)
                            <div class="mt-6 pt-6 border-t">
                                <h3 class="text-sm font-semibold text-gray-900 mb-4">Informasi Pengajuan Asal</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Pengaju:</span>
                                        <span class="text-gray-900 font-medium ml-2">{{ $bimtek->pengajuan->user->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Judul Rencana:</span>
                                        <span class="text-gray-900 font-medium ml-2">{{ $bimtek->pengajuan->judul_rencana }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Tanggal Rencana:</span>
                                        <span class="text-gray-900 font-medium ml-2">{{ $bimtek->pengajuan->tanggal_mulai_rencana?->format('d M Y') }} - {{ $bimtek->pengajuan->tanggal_selesai_rencana?->format('d M Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Lokasi Rencana:</span>
                                        <span class="text-gray-900 font-medium ml-2">{{ $bimtek->pengajuan->lokasi_rencana }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Submit --}}
                        <div class="mt-6 pt-6 border-t flex justify-end gap-2">
                            <a href="{{ route('bimtek.show', $bimtek) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed" {{ !$isPersiapan ? 'disabled' : '' }}>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
