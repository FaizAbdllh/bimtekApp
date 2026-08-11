<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-400 font-medium">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600 transition-colors">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    {{-- REFAKTORISASI: Menjamin kestabilan UUID dan menyematkan fallback judul rencana --}}
                    <li><a href="{{ route('bimtek.show', $bimtek->id) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-800 font-bold">Sertifikat</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Kelola Sertifikat Kelulusan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header dengan Tombol Aksi --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    {{-- REFAKTORISASI: Kestabilan ID rute kembali --}}
                    <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold text-xs uppercase tracking-wide transition shadow-sm w-fit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Kelas
                    </a>
                    <div class="mt-3 text-sm">
                        {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
                        <p class="text-gray-900 font-semibold">Kegiatan: {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</p>
                    </div>
                </div>
            </div>

            {{-- Informasi Aturan Kriteria Kelulusan Peserta --}}
            <div class="mb-6 p-4 bg-blue-50 border border-blue-100 text-blue-800 rounded-2xl text-xs font-semibold shadow-inner shadow-blue-50">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div class="flex items-center gap-1.5 text-blue-900">
                        <svg class="w-4 h-4 shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span class="uppercase tracking-wide text-[10px]">Kriteria Kelulusan Mandatori:</span>
                    </div>
                    <span>Presensi Kehadiran Kelas &ge; {{ $bimtek->syarat_kehadiran_persen ?? 80 }}%</span>
                    @if($bimtek->syarat_tugas_wajib)
                        <span class="text-blue-300">|</span>
                        <span>Seluruh Modul Tugas Wajib Terkumpul</span>
                        <span class="text-blue-300">|</span>
                        <span>Rata-rata Nilai Tugas Kelompok &ge; {{ $bimtek->syarat_tugas_persen ?? 80 }} Poin</span>
                    @endif
                </div>
            </div>

            {{-- Jaring Pengaman Proteksi Kelulusan Administrasi Berkas Peserta --}}
            @if(!$isVerified)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-16 bg-gray-50 rounded-full mb-4 border border-gray-100 text-gray-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Akses Dokumen Terkunci</h3>
                    <p class="text-xs text-gray-400 max-w-sm mx-auto font-medium leading-relaxed">
                        Mohon maaf, Anda wajib menyelesaikan proses unggah dokumen penugasan resmi (Surat Tugas) serta menunggu verifikasi sah panitia untuk dapat mengakses panel sertifikat ini.
                    </p>
                </div>
            @else
            
            {{-- Quick Statistics Widgets --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota Kelas</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($eligibilityData) }}</p>
                </div>
                <div class="bg-green-50/60 rounded-2xl shadow-sm border border-green-100 p-5">
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Memenuhi Syarat</p>
                    <p class="text-2xl font-bold text-green-700 mt-1">{{ collect($eligibilityData)->where('eligible', true)->count() }}</p>
                </div>
                <div class="bg-blue-50/60 rounded-2xl shadow-sm border border-blue-100 p-5">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Sertifikat Terbit</p>
                    <p class="text-2xl font-bold text-blue-700 mt-1">{{ $bimtek->sertifikats->count() }}</p>
                </div>
                <div class="bg-yellow-50/60 rounded-2xl shadow-sm border border-yellow-100 p-5">
                    <p class="text-xs font-bold text-yellow-600 uppercase tracking-wider">Antrean Penerbitan</p>
                    <p class="text-2xl font-bold text-yellow-700 mt-1">{{ collect($eligibilityData)->where('eligible', true)->where('has_sertifikat', false)->count() }}</p>
                </div>
            </div>

            {{-- PANEL INTERAKSI KHUSUS AKTOR PESERTA (My Certificate Row Card) --}}
            @if($isPeserta && $userSertifikat)
                <div class="mb-6 bg-white rounded-2xl shadow-sm border border-emerald-200 overflow-hidden shadow-emerald-50">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100/60 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                                    </svg>
                                </div>
                                <div class="text-sm">
                                    <h3 class="text-base font-bold text-gray-900">Sertifikat Kelulusan Anda Tersedia</h3>
                                    <p class="text-xs text-gray-400 font-medium mt-0.5">No Registrasi: <span class="text-gray-700 font-semibold">{{ $userSertifikat->nomor_sertifikat }}</span></p>
                                    <p class="text-[11px] text-gray-400 font-medium mt-0.5">Tanggal Terbit Dokumen: {{ $userSertifikat->tanggal_terbit->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide shrink-0">
                                {{-- REFAKTORISASI: Kestabilan ID parameter rute preview & download --}}
                                <a href="{{ route('bimtek.sertifikat.preview', [$bimtek->id, $userSertifikat->id]) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-2 bg-white border border-gray-200 text-purple-600 rounded-xl hover:bg-gray-50 transition shadow-sm">
                                    Pratinjau
                                </a>
                                <a href="{{ route('bimtek.sertifikat.download', [$bimtek->id, $userSertifikat->id]) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-sm">
                                    Unduh Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($isPeserta && !$userSertifikat)
                <div class="mb-6 bg-white rounded-2xl shadow-sm border border-amber-200 p-5 shadow-amber-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl border border-amber-100 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-sm leading-relaxed">
                            <h3 class="text-base font-bold text-gray-900">Sertifikat Belum Diterbitkan</h3>
                            <p class="text-xs text-gray-400 font-medium mt-0.5">Lembar sertifikat kelulusan digital Anda belum dirilis oleh operator pokja. Pastikan akumulasi rasio absensi kehadiran tatap muka dan pengerjaan tugas pengayaan Anda telah memenuhi ambang batas kelayakan minimum instansi.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- MEJA GENERATE MASSAL SERTIFIKAT (Khusus Operator Panitia / PIC Pokja) --}}
            @if($canManage && $bimtek->has_sertifikat)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Panel Penerbitan Dokumen Massal</h3>
                        <p class="text-xs text-gray-400 font-semibold mt-0.5">Saring dan pilih baris nama peserta yang layak lulus untuk dilakukan pembentukan file PDF otomatis.</p>
                    </div>

                    {{-- REFAKTORISASI FORM GENERATE: Kestabilan UUID parameter rute generate --}}
                    <form action="{{ route('bimtek.sertifikat.generate', $bimtek->id) }}" method="POST" id="generate-form">
                        @csrf
                        <div class="p-6 space-y-4">
                            {{-- Input Tanggal Terbit Sertifikat --}}
                            <div class="max-w-xs">
                                <label for="tanggal_terbit" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Tanggal Cetak Terbit Dokumen <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_terbit" id="tanggal_terbit" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            </div>

                            {{-- Opsi Pilihan Global Select All --}}
                            <div class="flex items-center gap-2 pt-2 border-t border-gray-50">
                                <label class="inline-flex items-center gap-2 cursor-pointer font-semibold text-xs text-gray-600 uppercase tracking-wide">
                                    <input type="checkbox" id="select-all-eligible" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                    <span>Pilih Semua yang Memenuhi Syarat Kelulusan</span>
                                </label>
                                <span id="selected-count" class="text-xs font-bold text-gray-400">(0 Anggota Terpilih)</span>
                            </div>
                        </div>

                        {{-- Tabel Kurasi Kelayakan Kelulusan Peserta --}}
                        <div class="overflow-x-auto border-t border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-14">Pilih</th>
                                        <th class="px-6 py-3 text-left">Nama Lengkap Anggota</th>
                                        <th class="px-6 py-3 text-center">Rasio Kehadiran</th>
                                        <th class="px-6 py-3 text-center">Rasio Penilaian Tugas</th>
                                        <th class="px-6 py-3 text-center">Status Kelayakan</th>
                                        <th class="px-6 py-3 text-center pr-6 w-44">Status Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                    @forelse($eligibilityData as $pesertaId => $data)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4 text-center align-middle">
                                                @if($data['eligible'] && !$data['has_sertifikat'])
                                                    <input type="checkbox" name="peserta_ids[]" value="{{ $pesertaId }}" class="peserta-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 cursor-pointer">
                                                @else
                                                    <input type="checkbox" disabled class="w-4 h-4 text-gray-200 border-gray-200 rounded cursor-not-allowed bg-gray-50">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-full bg-gray-50 border border-gray-200 text-gray-500 flex items-center justify-center font-bold text-xs shrink-0">
                                                        {{ strtoupper(substr($data['peserta']->name ?? 'P', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <span class="text-sm font-bold text-gray-900 block">{{ $data['peserta']->name ?? '-' }}</span>
                                                        <span class="text-[10px] text-gray-400 font-medium block mt-0.5">{{ $data['peserta']->email ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                <span class="text-sm font-bold block {{ $data['lulus_kehadiran'] ? 'text-green-600' : 'text-red-500' }}">
                                                    {{ $data['persentase_kehadiran'] }}%
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-semibold mt-0.5 block">Sesi: {{ $data['hadir_count'] }}/{{ $data['total_sesi'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if($bimtek->syarat_tugas_wajib && $data['total_tugas'] > 0)
                                                    <span class="text-sm font-bold block {{ $data['lulus_nilai_tugas'] ? 'text-green-600' : 'text-red-500' }}">
                                                        {{ $data['rata_rata_nilai_tugas'] !== null ? $data['rata_rata_nilai_tugas'] . ' Poin' : '-' }}
                                                    </span>
                                                    <span class="text-[10px] font-semibold block mt-0.5 {{ $data['lulus_kelengkapan_tugas'] ? 'text-green-600/80' : 'text-red-500/80' }}">
                                                        Terkumpul: {{ $data['tugas_terkumpul'] }}/{{ $data['total_tugas'] }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-400 font-medium">Bebas Tugas</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap align-middle">
                                                @if($data['eligible'])
                                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-green-50 border border-green-200 text-green-700">Lulus Seleksi</span>
                                                @else
                                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-50 border border-red-100 text-red-600">Tidak Layak</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center pr-6 whitespace-nowrap align-middle">
                                                @if($data['has_sertifikat'])
                                                    <div class="flex items-center justify-center gap-1.5 font-bold text-xs">
                                                        <span class="px-2 py-0.5 bg-blue-50 border border-blue-100 text-blue-700 font-bold rounded text-[10px] uppercase tracking-wide">Terbit Sah</span>
                                                        {{-- REFAKTORISASI: Kestabilan ID parameter rute download & destroy dokumen terbit --}}
                                                        <a href="{{ route('bimtek.sertifikat.download', [$bimtek->id, $data['sertifikat']->user_id ?? $data['sertifikat']]) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition" title="Unduh Berkas">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('bimtek.sertifikat.destroy', [$bimtek->id, $data['sertifikat']->user_id ?? $data['sertifikat']]) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin membatalkan dan menghapus lembar sertifikat resmi peserta ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-lg transition" title="Hapus Lembar">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400 font-medium italic">Belum Diproses</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                Belum ada database data peserta terdaftar dalam bimbingan teknis ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Panel Aksi Bawah Eksekusi Massal --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 rounded-b-2xl shadow-inner shadow-gray-50">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Beri centang pada kolom peserta untuk memicu fungsi cetak file.</p>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold text-xs uppercase tracking-wide transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm" id="generate-btn" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                                </svg>
                                Terbitkan Sertifikat Seleksi
                            </button>
                        </div>
                    </form>
                </div>
            @endif
            
            @endif
        </div>
    </div>

    {{-- Script Handler Manajemen Seleksi Massal Checkbox Kertas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all-eligible');
            const pesertaCheckboxes = document.querySelectorAll('.peserta-checkbox');
            const selectedCount = document.getElementById('selected-count');
            const generateBtn = document.getElementById('generate-btn');

            function updateCount() {
                const checked = document.querySelectorAll('.peserta-checkbox:checked').length;
                selectedCount.textContent = `(${checked} Anggota Terpilih)`;
                if(generateBtn) {
                    generateBtn.disabled = checked === 0;
                }
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    pesertaCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateCount();
                });
            }

            pesertaCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateCount();
                    const allChecked = document.querySelectorAll('.peserta-checkbox:checked').length === pesertaCheckboxes.length;
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });

            updateCount();
        });
    </script>
</x-app-layout>