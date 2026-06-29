<x-app-layout>
    <x-slot name="header">
        Formulir Telaah Staf (Pengajuan Kegiatan)
    </x-slot>

    @push('head-scripts')
    <style>
        /* Sembunyikan tombol spinner up/down bawaan browser pada input volume biaya */
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
                butuhVerifikasi: @json((bool) old('butuh_verifikasi_dokumen', false)),
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
                        
                        const suggestedPrimary = selectedOption.dataset.satuanPrimary || '';
                        const ImageOfSuggestedSecondary = selectedOption.dataset.satuanSecondary || '';
                        item.satuan_primary = suggestedPrimary;
                        item.satuan_secondary = ImageOfSuggestedSecondary;
                        
                        item.satuan_primary_suggestion = '';
                        item.satuan_secondary_suggestion = '';
                        item.harga_satuan = parseFloat(selectedOption.dataset.harga) || 0;
                        item.harga_satuan_sbm = parseFloat(selectedOption.dataset.harga) || 0;
                        this.calculateTotal(item);
                    } else {
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
                    const vol2 = Number(item.volume_2) || 1; // Default 1 jika tidak diisi agar perkalian manual aman
                    const harga = Number(item.harga_satuan) || 0;
                    item.total_biaya = vol1 * (vol2 > 0 ? vol2 : 1) * harga;
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
                    if (!value) return;

                    const exists = this.docTypes.some((item) => item.toLowerCase() === value.toLowerCase());
                    if (!exists) {
                        this.docTypes.push(value);
                    }
                    this.newDocType = '';
                },

                addPredefinedDocType() {
                    const value = this.predefinedDocType.trim();
                    if (!value) return;

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
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('pengajuan.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium">
                            Pengajuan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-medium">Pengajuan Baru</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <form action="{{ route('pengajuan.store') }}" method="POST" id="pengajuanForm">
                @csrf

                {{-- Section 1: Informasi Umum Kegiatan --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                    <div class="p-5 border-b border-gray-200 bg-primary-50">
                        <h3 class="text-lg font-bold text-primary-800">1. Informasi Umum Perencanaan</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Jenis Kegiatan --}}
                            <div>
                                <label for="jenis_kegiatan" class="block text-sm font-semibold text-gray-700">
                                    Jenis Sasaran Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_kegiatan" id="jenis_kegiatan" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('jenis_kegiatan') border-red-500 @enderror text-sm"
                                    x-model="jenisKegiatan"
                                    @change="syncVerifikasiByJenis()">
                                    <option value="">Pilih Jenis Kegiatan</option>
                                    <option value="internal">Internal (Peserta dari lingkungan BBPMP)</option>
                                    <option value="eksternal">Eksternal (Peserta dari Dinas/Sekolah Luar)</option>
                                </select>
                                @error('jenis_kegiatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Mode Pelaksanaan --}}
                            <div>
                                <label for="mode_pelaksanaan" class="block text-sm font-semibold text-gray-700">
                                    Mode Pelaksanaan <span class="text-red-500">*</span>
                                </label>
                                <select name="mode_pelaksanaan" id="mode_pelaksanaan" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('mode_pelaksanaan') border-red-500 @enderror text-sm">
                                    <option value="">Pilih Mode Pelaksanaan</option>
                                    <option value="offline" {{ old('mode_pelaksanaan') == 'offline' ? 'selected' : '' }}>Offline (Tatap Muka Fisik)</option>
                                    <option value="online" {{ old('mode_pelaksanaan') == 'online' ? 'selected' : '' }}>Online (Virtual Rapat)</option>
                                    <option value="hybrid" {{ old('mode_pelaksanaan') == 'hybrid' ? 'selected' : '' }}>Hybrid (Gabungan Campuran)</option>
                                </select>
                                @error('mode_pelaksanaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Nama Kegiatan --}}
                            <div>
                                <label for="judul_rencana" class="block text-sm font-semibold text-gray-700">
                                    Nama Kegiatan (Rencana) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="judul_rencana" id="judul_rencana" value="{{ old('judul_rencana') }}" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('judul_rencana') border-red-500 @enderror text-sm"
                                    placeholder="Contoh: Workshop Penjaminan Mutu Pendidikan Pasca Evaluasi...">
                                @error('judul_rencana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Sumber Pembiayaan --}}
                            <div>
                                <label for="sumber_pembiayaan" class="block text-sm font-semibold text-gray-700">
                                    Sumber Pembiayaan Sesuai Pagu <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="sumber_pembiayaan" id="sumber_pembiayaan" value="{{ old('sumber_pembiayaan', 'DIPA BBPMP Prov. Sumatera Barat Tahun ' . date('Y')) }}" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('sumber_pembiayaan') border-red-500 @enderror text-sm">
                                @error('sumber_pembiayaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Periode Tanggal --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="tanggal_mulai_rencana" class="block text-sm font-semibold text-gray-700">
                                    Rencana Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai_rencana" id="tanggal_mulai_rencana" value="{{ old('tanggal_mulai_rencana') }}" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_mulai_rencana') border-red-500 @enderror text-sm">
                                @error('tanggal_mulai_rencana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="tanggal_selesai_rencana" class="block text-sm font-semibold text-gray-700">
                                    Rencana Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_selesai_rencana" id="tanggal_selesai_rencana" value="{{ old('tanggal_selesai_rencana') }}" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tanggal_selesai_rencana') border-red-500 @enderror text-sm">
                                @error('tanggal_selesai_rencana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- REFAKTORISASI LOKASI: Mengubah id, name, dan old dari tempat_kegiatan menjadi tempat_kegiatan_rencana --}}
                        <div>
                            <label for="tempat_kegiatan_rencana" class="block text-sm font-semibold text-gray-700">
                                Rencana Tempat Kegiatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tempat_kegiatan_rencana" id="tempat_kegiatan_rencana" value="{{ old('tempat_kegiatan_rencana') }}" required
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('tempat_kegiatan_rencana') border-red-500 @enderror text-sm"
                                placeholder="Contoh: Aula Utama BBPMP Prov. Sumbar atau Hotel Axana Padang...">
                            @error('tempat_kegiatan_rencana')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi Pokok Pikiran --}}
                        <div>
                            <label for="deskripsi_rencana" class="block text-sm font-semibold text-gray-700">
                                Latar Belakang / Deskripsi Singkat Kegiatan
                            </label>
                            <textarea name="deskripsi_rencana" id="deskripsi_rencana" rows="3" 
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('deskripsi_rencana') border-red-500 @enderror text-sm"
                                placeholder="Jelaskan pokok urgensi pemikiran dilaksanakannya kegiatan ini...">{{ old('deskripsi_rencana') }}</textarea>
                            @error('deskripsi_rencana')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estimasi Target Jumlah Peserta --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="jumlah_peserta" class="block text-sm font-semibold text-gray-700">
                                    Estimasi Target Jumlah Peserta <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="jumlah_peserta" id="jumlah_peserta" value="{{ old('jumlah_peserta') }}" min="1" step="1" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('jumlah_peserta') border-red-500 @enderror text-sm"
                                    placeholder="Contoh: 40">
                                @error('jumlah_peserta')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Opsi Aturan Kelulusan Verifikasi Berkas Dokumen --}}
                        <div class="border-t border-gray-100 pt-4" x-show="jenisKegiatan === 'eksternal'" x-cloak>
                            <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4">
                                <label class="flex items-start gap-3">
                                    <input type="checkbox" name="butuh_verifikasi_dokumen" value="1"
                                        {{ old('butuh_verifikasi_dokumen') ? 'checked' : '' }}
                                        class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        id="toggle_verifikasi"
                                        x-model="butuhVerifikasi">
                                    <span>
                                        <span class="block text-sm font-semibold text-amber-900">Aktifkan sistem verifikasi dokumen persyaratan berkas</span>
                                        <span class="block text-xs text-amber-700 mt-1">Jika dicentang, peserta luar wajib mengunggah Surat Tugas / SPPD sah untuk ditinjau panitia agar bisa mendapatkan sertifikat.</span>
                                    </span>
                                </label>

                                <div id="dokumen_options" class="mt-4 ml-7" x-show="butuhVerifikasi && jenisKegiatan === 'eksternal'" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Checklist Berkas Wajib:</label>
                                    <div class="space-y-2 mb-3" x-show="docTypes.length">
                                        <template x-for="(jenis, index) in docTypes" :key="jenis">
                                            <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 bg-white shadow-sm">
                                                <div class="flex items-center">
                                                    <input type="hidden" name="jenis_dokumen_wajib[]" :value="jenis" :disabled="!butuhVerifikasi || jenisKegiatan === 'internal'">
                                                    <span class="text-sm font-medium text-gray-700" x-text="jenis"></span>
                                                </div>
                                                <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 transition" @click="removeDocType(index)">Hapus</button>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div class="space-y-2">
                                            <select x-model="predefinedDocType"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                                <option value="">Pilih berkas umum</option>
                                                <template x-for="option in predefinedOptions" :key="option">
                                                    <option :value="option" x-text="option"></option>
                                                </template>
                                            </select>
                                            <button type="button" class="w-full px-4 py-2 bg-gray-700 border border-transparent rounded-xl text-white hover:bg-gray-800 text-xs font-bold transition" @click="addPredefinedDocType()">
                                                Tambah dari Daftar
                                            </button>
                                        </div>
                                        <div class="space-y-2">
                                            <input type="text" x-model="newDocType" placeholder="Contoh: Surat Rekomendasi BBPMP"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                                @keydown.enter.prevent="addDocType()">
                                            <button type="button" class="w-full px-4 py-2 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 text-xs font-bold transition" @click="addDocType()">
                                                Tambah Jenis Manual
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Rancangan Anggaran Biaya (RAB) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                    <div class="p-5 border-b border-gray-200 bg-primary-50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-primary-800">2. Rancangan Anggaran Biaya Belanja (RAB)</h3>
                        <button type="button" @click="addAnggaran()" class="inline-flex items-center px-3 py-1.5 text-xs font-bold bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Komponen
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm min-w-[900px]">
                                <thead>
                                    <tr class="border-b-2 border-gray-200 bg-gray-50 text-gray-500 font-semibold text-xs uppercase">
                                        <th class="px-2 py-3 text-center w-12">No</th>
                                        <th class="px-2 py-3 text-left">Nama Komponen Belanja</th>
                                        <th class="px-2 py-3 text-center w-20">Vol 1</th>
                                        <th class="px-2 py-3 text-center w-20">Satuan 1</th>
                                        <th class="px-2 py-3 text-center w-20">Vol 2</th>
                                        <th class="px-2 py-3 text-center w-20">Satuan 2</th>
                                        <th class="px-2 py-3 text-right w-32">Harga Satuan</th>
                                        <th class="px-2 py-3 text-right w-40">Total Biaya</th>
                                        <th class="px-2 py-3 text-left w-48">Referensi Master SBM</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, index) in anggaranItems" :key="index">
                                        <tr class="hover:bg-gray-50/40 transition">
                                            <td class="px-2 py-3 text-sm text-gray-400 text-center align-middle" x-text="index + 1"></td>
                                            <td class="px-2 py-3 align-middle">
                                                <input type="text" x-model="item.nama_item" :name="'anggaran['+index+'][nama_item]'" required
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1"
                                                    placeholder="Nama item belanja...">
                                            </td>
                                            <td class="px-2 py-3 align-middle">
                                                <input type="number" x-model.number="item.volume_1" :name="'anggaran['+index+'][volume_1]'" required
                                                    @input="calculateTotal(item)"
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-center no-spin py-1"
                                                    min="1" placeholder="0">
                                            </td>
                                            <td class="px-2 py-3 align-middle">
                                                <input type="text" x-model="item.satuan_primary" :name="'anggaran['+index+'][satuan_primary]'" required
                                                    :placeholder="item.satuan_primary_suggestion || 'Satuan'"
                                                    class="w-full rounded border-gray-300 shadow-sm text-sm text-center py-1">
                                            </td>
                                            <td class="px-2 py-3 align-middle">
                                                <input type="number" x-model.number="item.volume_2" :name="'anggaran['+index+'][volume_2]'"
                                                    @input="calculateTotal(item)"
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-center no-spin py-1"
                                                    min="0" placeholder="Opsi">
                                            </td>
                                            <td class="px-2 py-3 align-middle">
                                                <input type="text" x-model="item.satuan_secondary" :name="'anggaran['+index+'][satuan_secondary]'"
                                                    :placeholder="item.satuan_secondary_suggestion || 'Opsi'"
                                                    class="w-full rounded border-gray-300 shadow-sm text-sm text-center py-1">
                                            </td>
                                            <td class="px-2 py-3 align-middle">
                                                <input type="number" x-model.number="item.harga_satuan" :name="'anggaran['+index+'][harga_satuan]'" required
                                                    @input="calculateTotal(item)"
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-right no-spin py-1"
                                                    min="0" placeholder="0">
                                                <input type="hidden" x-model="item.harga_satuan_sbm" :name="'anggaran['+index+'][harga_satuan_sbm]'">
                                            </td>
                                            <td class="px-2 py-3 text-right font-bold text-gray-900 align-middle">
                                                <span x-text="formatRupiah(item.total_biaya)"></span>
                                                <input type="hidden" x-model="item.total_biaya" :name="'anggaran['+index+'][total_biaya]'">
                                                <input type="hidden" x-model="item.kategori" :name="'anggaran['+index+'][kategori]'">
                                            </td>
                                            <td class="px-2 py-3 align-middle">
                                                <div class="flex items-center gap-1">
                                                    <select x-model="item.sbm_master_id" :name="'anggaran['+index+'][sbm_master_id]'"
                                                        @change="onSbmChange(item, $event)"
                                                        class="w-full rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xs py-1">
                                                        <option value="">Pilih SBM (Otomatis)</option>
                                                        @foreach ($sbmMasters as $kategori => $items)
                                                            <optgroup label="{{ $kategori }}">
                                                                @foreach ($items as $sbm)
                                                                    <option value="{{ $sbm->id }}" 
                                                                        data-nama="{{ $sbm->nama_item }}"
                                                                        data-kategori="{{ $sbm->kategori }}"
                                                                        data-satuan-primary="{{ $sbm->satuan_primary }}"
                                                                        data-satuan-secondary="{{ $sbm->satuan_secondary }}"
                                                                        data-harga="{{ $sbm->harga_satuan }}">{{ $sbm->nama_item }} (Rp {{ number_format($sbm->harga_satuan, 0, ',', '.') }})</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" @click="removeAnggaran(index)" x-show="anggaranItems.length > 1"
                                                        class="text-red-500 hover:text-red-700 transition flex-shrink-0">
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
                        
                        {{-- Akumulasi Total Biaya --}}
                        <div class="mt-4 border-t border-gray-100 pt-4 flex justify-end">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-right min-w-[240px]">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Pagu RAB:</p>
                                <p class="text-2xl font-bold text-primary-600" x-text="formatRupiah(grandTotal)"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Kebutuhan Fasilitas Rumah Tangga --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                    <div class="p-5 border-b border-gray-200 bg-primary-50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-primary-800">3. Kebutuhan Sarana Fasilitas & Logistik</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Input Baris Fasilitas --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Daftar Logistik yang Diminta:</label>
                                <div class="space-y-2">
                                    <template x-for="(item, index) in fasilitasItems" :key="index">
                                        <div class="flex items-center space-x-2">
                                            <input type="number" x-model.number="item.jumlah" :name="'fasilitas['+index+'][jumlah]'" min="1" required
                                                class="w-16 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm text-center py-1">
                                            <select x-model="item.satuan"
                                                class="w-24 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1 bg-white">
                                                <option value="Unit">Unit</option>
                                                <option value="Buah">Buah</option>
                                                <option value="Set">Set</option>
                                                <option value="Ruang">Ruang</option>
                                                <option value="Paket">Paket</option>
                                                <option value="Lembar">Lembar</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" x-model="item.satuanCustom" x-show="item.satuan === 'Lainnya'" required
                                                class="w-20 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1"
                                                placeholder="Satuan...">
                                            <input type="hidden" :name="'fasilitas['+index+'][satuan]'" :value="item.satuan === 'Lainnya' ? item.satuanCustom : item.satuan">
                                            <input type="text" x-model="item.nama" :name="'fasilitas['+index+'][nama]'" required
                                                class="flex-1 rounded border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-1"
                                                placeholder="Nama fasilitas (misal: Aula Utama / Mic Wireless)...">
                                            <button type="button" @click="removeFasilitas(index)" class="text-red-500 hover:text-red-700 transition" x-show="fasilitasItems.length > 1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                            <button type="button" @click="addFasilitas()" class="text-primary-500 hover:text-primary-700 transition" x-show="index === fasilitasItems.length - 1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Rekomendasi Saran Klik --}}
                                <div class="mt-4">
                                    <p class="text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wider">Rekomendasi Umum RT:</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" @click="addFasilitasSuggestion('Aula Utama BBPMP', 'Ruang')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition">+ Aula Utama</button>
                                        <button type="button" @click="addFasilitasSuggestion('Proyektor HD & Layar', 'Unit')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition">+ Proyektor</button>
                                        <button type="button" @click="addFasilitasSuggestion('Mic Wireless UHF', 'Unit')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition">+ Mic Wireless</button>
                                        <button type="button" @click="addFasilitasSuggestion('Sound System Portable', 'Set')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition">+ Sound System</button>
                                        <button type="button" @click="addFasilitasSuggestion('Kabel Roll 10 Meter', 'Buah')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition">+ Kabel Roll</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Instruksi Tata Letak --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Catatan / Instruksi Khusus Penataan RT:</label>
                                <textarea name="catatan_logistik" id="catatan_logistik" rows="7" 
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    placeholder="Contoh:&#10;1. Mohon susun kursi dengan format U-Shape.&#10;2. Snack pagi disajikan jam 10.00 WIB tepat.&#10;3. Nyalakan pendingin ruangan AC 30 menit sebelum sesi dimulai.">{{ old('catatan_logistik') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Flow Info Progress --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-bold text-blue-900">Alur Kerja State Machine BPMN Perencanaan Kegiatan:</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs font-semibold uppercase">Draft PIC</span>
                                    <span>→</span>
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold uppercase">Diajukan</span>
                                    <span>→</span>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-semibold uppercase">Review Kepala</span>
                                    <span>→</span>
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs font-semibold uppercase">Review PPK</span>
                                    <span>→</span>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-semibold uppercase">Disetujui Final</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Panel Form Buttons --}}
                <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-900 transition">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Batal
                    </a>
                    <div class="flex items-center space-x-3">
                        {{-- Simpan Draft --}}
                        <button type="submit" name="save_draft" value="1" class="inline-flex items-center px-5 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-bold text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan Draft
                        </button>
                        {{-- Kirim Pengajuan --}}
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-primary-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Ajukan Usulan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>