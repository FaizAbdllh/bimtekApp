{{-- Dashboard Peserta Eksternal --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Bimtek Diikuti --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Bimtek Diikuti</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $bimtekDiikuti ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bimtek Selesai --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Bimtek Selesai</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $bimtekSelesai ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sertifikat Diperoleh --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Sertifikat</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $sertifikatDiperoleh ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bimtek Aktif --}}
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-6">
    <div class="p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Bimtek Aktif</h3>
        @if(isset($bimtekAktif) && $bimtekAktif->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($bimtekAktif as $bimtek)
                    
                    {{-- 💡 MENCARI STATUS VERIFIKASI PESERTA PADA KELAS INI --}}
                    @php
                        $pivot = \DB::table('bimtek_pesertas')
                            ->where('bimtek_id', $bimtek->id)
                            ->where('user_id', auth()->id())
                            ->first();
                        
                        $statusVerif = $pivot ? $pivot->status_verifikasi : 'verified';
                        
                        // Jika bimtek tidak butuh dokumen, otomatis buka gembok
                        if(!$bimtek->butuh_verifikasi_dokumen) {
                            $statusVerif = 'verified';
                        }
                    @endphp

                    <div class="border border-gray-200 rounded-lg p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-gray-900 leading-tight pr-4">{{ $bimtek->judul_final ?? $bimtek->judul_rencana }}</h4>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                    @if($bimtek->status == 'berlangsung') bg-green-100 text-green-800
                                    @else bg-primary-100 text-primary-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $bimtek->status)) }}
                                </span>
                            </div>

                            {{-- 💡 GEMBOK VISUAL: Menampilkan Label Peringatan Jika Belum Sah --}}
                            @if($statusVerif === 'pending')
                                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-100 rounded-lg flex items-start gap-3">
                                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm text-yellow-800">Pendaftaran berhasil! Saat ini panitia sedang meninjau keabsahan berkas Anda.</p>
                                </div>
                            @elseif($statusVerif === 'rejected')
                                <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-lg flex items-start gap-3">
                                    <svg class="w-4 h-4 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-red-800 mb-0.5">Berkas Ditolak!</p>
                                        <p class="text-sm text-red-700">Terdapat kesalahan pada berkas unggahan Anda. Silakan unggah perbaikan.</p>
                                    </div>
                                </div>
                            @endif

                            <p class="text-sm text-gray-500 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $bimtek->tanggal_mulai_aktual ? \Carbon\Carbon::parse($bimtek->tanggal_mulai_aktual)->format('d M Y') : ($bimtek->tanggal_mulai_rencana ? \Carbon\Carbon::parse($bimtek->tanggal_mulai_rencana)->format('d M Y') : '-') }}
                            </p>
                            <p class="text-sm text-gray-500 mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="truncate">{{ $bimtek->lokasi_aktual ?? $bimtek->tempat_kegiatan_rencana ?? 'Lokasi belum ditentukan' }}</span>
                            </p>
                        </div>
                        
                        {{-- 💡 GEMBOK VISUAL: Aksi (Call to Action) --}}
                        <div class="pt-3 border-t border-gray-200 flex justify-end items-center">
                            @if($statusVerif === 'verified')
                                <a href="{{ route('bimtek.show', $bimtek->id) }}" class="inline-flex items-center text-primary-600 hover:text-primary-800 text-sm font-medium transition-colors">
                                    Masuk Kelas 
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            @elseif($statusVerif === 'rejected')
                                <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Revisi Berkas
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-gray-400 text-sm font-medium cursor-not-allowed" title="Menunggu persetujuan panitia">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Kelas Terkunci
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="mt-2 text-gray-500">Anda belum terdaftar di bimtek manapun.</p>
                <p class="text-sm text-gray-500">Hubungi panitia BBPMP untuk mendapatkan link pendaftaran.</p>
            </div>
        @endif
    </div>
</div>

