<x-app-layout>
    <x-slot name="header">
        Kelola Sertifikat Kelulusan
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('bimtek.index') }}" class="text-gray-500 hover:text-primary-600 text-sm font-medium transition-colors">Bimtek</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{-- REFAKTORISASI: Menjamin kestabilan UUID dan menyematkan fallback judul rencana --}}
                            <a href="{{ route('bimtek.show', $bimtek->id) }}" class="ml-1 text-gray-500 hover:text-primary-600 text-sm font-medium transition-colors">{{ Str::limit($bimtek->judul_final ?? $bimtek->judul_rencana, 30) }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700 font-medium">Sertifikat</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Tombol Navigasi Kembali --}}
            <div class="mb-6">
                {{-- REFAKTORISASI: Kestabilan ID rute kembali --}}
                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Kelas
                </a>
            </div>

            {{-- Informasi Kegiatan --}}
            {{-- REFAKTORISASI: Fallback judul rencana usulan --}}
            <div class="mb-6 p-3 bg-primary-50 border border-primary-100 text-primary-800 text-sm rounded-lg">
                <span class="font-semibold">Kegiatan:</span> {{ $bimtek->judul_final ?? $bimtek->judul_rencana }}
            </div>

            {{-- Informasi Aturan Kriteria Kelulusan Peserta --}}
            <div class="mb-6 p-3 bg-primary-50 border border-primary-100 text-primary-800 rounded-lg text-sm">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 shrink-0 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">Kriteria Kelulusan Mandatori:</span>
                    </div>
                    <span>Presensi Kehadiran Kelas &ge; {{ $bimtek->syarat_kehadiran_persen ?? 80 }}%</span>
                    @if($bimtek->syarat_tugas_wajib)
                        <span class="text-primary-300">|</span>
                        <span>Seluruh Modul Tugas Wajib Terkumpul</span>
                        <span class="text-primary-300">|</span>
                        <span>Rata-rata Nilai Tugas Kelompok &ge; {{ $bimtek->syarat_tugas_persen ?? 80 }} Poin</span>
                    @endif
                </div>
            </div>

            {{-- Jaring Pengaman Proteksi Kelulusan Administrasi Berkas Peserta --}}
            @if(!$isVerified)
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <h3 class="mt-2 text-base font-bold text-gray-800">Akses Dokumen Terkunci</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                                Mohon maaf, Anda wajib menyelesaikan proses unggah dokumen penugasan resmi (Surat Tugas) serta menunggu verifikasi sah panitia untuk dapat mengakses panel sertifikat ini.
                            </p>
                        </div>
                    </div>
                </div>
            @else
            
            {{-- Quick Statistics Widgets --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Anggota Kelas</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ count($eligibilityData) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Memenuhi Syarat</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ collect($eligibilityData)->where('eligible', true)->count() }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sertifikat Terbit</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ $bimtek->sertifikats->count() }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Antrean Penerbitan</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ collect($eligibilityData)->where('eligible', true)->where('has_sertifikat', false)->count() }}</p>
                </div>
            </div>

            {{-- PANEL INTERAKSI KHUSUS AKTOR PESERTA (My Certificate Row Card) --}}
            @if($isPeserta && $userSertifikat)
                <div class="mb-6 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 shrink-0">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-bold text-gray-800">Sertifikat Kelulusan Anda Tersedia</h3>
                                    <p class="mt-1 text-sm text-gray-500">No Registrasi: <span class="text-gray-700 font-semibold">{{ $userSertifikat->nomor_sertifikat }}</span></p>
                                    <p class="text-sm text-gray-500">Tanggal Terbit Dokumen: {{ $userSertifikat->tanggal_terbit->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 shrink-0">
                                {{-- REFAKTORISASI: Kestabilan ID parameter rute preview & download --}}
                                <a href="{{ route('bimtek.sertifikat.preview', [$bimtek->id, $userSertifikat->id]) }}" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    Pratinjau
                                </a>
                                <a href="{{ route('bimtek.sertifikat.download', [$bimtek->id, $userSertifikat->id]) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    Unduh Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($isPeserta && !$userSertifikat)
                <div class="mb-6 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800">Sertifikat Belum Diterbitkan</h3>
                                <p class="mt-1 text-sm text-gray-500 leading-relaxed">Lembar sertifikat kelulusan digital Anda belum dirilis oleh operator pokja. Pastikan akumulasi rasio absensi kehadiran tatap muka dan pengerjaan tugas pengayaan Anda telah memenuhi ambang batas kelayakan minimum instansi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- MEJA GENERATE MASSAL SERTIFIKAT (Khusus Operator Panitia / PIC Pokja) --}}
            @if($canManage && $bimtek->has_sertifikat)
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                        <h3 class="text-lg font-bold text-primary-800">Panel Penerbitan Dokumen Massal</h3>
                        <p class="mt-1 text-sm text-gray-500">Saring dan pilih baris nama peserta yang layak lulus untuk dilakukan pembentukan file PDF otomatis.</p>
                    </div>

                    {{-- REFAKTORISASI FORM GENERATE: Kestabilan UUID parameter rute generate --}}
                    <form action="{{ route('bimtek.sertifikat.generate', $bimtek->id) }}" method="POST" id="generate-form">
                        @csrf
                        <div class="p-6 space-y-5">
                            {{-- Input Tanggal Terbit Sertifikat --}}
                            <div class="max-w-xs">
                                <label for="tanggal_terbit" class="block text-sm font-semibold text-gray-700">
                                    Tanggal Cetak Terbit Dokumen <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_terbit" id="tanggal_terbit" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" required>
                            </div>

                            {{-- Opsi Pilihan Global Select All --}}
                            <div class="flex items-center gap-2 pt-4 border-t border-gray-200">
                                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-gray-700">
                                    <input type="checkbox" id="select-all-eligible" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <span>Pilih Semua yang Memenuhi Syarat Kelulusan</span>
                                </label>
                                <span id="selected-count" class="text-sm text-gray-500">(0 Anggota Terpilih)</span>
                            </div>
                        </div>

                        {{-- Tabel Kurasi Kelayakan Kelulusan Peserta --}}
                        <div class="overflow-x-auto border-t border-gray-200">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-14">Pilih</th>
                                        <th class="px-6 py-3 text-left">Nama Lengkap Anggota</th>
                                        <th class="px-6 py-3 text-center">Rasio Kehadiran</th>
                                        <th class="px-6 py-3 text-center">Rasio Penilaian Tugas</th>
                                        <th class="px-6 py-3 text-center">Status Kelayakan</th>
                                        <th class="px-6 py-3 text-center pr-6 w-44">Status Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                                    @forelse($eligibilityData as $pesertaId => $data)
                                        <tr class="hover:bg-gray-100 transition-colors">
                                            <td class="px-6 py-4 text-center align-middle">
                                                @if($data['eligible'] && !$data['has_sertifikat'])
                                                    <input type="checkbox" name="peserta_ids[]" value="{{ $pesertaId }}" class="peserta-checkbox rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 cursor-pointer">
                                                @else
                                                    <input type="checkbox" disabled class="rounded border-gray-200 text-gray-300 bg-gray-50 cursor-not-allowed">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-full bg-primary-100 text-primary-800 flex items-center justify-center font-semibold text-xs shrink-0">
                                                        {{ strtoupper(substr($data['peserta']->name ?? 'P', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-900 block">{{ $data['peserta']->name ?? '-' }}</span>
                                                        <span class="text-sm text-gray-500 block mt-0.5">{{ $data['peserta']->email ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                <span class="text-sm font-semibold block {{ $data['lulus_kehadiran'] ? 'text-green-700' : 'text-red-600' }}">
                                                    {{ $data['persentase_kehadiran'] }}%
                                                </span>
                                                <span class="text-xs text-gray-500 mt-0.5 block">Sesi: {{ $data['hadir_count'] }}/{{ $data['total_sesi'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if($bimtek->syarat_tugas_wajib && $data['total_tugas'] > 0)
                                                    <span class="text-sm font-semibold block {{ $data['lulus_nilai_tugas'] ? 'text-green-700' : 'text-red-600' }}">
                                                        {{ $data['rata_rata_nilai_tugas'] !== null ? $data['rata_rata_nilai_tugas'] . ' Poin' : '-' }}
                                                    </span>
                                                    <span class="text-xs mt-0.5 block {{ $data['lulus_kelengkapan_tugas'] ? 'text-green-700' : 'text-red-600' }}">
                                                        Terkumpul: {{ $data['tugas_terkumpul'] }}/{{ $data['total_tugas'] }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-500">Bebas Tugas</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap align-middle">
                                                @if($data['eligible'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Lulus Seleksi</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Tidak Layak</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center pr-6 whitespace-nowrap align-middle">
                                                @if($data['has_sertifikat'])
                                                    <div class="flex items-center justify-center gap-1">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800">Terbit Sah</span>
                                                        {{-- REFAKTORISASI: Kestabilan ID parameter rute download & destroy dokumen terbit --}}
                                                        <a href="{{ route('bimtek.sertifikat.download', [$bimtek->id, $data['sertifikat']->user_id ?? $data['sertifikat']]) }}" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Unduh Berkas">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('bimtek.sertifikat.destroy', [$bimtek->id, $data['sertifikat']->user_id ?? $data['sertifikat']]) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin membatalkan dan menghapus lembar sertifikat resmi peserta ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Lembar">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">Belum Diproses</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <p class="mt-2 text-gray-500">Belum ada database data peserta terdaftar dalam bimbingan teknis ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Panel Aksi Bawah Eksekusi Massal --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                            <p class="text-sm text-gray-500">Beri centang pada kolom peserta untuk memicu fungsi cetak file.</p>
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" id="generate-btn" disabled>
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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