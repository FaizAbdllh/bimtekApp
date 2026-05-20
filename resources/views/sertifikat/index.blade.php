<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('bimtek.index') }}" class="hover:text-primary-600">Bimtek</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li><a href="{{ route('bimtek.show', $bimtek) }}" class="hover:text-primary-600">{{ Str::limit($bimtek->judul_final, 30) }}</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-gray-900 font-medium">Sertifikat</li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">
                Kelola Sertifikat
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header with Actions --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('bimtek.show', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <div class="mt-2">
                        <p class="text-gray-600">{{ $bimtek->judul_final }}</p>
                    </div>
                </div>
            </div>

            {{-- Syarat Kelulusan Info --}}
            <div class="mb-6 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Syarat Kelulusan:</span>
                    <span>Kehadiran ≥ {{ $bimtek->syarat_kehadiran_persen ?? 80 }}%</span>
                    @if($bimtek->syarat_tugas_wajib)
                        <span class="mx-2">|</span>
                        <span>Semua tugas wajib terkumpul</span>
                        <span class="mx-2">|</span>
                        <span>Rata-rata nilai tugas ≥ {{ $bimtek->syarat_tugas_persen ?? 80 }}%</span>
                    @endif
                </div>
            </div>

            {{-- Statistics Cards --}}
            @if(!$isVerified)
                {{-- Locked State for Unverified Peserta --}}
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Terbatas</h3>
                    <p class="text-sm text-gray-600 max-w-md mx-auto">
                        Anda harus menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses sertifikat.
                    </p>
                </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Peserta</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($eligibilityData) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Memenuhi Syarat</p>
                            <p class="text-2xl font-bold text-green-600">{{ collect($eligibilityData)->where('eligible', true)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sudah Terbit</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $bimtek->sertifikats->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Belum Terbit</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ collect($eligibilityData)->where('eligible', true)->where('has_sertifikat', false)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Peserta View: My Certificate --}}
            @if($isPeserta && $userSertifikat)
                <div class="mb-6 bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            {{-- Info --}}
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Sertifikat Anda</h3>
                                    <p class="text-sm text-gray-500">No: {{ $userSertifikat->nomor_sertifikat }}</p>
                                    <p class="text-sm text-gray-500">Terbit: {{ $userSertifikat->tanggal_terbit->format('d F Y') }}</p>
                                </div>
                            </div>
                            {{-- Actions --}}
                            <div class="flex items-center gap-2 sm:flex-shrink-0">
                                <a href="{{ route('bimtek.sertifikat.preview', [$bimtek, $userSertifikat]) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Preview</span>
                                </a>
                                <a href="{{ route('bimtek.sertifikat.download', [$bimtek, $userSertifikat]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span class="hidden sm:inline">Download</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($isPeserta && !$userSertifikat)
                <div class="mb-6 bg-white rounded-xl shadow-sm border border-yellow-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Sertifikat Belum Tersedia</h3>
                            <p class="text-sm text-gray-500">Sertifikat Anda belum diterbitkan. Pastikan Anda memenuhi syarat kehadiran dan tugas.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Generate Sertifikat Form (For PIC/Panitia) --}}
            @if($canManage && $bimtek->has_sertifikat)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Generate Sertifikat</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih peserta yang memenuhi syarat untuk diterbitkan sertifikatnya</p>
                    </div>

                    <form action="{{ route('bimtek.sertifikat.generate', $bimtek) }}" method="POST" id="generate-form">
                        @csrf
                        <div class="p-6 space-y-4">
                            {{-- Info Placeholder --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">ℹ️ Informasi Template</h4>
                                <p class="text-sm text-blue-700 mb-2">
                                    Template sertifikat akan mengganti variabel berikut dengan data peserta:
                                </p>
                                <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                                    <div><code class="bg-blue-100 px-1 rounded">${NAMA_PESERTA}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${NIP}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${INSTANSI}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${NOMOR_SERTIFIKAT}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${JUDUL_BIMTEK}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${TANGGAL_MULAI}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${TANGGAL_SELESAI}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${LOKASI}</code></div>
                                    <div><code class="bg-blue-100 px-1 rounded">${TANGGAL_TERBIT}</code></div>
                                </div>
                            </div>

                            {{-- Tanggal Terbit --}}
                            <div class="mb-4">
                                <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Terbit <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_terbit" id="tanggal_terbit" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            </div>

                            {{-- Select All --}}
                            <div class="flex items-center gap-4 pt-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="select-all-eligible" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="text-sm font-medium text-gray-700">Pilih semua yang memenuhi syarat & belum punya sertifikat</span>
                                </label>
                                <span id="selected-count" class="text-sm text-gray-500">(0 dipilih)</span>
                            </div>
                        </div>

                        {{-- Peserta Table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">Pilih</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($eligibilityData as $pesertaId => $data)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($data['eligible'] && !$data['has_sertifikat'])
                                                    <input type="checkbox" name="peserta_ids[]" value="{{ $pesertaId }}" class="peserta-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                                @else
                                                    <input type="checkbox" disabled class="w-4 h-4 text-gray-300 border-gray-300 rounded cursor-not-allowed">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-primary-600">{{ strtoupper(substr($data['peserta']->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $data['peserta']->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $data['peserta']->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex flex-col items-center">
                                                    <span class="text-sm font-medium {{ $data['lulus_kehadiran'] ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $data['persentase_kehadiran'] }}%
                                                    </span>
                                                    <span class="text-xs text-gray-500">{{ $data['hadir_count'] }}/{{ $data['total_sesi'] }} sesi</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex flex-col items-center">
                                                    @if($bimtek->syarat_tugas_wajib && $data['total_tugas'] > 0)
                                                        <span class="text-sm font-medium {{ $data['lulus_nilai_tugas'] ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $data['rata_rata_nilai_tugas'] !== null ? $data['rata_rata_nilai_tugas'] . '%' : '-' }}
                                                        </span>
                                                        <span class="text-xs {{ $data['lulus_kelengkapan_tugas'] ? 'text-green-600' : 'text-red-600' }}">
                                                            Terkumpul {{ $data['tugas_terkumpul'] }}/{{ $data['total_tugas'] }} tugas
                                                        </span>
                                                        <span class="text-xs text-gray-500">
                                                            Dinilai {{ $data['tugas_dinilai'] }}/{{ $data['total_tugas'] }} tugas
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-500">Tidak disyaratkan</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($data['eligible'])
                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Memenuhi Syarat
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Tidak Memenuhi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($data['has_sertifikat'])
                                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Sudah Terbit
                                                        </span>
                                                        <div class="flex items-center gap-1">
                                                            <a href="{{ route('bimtek.sertifikat.download', [$bimtek, $data['sertifikat']]) }}" class="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition" title="Download">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('bimtek.sertifikat.destroy', [$bimtek, $data['sertifikat']]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus sertifikat ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded-lg transition" title="Hapus">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-500">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center">
                                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <p class="text-gray-500">Belum ada peserta di bimtek ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Form Actions --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 rounded-b-xl">
                            <p class="text-sm text-gray-500">Pilih peserta yang akan diterbitkan sertifikatnya</p>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm transition disabled:opacity-50 disabled:cursor-not-allowed" id="generate-btn" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Generate Sertifikat
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Sertifikat yang Sudah Terbit --}}
            @if($bimtek->sertifikats->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Sertifikat yang Sudah Terbit</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $bimtek->sertifikats->count() }} sertifikat telah diterbitkan</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Sertifikat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terbit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bimtek->sertifikats->sortBy('user.name') as $index => $sertifikat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-primary-600">{{ strtoupper(substr($sertifikat->user->name ?? 'U', 0, 1)) }}</span>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ $sertifikat->user->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $sertifikat->nomor_sertifikat }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sertifikat->tanggal_terbit->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('bimtek.sertifikat.preview', [$bimtek, $sertifikat]) }}" target="_blank" class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition" title="Preview">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('bimtek.sertifikat.download', [$bimtek, $sertifikat]) }}" class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition" title="Download">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                </a>
                                                @if($canManage)
                                                    <form action="{{ route('bimtek.sertifikat.destroy', [$bimtek, $sertifikat]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus sertifikat ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded-lg transition" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all-eligible');
            const pesertaCheckboxes = document.querySelectorAll('.peserta-checkbox');
            const selectedCount = document.getElementById('selected-count');
            const generateBtn = document.getElementById('generate-btn');

            function updateCount() {
                const checked = document.querySelectorAll('.peserta-checkbox:checked').length;
                selectedCount.textContent = `(${checked} dipilih)`;
                generateBtn.disabled = checked === 0;
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
                    // Update select all checkbox
                    const allChecked = document.querySelectorAll('.peserta-checkbox:checked').length === pesertaCheckboxes.length;
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });

            updateCount();
        });
    </script>
    @endif
</x-app-layout>
