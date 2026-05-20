<x-app-layout>
    <x-slot name="header">
        Formulir Telaah Staf (Pengajuan Kegiatan)
    </x-slot>

    @push('head-scripts')
    <style>
        /* Hide number spinners so more space is available for digits in volume fields. */
        .no-spin::-webkit-outer-spin-button,
        .no-spin::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .no-spin {
            -moz-appearance: textfield;
        }
    </style>
    <script>
        window.pengajuanForm = function() {
            return {
                butuhVerifikasi: @json((bool) old('butuh_verifikasi_dokumen')),
                jenisKegiatan: @json(old('jenis_kegiatan', '')),
                docTypes: @json(old('jenis_dokumen_wajib', ['Surat Tugas', 'SPPD'])),
                newDocType: '',
                predefinedDocType: '',
                predefinedOptions: [
                    'Surat Tugas',
                    'SPPD',
                    'KTP',
                    'NPWP',
                    'Surat Keterangan',
                    'Surat Rekomendasi',
                    'Sertifikat',
                    'Pas Foto',
                ],
                anggaranItems: [
                    { 
                        sbm_master_id: '', 
                        nama_item: '', 
                        kategori: 'lainnya', 
                        volume_1: '', 
                        satuan_primary: '',
                        satuan_primary_suggestion: '',
                        volume_2: '',
                        satuan_secondary: '',
                        satuan_secondary_suggestion: '',
                        harga_satuan: '',
                        harga_satuan_sbm: '',
                        total_biaya: '' 
                    }
                ],
                fasilitasItems: [
                    { nama: '', jumlah: 1, satuan: 'Unit', satuanCustom: '' }
                ],

                get grandTotal() {
                    return this.anggaranItems.reduce((sum, item) => sum + (parseFloat(item.total_biaya) || 0), 0);
                },

                syncVerifikasiByJenis() {
                    if (this.jenisKegiatan === 'internal') {
                        this.butuhVerifikasi = false;
                    }
                },

                formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(value || 0);
                },

                onSbmChange(item, event) {
                    const selectedOption = event.target.options[event.target.selectedIndex];
                    if (selectedOption.value) {
                        item.sbm_master_id = selectedOption.value;
                        item.nama_item = selectedOption.dataset.nama || '';
                        item.kategori = selectedOption.dataset.kategori || 'lainnya';
                        // auto-fill satuan from SBM but keep editable for user
                        const suggestedPrimary = selectedOption.dataset.satuanPrimary || '';
                        const suggestedSecondary = selectedOption.dataset.satuanSecondary || '';
                        item.satuan_primary = suggestedPrimary;
                        item.satuan_secondary = suggestedSecondary;
                        // clear suggestion trackers
                        item.satuan_primary_suggestion = '';
                        item.satuan_secondary_suggestion = '';
                        item.harga_satuan = parseFloat(selectedOption.dataset.harga) || 0;
                        item.harga_satuan_sbm = parseFloat(selectedOption.dataset.harga) || 0;
                        this.calculateTotal(item);
                    } else {
                        // cleared selection -> clear values
                        item.satuan_primary = '';
                        item.satuan_secondary = '';
                        item.satuan_primary_suggestion = '';
                        item.satuan_secondary_suggestion = '';
                        item.harga_satuan_sbm = '';
                    }
                },

                calculateTotal(item) {
                    const hasInput = [item.volume_1, item.volume_2, item.harga_satuan]
                        .some((value) => value !== '' && value !== null && value !== undefined);
                    if (!hasInput) {
                        item.total_biaya = '';
                        return;
                    }

                    const vol1 = Number(item.volume_1) || 0;
                    const vol2 = Number(item.volume_2) || 0;
                    const harga = Number(item.harga_satuan) || 0;
                    item.total_biaya = vol1 * vol2 * harga;
                },

                addAnggaran() {
                    this.anggaranItems.push({
                        sbm_master_id: '',
                        nama_item: '',
                        kategori: 'lainnya',
                        volume_1: '',
                        satuan_primary: '',
                        satuan_primary_suggestion: '',
                        volume_2: '',
                        satuan_secondary: '',
                        satuan_secondary_suggestion: '',
                        harga_satuan: '',
                        harga_satuan_sbm: '',
                        total_biaya: 0
                    });
                },

                removeAnggaran(index) {
                    if (this.anggaranItems.length > 1) {
                        this.anggaranItems.splice(index, 1);
                    }
                },

                addFasilitas() {
                    this.fasilitasItems.push({ nama: '', jumlah: 1, satuan: 'Unit', satuanCustom: '' });
                },

                removeFasilitas(index) {
                    if (this.fasilitasItems.length > 1) {
                        this.fasilitasItems.splice(index, 1);
                    }
                },

                addFasilitasSuggestion(nama, satuan = 'Unit') {
                    const exists = this.fasilitasItems.some(f => f.nama === nama);
                    if (!exists) {
                        const lastItem = this.fasilitasItems[this.fasilitasItems.length - 1];
                        if (!lastItem.nama) {
                            lastItem.nama = nama;
                            lastItem.jumlah = 1;
                            lastItem.satuan = satuan;
                            lastItem.satuanCustom = '';
                        } else {
                            this.fasilitasItems.push({ nama: nama, jumlah: 1, satuan: satuan, satuanCustom: '' });
                        }
                    }
                },

                getSatuan(item) {
                    return item.satuan === 'Lainnya' ? item.satuanCustom : item.satuan;
                },

                addDocType() {
                    const value = this.newDocType.trim();
                    if (!value) {
                        return;
                    }

                    const exists = this.docTypes.some((item) => item.toLowerCase() === value.toLowerCase());
                    if (!exists) {
                        this.docTypes.push(value);
                    }

                    this.newDocType = '';
                },

                addPredefinedDocType() {
                    const value = this.predefinedDocType.trim();
                    if (!value) {
                        return;
                    }

                    const exists = this.docTypes.some((item) => item.toLowerCase() === value.toLowerCase());
                    if (!exists) {
                        this.docTypes.push(value);
                    }

                    this.predefinedDocType = '';
                },

                removeDocType(index) {
                    this.docTypes.splice(index, 1);
                },

                init() {
                    this.syncVerifikasiByJenis();
                }
            };
        };
    </script>
    @endpush

    <div class="py-6" x-data="pengajuanForm()" x-init="init()">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('pengajuan.index') }}" class="text-gray-500 hover:text-primary-600">
                            Pengajuan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-700 font-medium">Pengajuan Baru</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <form action="{{ route('pengajuan.store') }}" method="POST" id="pengajuanForm">
                @csrf

                {{-- Section 1: Informasi Umum Kegiatan --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-5 border-b border-gray-200 bg-primary-50">
                        <h3 class="text-lg font-semibold text-primary-800">1. Informasi Umum Kegiatan</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        {{-- Jenis Kegiatan --}}
                        <div>
                            <label for="jenis_kegiatan" class="block text-sm font-medium text-gray-700">
                                Jenis Kegiatan <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_kegiatan" id="jenis_kegiatan" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('jenis_kegiatan') border-red-500 @enderror"
                                x-model="jenisKegiatan"
                                @change="syncVerifikasiByJenis()">
                                <option value="">Pilih Jenis Kegiatan</option>
                                <option value="internal" {{ old('jenis_kegiatan') == 'internal' ? 'selected' : '' }}>Internal (Peserta dari BBPMP)</option>
                                <option value="eksternal" {{ old('jenis_kegiatan') == 'eksternal' ? 'selected' : '' }}>Eksternal (Peserta dari Luar)</option>
                            </select>
                            @error('jenis_kegiatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Nama Kegiatan --}}
                            <div>
                                <label for="judul_rencana" class="block text-sm font-medium text-gray-700">
                                    Nama Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="judul_rencana" id="judul_rencana" value="{{ old('judul_rencana') }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('judul_rencana') border-red-500 @enderror"
                                    placeholder="Contoh: Workshop Pengimbasan...">
                                @error('judul_rencana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Sumber Pembiayaan --}}
                            <div>
                                <label for="sumber_pembiayaan" class="block text-sm font-medium text-gray-700">
                                    Sumber Pembiayaan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="sumber_pembiayaan" id="sumber_pembiayaan" value="{{ old('sumber_pembiayaan', 'DIPA BBPMP Prov. Sumatera Barat Tahun ' . date('Y')) }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('sumber_pembiayaan') border-red-500 @enderror">
                                @error('sumber_pembiayaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="tanggal_mulai_rencana" class="block text-sm font-medium text-gray-700">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai_rencana" id="tanggal_mulai_rencana" value="{{ old('tanggal_mulai_rencana') }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_mulai_rencana') border-red-500 @enderror">
                                @error('tanggal_mulai_rencana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="tanggal_selesai_rencana" class="block text-sm font-medium text-gray-700">
                                    Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_selesai_rencana" id="tanggal_selesai_rencana" value="{{ old('tanggal_selesai_rencana') }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_selesai_rencana') border-red-500 @enderror">
                                @error('tanggal_selesai_rencana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tempat Kegiatan --}}
                        <div>
                            <label for="tempat_kegiatan" class="block text-sm font-medium text-gray-700">
                                Tempat Kegiatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tempat_kegiatan" id="tempat_kegiatan" value="{{ old('tempat_kegiatan') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tempat_kegiatan') border-red-500 @enderror"
                                placeholder="Contoh: Aula BBPMP Prov. Sumbar atau Hotel Axana...">
                            @error('tempat_kegiatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="deskripsi_rencana" class="block text-sm font-medium text-gray-700">
                                Deskripsi / Latar Belakang
                            </label>
                            <textarea name="deskripsi_rencana" id="deskripsi_rencana" rows="3" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('deskripsi_rencana') border-red-500 @enderror"
                                placeholder="Jelaskan dasar pelaksanaan kegiatan...">{{ old('deskripsi_rencana') }}</textarea>
                            @error('deskripsi_rencana')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jumlah Peserta --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="jumlah_peserta" class="block text-sm font-medium text-gray-700">
                                    Jumlah Peserta <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="jumlah_peserta" id="jumlah_peserta" value="{{ old('jumlah_peserta') }}" min="1" step="1"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('jumlah_peserta') border-red-500 @enderror"
                                    placeholder="Contoh: 40">
                                @error('jumlah_peserta')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Verifikasi Dokumen --}}
                        <div class="border-t pt-4" x-show="jenisKegiatan === 'eksternal'" x-cloak>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <label class="flex items-start gap-3">
                                    <input type="checkbox" name="butuh_verifikasi_dokumen" value="1"
                                        {{ old('butuh_verifikasi_dokumen') ? 'checked' : '' }}
                                        class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        id="toggle_verifikasi"
                                        x-model="butuhVerifikasi">
                                    <span>
                                        <span class="block text-sm font-medium text-gray-700">Aktifkan verifikasi dokumen persyaratan peserta</span>
                                        <span class="block text-xs text-gray-500 mt-1">Peserta harus mengupload dan memverifikasi dokumen sebelum dapat mengikuti Absensi, Tugas, dan Sertifikat.</span>
                                    </span>
                                </label>

                                <div id="dokumen_options" class="mt-4 ml-7" x-show="butuhVerifikasi && jenisKegiatan === 'eksternal'" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen yang Wajib Diupload:</label>
                                    <div class="space-y-2" x-show="docTypes.length">
                                        <template x-for="(jenis, index) in docTypes" :key="jenis">
                                            <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2 bg-white">
                                                <div class="flex items-center">
                                                    <input type="hidden" name="jenis_dokumen_wajib[]" :value="jenis" :disabled="!butuhVerifikasi || jenisKegiatan === 'internal'">
                                                    <span class="text-sm text-gray-700" x-text="jenis"></span>
                                                </div>
                                                <button type="button" class="text-xs text-red-600 hover:text-red-800" @click="removeDocType(index)">Hapus</button>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div class="space-y-2">
                                            <select x-model="predefinedDocType"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                                <option value="">Pilih dokumen umum</option>
                                                <template x-for="option in predefinedOptions" :key="option">
                                                    <option :value="option" x-text="option"></option>
                                                </template>
                                            </select>
                                            <button type="button" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium" @click="addPredefinedDocType()">
                                                Tambah dari daftar
                                            </button>
                                        </div>
                                        <div class="space-y-2">
                                            <input type="text" x-model="newDocType" placeholder="Contoh: Kartu Pegawai"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                @keydown.enter.prevent="addDocType()">
                                            <button type="button" class="w-full px-4 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition font-medium" @click="addDocType()">
                                                Tambah manual
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">Kolom kiri untuk dokumen dari daftar umum, kolom kanan untuk dokumen manual.</div>
                                    <p class="text-xs text-gray-500 mt-2">Tambahkan minimal 1 jenis dokumen yang harus diverifikasi oleh PIC/Panitia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Rancangan Anggaran Biaya (RAB) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-5 border-b border-gray-200 bg-primary-50 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-primary-800">2. Rancangan Anggaran Biaya (RAB)</h3>
                        <button type="button" @click="addAnggaran()" class="inline-flex items-center px-3 py-1 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Item
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="pb-2">
                            <table class="w-full table-fixed">
                                <colgroup>
                                    <col style="width: 5%;">
                                    <col style="width: 21%;">
                                    <col style="width: 7%;">
                                    <col style="width: 6%;">
                                    <col style="width: 7%;">
                                    <col style="width: 6%;">
                                    <col style="width: 11%;">
                                    <col style="width: 16%;">
                                    <col style="width: 21%;">
                                </colgroup>
                                <thead>
                                    <tr class="border-b-2 border-gray-200 bg-gray-50">
                                        <th class="px-2 py-2.5 text-center text-xs font-semibold text-gray-700 whitespace-nowrap">Nomor</th>
                                        <th class="px-2 py-2.5 pl-4 text-left text-xs font-semibold text-gray-700">Nama Item</th>
                                        <th class="px-2 py-2.5 text-center text-xs font-semibold text-gray-700 whitespace-nowrap">Volume 1</th>
                                        <th class="px-2 py-2.5 text-center text-xs font-semibold text-gray-700 whitespace-nowrap">Satuan 1</th>
                                        <th class="px-2 py-2.5 text-center text-xs font-semibold text-gray-700 whitespace-nowrap">Volume 2</th>
                                        <th class="px-2 py-2.5 text-center text-xs font-semibold text-gray-700 whitespace-nowrap">Satuan 2</th>
                                        <th class="px-2 py-2.5 text-right text-xs font-semibold text-gray-700">Harga Satuan</th>
                                        <th class="px-2 py-2.5 text-right text-xs font-semibold text-gray-700">Total Biaya</th>
                                        <th class="px-2 py-2.5 text-left text-xs font-semibold text-gray-700">Referensi SBM</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="(item, index) in anggaranItems" :key="index">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-2 py-2.5 text-sm text-gray-500 text-center align-top" x-text="index + 1"></td>
                                                <td class="px-2 py-2.5 pl-4 align-top">
                                                    <input type="text" x-model="item.nama_item" :name="'anggaran['+index+'][nama_item]'"
                                                    class="w-full min-w-0 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1"
                                                    placeholder="Nama item / manual">
                                            </td>
                                            <td class="px-2 py-2.5 align-top">
                                                <input type="number" x-model.number="item.volume_1" :name="'anggaran['+index+'][volume_1]'"
                                                    @input="calculateTotal(item)"
                                                    class="w-full min-w-[6ch] rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-right no-spin py-1 pr-1"
                                                    min="0" step="1" placeholder="0">
                                            </td>
                                            <td class="px-2 py-2.5 align-top">
                                                <input type="text" x-model="item.satuan_primary" :name="'anggaran['+index+'][satuan_primary]'"
                                                    :placeholder="item.satuan_primary || item.satuan_primary_suggestion || 'Sat'"
                                                    class="w-full min-w-[6ch] rounded border-gray-300 shadow-sm text-sm text-right py-1 pr-2 placeholder-gray-400">
                                            </td>
                                            <td class="px-2 py-2.5 align-top">
                                                <input type="number" x-model.number="item.volume_2" :name="'anggaran['+index+'][volume_2]'"
                                                    @input="calculateTotal(item)"
                                                    class="w-full min-w-[6ch] rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-right no-spin py-1 pr-1"
                                                    min="0" step="1" placeholder="0">
                                            </td>
                                            <td class="px-2 py-2.5 align-top">
                                                <input type="text" x-model="item.satuan_secondary" :name="'anggaran['+index+'][satuan_secondary]'"
                                                    :placeholder="item.satuan_secondary || item.satuan_secondary_suggestion || 'Sat'"
                                                    class="w-full min-w-[6ch] rounded border-gray-300 shadow-sm text-sm text-right py-1 pr-2 placeholder-gray-400">
                                            </td>
                                            <td class="px-2 py-2.5 align-top">
                                                <input type="number" x-model.number="item.harga_satuan" :name="'anggaran['+index+'][harga_satuan]'"
                                                    @input="calculateTotal(item)"
                                                    class="w-full min-w-0 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-right no-spin py-1"
                                                    min="0" placeholder="0">
                                                <input type="hidden" x-model="item.harga_satuan_sbm" :name="'anggaran['+index+'][harga_satuan_sbm]'">
                                            </td>
                                            <td class="px-2 py-2.5 text-right font-medium text-gray-900 align-top">
                                                <span x-text="formatRupiah(item.total_biaya)"></span>
                                                <input type="hidden" x-model="item.total_biaya" :name="'anggaran['+index+'][total_biaya]'">
                                                <input type="hidden" x-model="item.kategori" :name="'anggaran['+index+'][kategori]'">
                                            </td>
                                            <td class="px-2 py-2.5 align-top">
                                                <div class="flex items-center gap-1">
                                                    <select x-model="item.sbm_master_id" :name="'anggaran['+index+'][sbm_master_id]'"
                                                        @change="onSbmChange(item, $event)"
                                                        class="w-full max-w-[130px] min-w-0 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1">
                                                        <option value="">Pilih SBM atau Manual</option>
                                                        @php
                                                            foreach ($sbmMasters as $kategori => $items) {
                                                                echo "<optgroup label=\"$kategori\">";
                                                                foreach ($items as $sbm) {
                                                                    echo "<option value=\"{$sbm->id}\" 
                                                                        data-nama=\"{$sbm->nama_item}\"
                                                                        data-kategori=\"{$sbm->kategori}\"
                                                                        data-satuan-primary=\"{$sbm->satuan_primary}\"
                                                                        data-satuan-secondary=\"{$sbm->satuan_secondary}\"
                                                                        data-harga=\"{$sbm->harga_satuan}\">{$sbm->nama_item}</option>";
                                                                }
                                                                echo "</optgroup>";
                                                            }
                                                        @endphp
                                                    </select>
                                                    <button type="button" @click="removeAnggaran(index)" x-show="anggaranItems.length > 1"
                                                        class="shrink-0 text-red-500 hover:text-red-700 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Total Biaya Summary --}}
                        <div class="mt-4 border-t pt-4 flex justify-end">
                            <div class="text-right">
                                <p class="text-sm text-gray-600 mb-1">Total Anggaran:</p>
                                <p class="text-2xl font-bold text-primary-600" x-text="formatRupiah(grandTotal)"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Kebutuhan Fasilitas & Logistik --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-5 border-b border-gray-200 bg-primary-50 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-primary-800">3. Kebutuhan Fasilitas & Logistik</h3>
                        <span class="text-sm text-primary-600 bg-primary-100 px-3 py-1 rounded-full">Instruksi untuk Koordinator RT</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Daftar Permintaan Fasilitas --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Daftar Permintaan Fasilitas:</label>
                                <div class="space-y-2">
                                    <template x-for="(item, index) in fasilitasItems" :key="index">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500 w-6" x-text="index + 1"></span>
                                            <input type="number" x-model.number="item.jumlah" :name="'fasilitas['+index+'][jumlah]'" min="1"
                                                class="w-16 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-center">
                                            <select x-model="item.satuan"
                                                class="w-24 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                                <option value="Unit">Unit</option>
                                                <option value="Buah">Buah</option>
                                                <option value="Set">Set</option>
                                                <option value="Ruang">Ruang</option>
                                                <option value="Meter">Meter</option>
                                                <option value="Paket">Paket</option>
                                                <option value="Lembar">Lembar</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" x-model="item.satuanCustom" x-show="item.satuan === 'Lainnya'"
                                                class="w-20 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                                placeholder="Satuan...">
                                            <input type="hidden" :name="'fasilitas['+index+'][satuan]'" :value="item.satuan === 'Lainnya' ? item.satuanCustom : item.satuan">
                                            <input type="text" x-model="item.nama" :name="'fasilitas['+index+'][nama]'"
                                                class="flex-1 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                                placeholder="Nama fasilitas (misal: Mic Wireless)...">
                                            <button type="button" @click="removeFasilitas(index)" class="text-red-500 hover:text-red-700" x-show="fasilitasItems.length > 1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                            <button type="button" @click="addFasilitas()" class="text-primary-500 hover:text-primary-700" x-show="index === fasilitasItems.length - 1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Quick Fasilitas Suggestions --}}
                                <div class="mt-4">
                                    <p class="text-xs text-gray-500 mb-2">Saran Cepat:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" @click="addFasilitasSuggestion('Aula Utama', 'Ruang')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">+ Aula Utama</button>
                                        <button type="button" @click="addFasilitasSuggestion('Proyektor', 'Unit')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">+ Proyektor</button>
                                        <button type="button" @click="addFasilitasSuggestion('Layar', 'Unit')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">+ Layar</button>
                                        <button type="button" @click="addFasilitasSuggestion('Mic Wireless', 'Unit')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">+ Mic Wireless</button>
                                        <button type="button" @click="addFasilitasSuggestion('Sound System Portable', 'Set')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">+ Sound System</button>
                                        <button type="button" @click="addFasilitasSuggestion('Kabel Roll Panjang', 'Buah')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">+ Kabel Roll</button>
                                    </div>
                                </div>

                                {{-- Items yang diajukan --}}
                                <div class="mt-4" x-show="fasilitasItems.filter(f => f.nama).length > 0">
                                    <p class="text-xs text-gray-500 mb-2">Item yang diajukan:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(item, index) in fasilitasItems.filter(f => f.nama)" :key="index">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                <span x-text="item.jumlah" class="font-bold mr-1"></span>
                                                <span x-text="item.satuan === 'Lainnya' ? item.satuanCustom : item.satuan" class="mr-1"></span>
                                                <span x-text="item.nama"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Instruksi / Catatan Khusus --}}
                            <div>
                                <label for="catatan_logistik" class="block text-sm font-medium text-gray-700 mb-3">Instruksi Penataan / Catatan Khusus:</label>
                                <textarea name="catatan_logistik" id="catatan_logistik" rows="8" 
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    placeholder="Contoh:&#10;1. Mohon setup meja bentuk U-Shape di Aula Utama.&#10;2. Snack pagi disajikan jam 10.00 di meja luar ruangan.&#10;3. Pastikan AC dinyalakan 1 jam sebelum acara dimulai.">{{ old('catatan_logistik') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Alur --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-5 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-semibold text-blue-800">Alur Persetujuan Pengajuan:</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs font-medium">Draft</span>
                                    <span>→</span>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">Diajukan</span>
                                    <span>→</span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">Review Kepala</span>
                                    <span>→</span>
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs font-medium">Review PPK</span>
                                    <span>→</span>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Disetujui</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-between bg-white rounded-lg shadow-sm p-4">
                    <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Batal
                    </a>
                    <div class="flex items-center space-x-3">
                        {{-- Simpan Draft --}}
                        <button type="submit" name="save_draft" value="1" class="inline-flex items-center px-5 py-2.5 bg-gray-200 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan Draft
                        </button>
                        {{-- Submit --}}
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Ajukan Telaah Staf
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
